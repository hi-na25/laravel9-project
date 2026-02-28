<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale; 
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SalesController;

class SalesController extends Controller
{
    public function purchase(Request $request)
    {
        // データの整合性を保つためトランザクションを開始
        DB::beginTransaction();

        try {
            // リクエストから商品IDを取得
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            // ① 在庫が0の商品を購入しようとした場合、エラーとする
            if (!$product || $product->stock <= 0) {
                return response()->json(['error' => '在庫がありません。'], 400);
            }

            // ② salesテーブルにレコードを追加する
            $sale = new Sale();
            $sale->product_id = $productId;
            $sale->save();

            // ③ productsテーブルの在庫数を減算する
            $product->stock -= 1;
            $product->save();

            // 全ての処理が成功したら確定
            DB::commit();

            return response()->json(['success' => '購入が完了しました。']);

        } catch (\Exception $e) {
            // どこかでエラーが発生した場合は全ての処理をキャンセル
            DB::rollBack();
            return response()->json(['error' => '購入処理に失敗しました。'], 500);
        }
    }
}
