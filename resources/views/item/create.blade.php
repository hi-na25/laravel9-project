@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    商品新規登録
                    <a href="{{ route('item.index') }}" class="btn btn-sm btn-secondary float-end">一覧に戻る</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('item.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">商品名 <span class="badge bg-danger">必須</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="maker" class="form-label">メーカー</label>
                            <input type="text" class="form-control" id="maker" name="maker">
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">価格 <span class="badge bg-danger">必須</span></label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stock" class="form-label">在庫数 <span class="badge bg-danger">必須</span></label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">商品コメント</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image_path" class="form-label">商品画像</label>
                            <input class="form-control" type="file" id="image_path" name="image_path">
                        </div>

                        <button type="submit" class="btn btn-primary">登録する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection