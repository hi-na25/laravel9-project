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
    public function authorize()
{
    return true; // 🌟必ず false から true に変更してください
}

public function rules()
{
    return [
        'product_name' => 'required|max:100',
        'price' => 'required|integer',
        'stock' => 'required|integer',
        'company_id' => 'required|exists:companies,id',
        'comment' => 'nullable',
        'img_path' => 'nullable|image|max:2048',
    ];
}
}
