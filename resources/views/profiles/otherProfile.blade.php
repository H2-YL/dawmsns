@extends('layouts.app')

@section('content')
  <div class='container'>
    <!-- それぞれの変数において値を取得できなかった場合、処理を止める。 -->
    @if( $user === null || $posts === null || $follow === null )
      <a>ユーザー情報の取得に失敗しました。もう一度やり直してください。</a>
    @else
      <table class='table table-hover'>
        <tr>
          <td><img src="{{ asset('/storage/userIcon/'. $user->image) }}" alt="{{ $user->image }}"></td> <!--プロフ画像-->
          <td>UserName</td> <!--ユーザー名-->
          <td>{{ $user->name }}</td> <!--ユーザーネーム-->
          <td></td>
        </tr>
        <tr>
          <td></td> <!--空欄-->
          <td>Bio</td><!--自己紹介文（任意）-->
          <td>{{ $user->bio }}</td> <!--自己紹介-->
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <!-- if(in_array($user->id,$follow)) ：前方の「@」は外した-->
          <!-- in_array(配列,検索対象)：今回の場合は「$user->id」という配列に$followの値が入っているかの確認だが、冗長的なので別の方法を考える -->
          <!-- 1は文字列ではないので、「''」や「""」は必要無い -->
          <td>
          @if($follow >= 1)
            <form action="/follow/delete" method="post">
            @csrf
            <input type="hidden" name="targetUserId" value="{{ $user->id }}">
            <button type="submit" class="btn btn-danger">フォローをはずす</button>
          </form>
          @else
            <form action="/follow/create" method="post">
            @csrf
            <input type="hidden" name="targetUserId" value="{{ $user->id }}">
            <button type="submit" class="btn btn-primary">フォローする</button>
            </form>
          @endif
          </td>
        </tr>
      </table>
      <table class='table table-hover'>
      @foreach ($posts as $post)
        <tr>
          <td><img src="{{ asset('/storage/userIcon/'. $user->image) }}" alt="{{ $user->image }}"></td>
          <td>{{ $user->name }}</td> <!--ユーザーネーム-->
          <td>{{ $post->post }}</td> <!--投稿内容-->
          <td>{{ $post->created_at }}</td> <!--投稿日-->
        </tr>
      @endforeach
      </table>
    @endif
  </div>
@endsection
