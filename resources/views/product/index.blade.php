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

                    <form id="search-form" method="GET" action="{{ route('products.index') }}" class="mb-4">
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
                               <select name="company_id" class="form-select" id="company_id">
                                   <option value="">メーカー名を選択</option>
                                   @foreach($companies as $company)
                                       <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                          {{ $company->company_name }}
                                   </option>
                                   @endforeach
                              </select>
                           </div>

                           <div class="col-md-4 mb-3">
                               <label class="form-label">価格</label>
                               <div class="d-flex align-items-center">
                                   <input type="number" name="min_price" class="form-control" placeholder="下限" value="{{ request('min_price') }}">
                                   <span class="mx-2">〜</span>
                                   <input type="number" name="max_price" class="form-control" placeholder="上限" value="{{ request('max_price') }}">
                               </div>
                          </div>

                          <div class="col-md-4 mb-3">
                              <label class="form-label">在庫数</label>
                              <div class="d-flex align-items-center">
                                  <input type="number" name="min_stock" class="form-control" placeholder="下限" value="{{ request('min_stock') }}">
                                  <span class="mx-2">〜</span>
                                  <input type="number" name="max_stock" class="form-control" placeholder="上限" value="{{ request('max_stock') }}">
                              </div>
                          </div>

                            {{-- 検索ボタン --}}
                            <div class="col-md-4 d-flex align-products-end">
                                <button id="search-btn" type="submit" class="btn btn-info text-white me-2">検索</button>
                            </div>
                        </div>
                    </form>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="sort" data-sort="id" style="cursor:pointer;">ID</th>
                                <th class="sort" data-sort="product_name" style="cursor:pointer;">商品名</th>
                                <th>メーカー</th> <th>画像</th>
                                <th class="sort" data-sort="price" style="cursor:pointer;">価格</th>
                                <th class="sort" data-sort="stock" style="cursor:pointer;">在庫数</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody id="product-table">
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->product_name }}</td>
                                    <td>{{ $product->company->company_name }}</td>
                                    {{-- ★画像表示用の<td>を追加。ファイルパスがある場合のみ画像を仮表示 --}}
                                    <td>
                                        @if($product->img_path)
                                            {{-- storage フォルダの中身を見に行くように asset を使います --}}
                                            <img src="{{ asset('storage/' . str_replace('public/', '', $product->img_path)) }}" style="width: 100px; height: auto;">
                                       @else
                                            <span>画像なし</span>
                                       @endif
                                    </td>
                                    <td>¥{{ number_format($product->price) }}</td>
                                    <td>{{ $product->stock }}</td>                                    <td>
                                        <a href="{{ route('products.show', ['product' => $product->id]) }}" class="btn btn-sm btn-info me-2">詳細</a>
                                        {{-- ★削除ボタン用のフォームを追加 --}}
                                        <form action="{{ route('products.destroy', ['product' => $product->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger delete-btn" data-product_id="{{ $product->id }}">削除</button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // 検索ボタンが押された時の処理
    $('#search-btn').on('click', function(e) {
        e.preventDefault(); // 本来の「ページ再読み込み」を止める！

        let formData = $('#search-form').serialize(); // フォームの入力内容をひとまとめにする

        $.ajax({
            url: "{{ route('products.index') }}", // 送り先は一覧画面のルート
            type: 'GET',
            data: formData,
            dataType: 'html', // サーバーからは「HTML」を返してもらう
        })
        .done(function(data) {
            // 通信成功：表の中身（tbody）だけを書き換える！
            let newTable = $(data).find('#product-table').html();
            $('#product-table').html(newTable);
        })
        .fail(function() {
            alert('検索に失敗しました。');
        });
    });
});
</script>

<script>
$(function() {
    // 現在のソート状態を保持する変数（初期表示はID降順）
    let currentSort = 'id';
    let currentDirection = 'desc';

    // 共通のAjax送信関数（検索とソート、どちらでもこれを使います）
    function fetchProducts() {
        let formData = $('#search-form').serialize();
        // ソート情報も合体させる！
        formData += '&sort=' + currentSort + '&direction=' + currentDirection;

        $.ajax({
            url: "{{ route('products.index') }}",
            type: 'GET',
            data: formData,
            dataType: 'html',
        })
        .done(function(data) {
            let newTable = $(data).find('#product-table').html();
            $('#product-table').html(newTable);
        })
        .fail(function() {
            alert('通信に失敗しました。');
        });
    }

    // 検索ボタンが押された時
    $('#search-btn').on('click', function(e) {
        e.preventDefault();
        fetchProducts();
    });

    // ヘッダー（.sort）がクリックされた時
    $('.sort').on('click', function() {
        let clickedSort = $(this).data('sort');

        // 同じ項目なら反転、違う項目なら昇順にする
        if (currentSort === clickedSort) {
            currentDirection = (currentDirection === 'asc') ? 'desc' : 'asc';
        } else {
            currentSort = clickedSort;
            currentDirection = 'asc';
        }

        fetchProducts();
    });
});

    // 削除ボタンが押された時の処理
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault(); // フォームの送信（リロード）を止める！

        if (!confirm('本当に削除しますか？')) {
            return false;
        }

        let productId = $(this).data('product_id'); // ボタンからIDを取得
        let deleteUrl = "{{ route('products.destroy', ':id') }}".replace(':id', productId);
        let targetRow = $(this).closest('tr'); // 削除する行（tr）を捕まえておく

        $.ajax({
          url: deleteUrl,
          type: 'POST', // LaravelのDELETEはPOSTで送る
          data: {
              '_method': 'DELETE', // DELETEメソッドとして扱う
              '_token': '{{ csrf_token() }}' // セキュリティトークン
          }
        })
        .done(function() {
            // 通信成功：その行をスッと消す！
            targetRow.fadeOut(); 
        })
        .fail(function() {
            alert('削除に失敗しました。');
        });
    });
</script>