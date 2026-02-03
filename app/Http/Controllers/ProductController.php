<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $companies = Company::all();

        // 1. 商品モデル（product.php）を使って、productsテーブルの全データを取得

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
    public function store(ProductRequest $request)
    {
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

    public function show(Product $product)
    {
        return view('product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $companies = Company::all();
        return view('product.edit', compact('product', 'companies'));
    }

    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);

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

            return redirect()->route('products.index')->with('success', '商品を更新しました。');

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product_name = $product->product_name;

            if ($product->img_path) {
                \Storage::delete('public/' . $product->img_path);
            }

            $product->delete();

            return redirect()->route('products.index')
                ->with('success', '商品「' . $product_name . '」を削除しました。');

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return back()->withErrors(['error' => '削除に失敗しました。']);
        }
    }
}


