@extends('layouts.app')

@section('content')
  <div class='container'>
    <h1 class='page-header'>Laravelを使った投稿機能の実装</h1>
    <p class="pull-right">
      <a class="btn btn-success" href="post/create-form">投稿する</a>
    </p>
    <h2 class='page-header'>投稿一覧</h2>
    <table class='table table-hover'>
      <tr>
        <th>投稿No</th>
        <th>ユーザーID</th>
        <th>投稿内容</th>
        <th>投稿日時</th>
        <th>更新日時</th>
        <th></th>
        <th></th>
      </tr>
      @foreach ($posts as $post)
      <tr>
        <td>{{ $post->id }}</td><!--投稿番号-->
        <td>{{ $post->user_id }}</td> <!--ユーザーID-->
        <td>{{ $post->post }}</td> <!--投稿内容-->
        <td>{{ $post->created_at }}</td> <!--登校日-->
        <td>{{ $post->updated_at }}</td> <!--更新日-->
        <td>
          <a class="btn btn-primary" href="/post/{{ $post->id }}/update-form">更新</a>
        </td> <!--更新ボタン-->
        <td>
          <form action="/post/delete" method="post" onclick="return confirm('こちらの投稿を削除してもよろしいでしょうか？')">
          @method('DELETE')
          @csrf
          <input type="hidden" name="id" value="{{ $post->id }}">
          <button type="submit" class="btn btn-danger">削除</button>
          </form>
        </td> <!--削除ボタン-->
      </tr>
      @endforeach
    </table>
  </div>
@endsection
