<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use Illuminate\Http\Request;

class productController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        // 1. 商品モデル（product.php）を使って、productsテーブルの全データを取得
        $products = product::all();

        // productモデルのクエリビルダーを開始
        $query = product::query();

       // 商品名 (name) で検索
       if ($request->keyword) {
           $query->where('product_name', 'like', '%' . $request->keyword . '%');
        }

       // メーカー (maker) で検索
       if ($request->company_id) {
           $query->whereHas('company', function($q) use ($request) {
               $q->where('company_name', 'like', '%' . $request->company_id . '%');
           });
        }
    
       // 絞り込み後のデータを取得
       $products = $query->get();

        // 2. 取得した商品データ ($products) を一覧画面 ('product.index') に渡して表示
        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        // 会社（メーカー）の一覧をデータベースから取得する
        $companies = Company::all();

        // 'product.create' 画面に、メーカー一覧（$companies）を渡して表示する
        return view('product.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
    // 1. バリデーション（入力チェック）
    $request->validate([
        'product_name' => 'required|max:100',
        'price' => 'required|integer',
        'stock' => 'required|integer',
        'company_id' => 'required|exists:companies,id', // maker は削除してこれだけにします
        'comment' => 'nullable',
        'img_path' => 'nullable|image|max:2048',
    ]);

    $image_path = null;
    if ($request->hasFile('image_path')) {
        // public/products ディレクトリに画像を保存し、保存先パスを取得
        $image_path = $request->file('image_path')->store('public/products');
    }

    // 2. データの保存処理（画像のアップロード処理はここでは省略し、後に実装します）
    
    // 商品モデルをインスタンス化
    $product = new product();
    
    // リクエストからデータを受け取り、モデルにセット
    $product->product_name = $request->product_name;
    $product->price = $request->price;
    $product->stock = $request->stock;
    $product->company_id = $request->company_id;
    $product->comment = $request->comment;
    $product->img_path = $image_path;
    
    // データをデータベースに保存
    $product->save();
    
    // 3. 処理完了後、商品一覧画面へリダイレクト
    return redirect()->route('products.index')->with('success', '商品を登録しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(product $product) {
        // $product には、ルーティングによって自動的にIDに対応する商品モデルが入っている
        return view('product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product) {

        $companies = Company::all();

        // show() メソッドと同様に、自動的にIDに対応する商品モデルが$productに入る
        return view('product.edit', compact('product', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, product $product) {
        // 1. バリデーション（新規登録時と同じ）
        $request->validate([
           'product_name' => 'required|max:100', 
           'price' => 'required|integer', 
           'stock' => 'required|integer', 
           'company_id' => 'required|integer', 
           'comment' => 'nullable', 
           'img_path' => 'nullable|image|max:2048', 
    ]);

    // ★画像ファイルの処理ロジックの追加
    if ($request->hasFile('image_path')) {
        // 既存の画像ファイルがあれば削除
        if ($product->image_path) {
            Storage::delete('public/' .$product->image_path);
        }
        // 新しい画像を保存
       $path = $request->file('image_path')->store('public/products');
       
       // DBに保存するために 'public/' のプレフィックスを削除
       $filename = str_replace('public/', '', $path);

       // 新しいパスをモデルにセット
       $product->img_path = $filename;
    }

        // 2. データの更新処理
    
        // $product には、編集対象の商品モデルが自動的に入っている
    
        // リクエストからデータを受け取り、モデルにセット
        $product->product_name = $request->product_name;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->company_id = $request->company_id;
        $product->comment = $request->comment;
    
        // データをデータベースに保存（更新）
        $product->save();
    
        // 3. 処理完了後、商品一覧画面へリダイレクト
        return redirect()->route('products.index')->with('success', '商品「' . $product->name . '」の情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(product $product) {
        // $product には削除対象の商品モデルが自動的に入っている
    
        $product_name = $product->name; // 削除前に商品名を保存
    
        if ($product->image_path) {
        // サーバー上のファイルを削除
        Storage::delete('public/' . $product->image_path);
        }

        // データをデータベースから削除
        $product->delete();
    
        // 処理完了後、商品一覧画面へリダイレクト
        return redirect()->route('product.index')->with('success', '商品「' . $product_name . '」を削除しました。');
    }

}
