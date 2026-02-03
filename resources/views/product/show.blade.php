@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    商品詳細
                    <div class="float-end">
                        {{-- ★編集ボタンを追加 --}}
                        <a href="{{ route('products.edit', ['product' => $product->id]) }}" class="btn btn-sm btn-success me-2">編集</a>

                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">一覧に戻る</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th>ID</th>
                            <td>{{ $product->id }}</td>
                        </tr>
                        <tr>
                            <th>商品名</th>
                            <td>{{ $product->product_name }}</td>
                        </tr>
                        <tr class="align-middle">
                           <th>商品画像</th>
                           <td>
                               @if ($product->img_path)
                                   <img src="{{ asset('storage/' . $product->img_path) }}" 
                                        alt="{{ $product->name }}の画像" 
                                        style="max-width: 300px; height: auto;">
                               @else
                                   （画像なし）
                               @endif
                          </td>
                       </tr>
                        <tr>
                            <th>メーカー</th>
                            <td>{{ $product->company->company_name }}</td>
                        </tr>
                        <tr>
                            <th>価格</th>
                            <td>¥{{ number_format($product->price) }}</td>
                        </tr>
                        <tr>
                            <th>在庫数</th>
                            <td>{{ $product->stock }}</td>
                        </tr>
                        <tr>
                            <th>コメント</th>
                            <td>{{ $product->comment }}</td>
                        </tr>
                    </table>

                    {{-- 編集・削除ボタンは後ほど実装します --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection