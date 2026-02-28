<?php

use Illuminate\Http\Request;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

// 購入処理APIのルート定義
Route::post('/purchase', [SalesController::class, 'purchase']);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
