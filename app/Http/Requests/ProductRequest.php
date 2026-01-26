<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
    return true; // 🌟必ず false から true に変更してください
 }

public function rules() {
    return [
        'product_name' => 'required|max:100',
        'price' => 'required|integer',
        'stock' => 'required|integer',
        'company_id' => 'required|exists:companies,id',
        'comment' => 'nullable',
        'img_path' => 'nullable|image|max:2048',
    ];
 }


public function messages() {
    return [
        'product_name.required' => '商品名は必須項目です。',
        'product_name.max' => '商品名は100文字以内で入力してください。',
        'price.required' => '価格は必須項目です。',
        'price.integer' => '価格は数値で入力してください。',
        'stock.required' => '在庫数は必須項目です。',
        'stock.integer' => '在庫数は数値で入力してください。',
        'company_id.required' => 'メーカーを選択してください。',
     ];
 }
}