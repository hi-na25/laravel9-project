<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // 商品名 (最大255文字)
            $table->string('maker', 100)->nullable();// メーカー (最大100文字)
            $table->integer('price'); // 価格 (整数)
            $table->integer('stock'); // 在庫数 (整数)
            $table->text('comment')->nullable(); // コメント (長文、null許容)
            $table->string('image_path', 255)->nullable(); // 画像パス (null許容)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
