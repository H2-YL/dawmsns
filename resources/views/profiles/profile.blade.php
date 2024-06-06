@extends('layouts.app')

@section('content')
  <div class='container'>
    @if( $users === null || $posts === null )
      <a>ユーザー情報の取得に失敗しました。もう一度やり直してください。</a>
    @else
      <table class='table table-hover'>
        <tr>
          <td><img src="{{ asset('/storage/userIcon/'. $users->image) }}" alt="{{ $users->image }}"></td> <!--プロフ画像-->
          <td>UserName</td> <!--ユーザー名-->
          <td>{{ $users->name }}</td> <!--ユーザーネーム-->
        </tr>
        <tr>
          <td></td> <!--空欄-->
          <td>MailAddress</td><!--メールアドレス-->
          <td>{{ $users->email }}</td> <!--メルアド-->
        </tr>
        <tr>
          <td></td> <!--空欄-->
          <td>Bio</td><!--自己紹介文（任意）-->
          <td>{{ $users->bio }}</td> <!--自己紹介-->
        </tr>
        <tr>
          <td></td> <!--空欄-->
          <td></td> <!--空欄-->
          <td class="btn-pr-update">
            <a class="btn btn-success" href="/profile/edit/">編集画面へ</a>  <!-- 投稿ボタン -->
          </td>
        </tr>
      </table>
      <table class='table table-hover'>
      @foreach ($posts as $post)
        <tr>
          <td><img src="{{ asset('/storage/userIcon/'. Auth::user()->image) }}" alt="{{ Auth::user()->image }}"></td>
          <td>{{ Auth::user()->name }}</td> <!--ユーザーネーム-->
          <td>{{ $post->post }}</td> <!--投稿内容-->
          <td>{{ $post->created_at }}</td> <!--投稿日-->
        </tr>
      @endforeach
      </table>
    @endif
  </div>
@endsection
