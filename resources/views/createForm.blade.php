@extends('layouts.app')

@section('content')

<div class='container'>
    <h1 class='page-header'>Laravelを使った投稿機能の実装</h1>
    <h2 class='page-header'>新しく投稿をする</h2>
    <form action="/post/create" method="post">
    <!-- ↑「『/post/create』に対してPOST通信で値を送る」という記述、その後の詳細はPostsControllersにて -->
      @csrf
      <div class="form-group">
        <input type="text" name="newPost" class="form-control" placeholder="投稿内容">
      </div>
      <div class="pull-right submit-btn">
        <button type="submit" class="btn btn-success">追加</button>
      </div>
    </form>
  </div>

@endsection
