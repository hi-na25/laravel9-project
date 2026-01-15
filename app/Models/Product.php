<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // 設計図のカラム名に合わせて、一括保存を許可する項目を指定
    protected $fillable = [
        'company_id',
        'product_name',
        'price',
        'stock',
        'comment',
        'img_path',
    ];

    // メーカーとの紐付け設定（1対多のリレーション）
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
