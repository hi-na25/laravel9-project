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

        $companies = Company::all();

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
           $query->where('company_id', $request->company_id);
        }
    
       // 絞り込み後のデータを取得
       $products = $query->get();

        // 2. 取得した商品データ ($products) を一覧画面 ('product.index') に渡して表示
        return view('product.index', compact('products', 'companies'));
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

    try {
        $img_path = null;
        if ($request->hasFile('img_path')) {
            $path = $request->file('img_path')->store('public/products');
            $img_path = str_replace('public/', '', $path);
        }

        $product = new Product();
        $product->product_name = $request->product_name;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->company_id = $request->company_id;
        $product->comment = $request->comment;
        $product->img_path = $img_path;

        $product->save();

        return redirect()->route('products.index')->with('success', '商品を登録しました。');

    } catch (\Exception $e) {
        \Log::error($e->getMessage());
        return back()->withInput()->withErrors(['error' => '登録に失敗しました。']);
    }
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
    public function update(Request $request, $id) 
    {
        // 1. バリデーション (指摘3)
        $request->validate([
            'product_name' => 'required|max:100',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'company_id' => 'required|exists:companies,id',
            'comment' => 'nullable',
            'img_path' => 'nullable|image|max:2048',
        ]);

        // 2. try-catch文 (指摘4)
        try {
            $product = Product::find($id);

            // 3. 画像の更新処理 (指摘1)
            if ($request->hasFile('img_path')) { 
                $path = $request->file('img_path')->store('public/products');
                $product->img_path = str_replace('public/', '', $path);
            }

            $product->product_name = $request->product_name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->company_id = $request->company_id;
            $product->comment = $request->comment;

            $product->save();

            return redirect()->route('products.index');

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }
    public function destroy(Product $product)
    {
        // 指摘4: try-catch文を追加
        try {
            // 削除前に商品名を保存（メッセージ用）
            $product_name = $product->product_name;

            // 画像があればサーバーから削除
            if ($product->img_path) {
                \Storage::delete('public/' . $product->img_path);
            }

            // データベースから削除
            $product->delete();

            return redirect()->route('products.index')
                ->with('success', '商品「' . $product_name . '」を削除しました。');

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return back()->withErrors(['error' => '削除に失敗しました。']);
        }
    }
}


