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
                        <a href="{{ route('item.edit', ['item' => $item->id]) }}" class="btn btn-sm btn-success me-2">編集</a>

                        <a href="{{ route('item.index') }}" class="btn btn-sm btn-secondary">一覧に戻る</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th>ID</th>
                            <td>{{ $item->id }}</td>
                        </tr>
                        <tr>
                            <th>商品名</th>
                            <td>{{ $item->name }}</td>
                        </tr>
                        <tr class="align-middle">
                           <th>商品画像</th>
                           <td>
                               @if ($item->image_path)
                                   <img src="{{ asset('storage/' . $item->image_path) }}" 
                                        alt="{{ $item->name }}の画像" 
                                        style="max-width: 300px; height: auto;">
                               @else
                                   （画像なし）
                               @endif
                          </td>
                       </tr>
                        <tr>
                            <th>メーカー</th>
                            <td>{{ $item->maker }}</td>
                        </tr>
                        <tr>
                            <th>価格</th>
                            <td>¥{{ number_format($item->price) }}</td>
                        </tr>
                        <tr>
                            <th>在庫数</th>
                            <td>{{ $item->stock }}</td>
                        </tr>
                        <tr>
                            <th>コメント</th>
                            <td>{{ $item->comment }}</td>
                        </tr>
                    </table>

                    {{-- 編集・削除ボタンは後ほど実装します --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection