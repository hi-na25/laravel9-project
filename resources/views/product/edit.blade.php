@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    商品情報編集 (ID: {{ $product->id }})
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary float-end">一覧に戻る</a>
                </div>

                <div class="card-body">
                    {{-- フォームの送信先を update メソッドへ、 method を POST にし、@method('PUT')で PUT/PATCH リクエストを偽装 --}}
                    <form action="{{ route('products.update', ['product' => $product->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') 
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">商品名 <span class="badge bg-danger">必須</span></label>
                            {{-- ★現在の値を value に設定 --}}
                            <input type="text" class="form-control" id="product_name" name="product_name" value="{{ $product->product_name }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company_id" class="form-label">メーカー</label>
                            <select class="form-control" id="company_id" name="company_id">
                              @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $product->company_id == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                              @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">価格 <span class="badge bg-danger">必須</span></label>
                            {{-- ★現在の値を value に設定 --}}
                            <input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stock" class="form-label">在庫数 <span class="badge bg-danger">必須</span></label>
                            {{-- ★現在の値を value に設定 --}}
                            <input type="number" class="form-control" id="stock" name="stock" value="{{ $product->stock }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">商品コメント</label>
                            {{-- ★現在の値を textarea の中身に設定 --}}
                            <textarea class="form-control" id="comment" name="comment" rows="3">{{ $product->comment }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image_path" class="form-label">商品画像 (既存の画像はここに表示されます)</label>
                       
                            @if ($product->img_path)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->img_path) }}" 
                                     alt="現在の画像" 
                                     style="max-width: 150px; height: auto;">
                            </div>
                        @else
                            <p>現在の画像はありません</p>
                        @endif     
                            <input class="form-control" type="file" id="img_path" name="img_path">
                        </div>

                        <button type="submit" class="btn btn-success">更新する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection