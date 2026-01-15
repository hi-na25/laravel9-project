@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    商品情報一覧
                    <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary float-end">新規登録</a>
                </div>

                <div class="card-body">
                    {{-- 成功メッセージの表示 --}}
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>   
                    @endif

                    <form method="GET" action="{{ route('products.index') }}" class="mb-4">
                        <div class="row">
                            {{-- 商品名検索 --}}
                            <div class="col-md-4">
                                <label for="keyword" class="form-label">商品名</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" 
                                       value="{{ request('keyword') }}" placeholder="商品名を入力">
                            </div>

                            {{-- メーカー名検索（一時的にテキストボックス） --}}
                            <div class="col-md-4">
                                <label for="company_id" class="form-label">メーカー名</label>
                                <input type="text" class="form-control" id="company_id" name="company_id" 
                                       value="{{ request('company_id') }}" placeholder="メーカー名を入力">
                            </div>

                            {{-- 検索ボタン --}}
                            <div class="col-md-4 d-flex align-products-end">
                                <button type="submit" class="btn btn-info text-white me-2">検索</button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">リセット</a>
                            </div>
                        </div>
                    </form>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>商品名</th>
                                <th>メーカー</th>
                                <th>画像</th>
                                <th>価格</th>
                                <th>在庫数</th>
                                <th>コメント</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->company->company_name }}</td>
                                    {{-- ★画像表示用の<td>を追加。ファイルパスがある場合のみ画像を仮表示 --}}
                                    <td>
                                        @if($product->img_path)
                                            {{-- storage フォルダの中身を見に行くように asset を使います --}}
                                            <img src="{{ asset('storage/' . $product->img_path) }}" style="width: 100px; height: auto;">
                                       @else
                                            <span>画像なし</span>
                                       @endif
                                    </td>
                                    <td>¥{{ number_format($product->price) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>{{ Str::limit($product->comment, 30) }}</td>
                                    <td>
                                        <a href="{{ route('products.show', ['product' => $product->id]) }}" class="btn btn-sm btn-info me-2">詳細</a>
                                        <a href="{{ route('products.edit', ['product' => $product->id]) }}" class="btn btn-sm btn-success">編集</a>
                                        {{-- ★削除ボタン用のフォームを追加 --}}
                                        <form action="{{ route('products.destroy', ['product' => $product->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection