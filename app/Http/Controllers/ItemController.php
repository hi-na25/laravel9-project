<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        // 1. 商品モデル（Item.php）を使って、itemsテーブルの全データを取得
        $items = Item::all();

        // Itemモデルのクエリビルダーを開始
        $query = Item::query();

       // 商品名 (name) で検索
       if ($request->name) {
           $query->where('name', 'like', '%' . $request->name . '%');
        }

       // メーカー (maker) で検索
       if ($request->maker) {
           $query->where('maker', 'like', '%' . $request->maker . '%');
        }
    
       // 絞り込み後のデータを取得
       $items = $query->get();

        // 2. 取得した商品データ ($items) を一覧画面 ('item.index') に渡して表示
        return view('item.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('item.create'); // 呼び出すView名を指定
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
        'name' => 'required|max:100', // 必須、最大100文字
        'price' => 'required|integer', // 必須、整数
        'stock' => 'required|integer', // 必須、整数
        'maker' => 'max:50', // 最大50文字
        'comment' => 'nullable', // 入力任意
        'image_path' => 'nullable|image|max:2048', // 任意、画像ファイル、最大2MB
    ]);

    $image_path = null;
    if ($request->hasFile('image_path')) {
        // public/items ディレクトリに画像を保存し、保存先パスを取得
        $image_path = $request->file('image_path')->store('public/items');
    }

    // 2. データの保存処理（画像のアップロード処理はここでは省略し、後に実装します）
    
    // 商品モデルをインスタンス化
    $item = new Item();
    
    // リクエストからデータを受け取り、モデルにセット
    $item->name = $request->name;
    $item->maker = $request->maker;
    $item->price = $request->price;
    $item->stock = $request->stock;
    $item->comment = $request->comment;
    $item->image_path = str_replace('public/', '', $image_path);
    
    // データをデータベースに保存
    $item->save();
    
    // 3. 処理完了後、商品一覧画面へリダイレクト
    return redirect()->route('item.index')->with('success', '商品「' . $item->name . '」を登録しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item) {
        // $item には、ルーティングによって自動的にIDに対応する商品モデルが入っている
        return view('item.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item) {
        // show() メソッドと同様に、自動的にIDに対応する商品モデルが$itemに入る
        return view('item.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item) {
        // 1. バリデーション（新規登録時と同じ）
        $request->validate([
           'name' => 'required|max:100', 
           'price' => 'required|integer', 
           'stock' => 'required|integer', 
           'maker' => 'max:50', 
           'comment' => 'nullable', 
           'image_path' => 'nullable|image|max:2048', 
    ]);

    // ★画像ファイルの処理ロジックの追加
    if ($request->hasFile('image_path')) {
        // 既存の画像ファイルがあれば削除
        if ($item->image_path) {
            Storage::delete('public/' .$item->image_path);
        }
        // 新しい画像を保存
       $path = $request->file('image_path')->store('public/items');
       
       // DBに保存するために 'public/' のプレフィックスを削除
       $filename = str_replace('public/', '', $path);

       // 新しいパスをモデルにセット
       $item->image_path = $filename;
    }

        // 2. データの更新処理
    
        // $item には、編集対象の商品モデルが自動的に入っている
    
        // リクエストからデータを受け取り、モデルにセット
        $item->name = $request->name;
        $item->maker = $request->maker;
        $item->price = $request->price;
        $item->stock = $request->stock;
        $item->comment = $request->comment;
    
        // データをデータベースに保存（更新）
        $item->save();
    
        // 3. 処理完了後、商品一覧画面へリダイレクト
        return redirect()->route('item.index')->with('success', '商品「' . $item->name . '」の情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item) {
        // $item には削除対象の商品モデルが自動的に入っている
    
        $item_name = $item->name; // 削除前に商品名を保存
    
        if ($item->image_path) {
        // サーバー上のファイルを削除
        Storage::delete('public/' . $item->image_path);
        }

        // データをデータベースから削除
        $item->delete();
    
        // 処理完了後、商品一覧画面へリダイレクト
        return redirect()->route('item.index')->with('success', '商品「' . $item_name . '」を削除しました。');
    }
}
