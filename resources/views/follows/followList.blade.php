@extends('layouts.app')

@section('content')
  <div class='container'>
    <h2>follow list</h2>
      @foreach ($follows as $follow)
        <img class='page-header' src="{{ asset('/storage/userIcon/'. $follow->image) }}" alt="{{ $follow->image }}"><!--ユーザーアイコン-->
        <a>{{ $follow->name }}</a><!-- ユーザー名確認用 -->
      @endforeach
  </div>
  <table class='table table-hover'>
  @foreach ($posts as $post)
    <tr>
      <td><a href="/other/profile/{{ $post->id }}"><img src="{{ asset('/storage/userIcon/'. $post->image) }}" alt="{{ $post->image }}"></a></td>
      <td>{{ $post->name }}</td> <!--ユーザーネーム-->
      <td>{{ $post->post }}</td> <!--投稿内容-->
      <td>{{ $post->created_at }}</td> <!--投稿日-->
    </tr>
  @endforeach
  </table>

@endsection
