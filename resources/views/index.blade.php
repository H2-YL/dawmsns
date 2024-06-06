@extends('layouts.app')

@section('content')
  <div class='container'>
    <table>
      <tr>
        <form action="/post/create" method="post">
        <!-- ↑「『/post/create』に対してPOST通信で値を送る」という記述、その後の詳細はPostsControllersにて -->
          @csrf
          <div class="form-group">
            <img src="{{ asset('/storage/userIcon/'. Auth::user()->image) }}" alt="{{ Auth::user()->image }}">
            <input type="text" name="post" class="form-control" placeholder="何をつぶやこうか…？">
            @if($errors->has('post'))
              {{ $errors->first('post') }}
            @endif
            <!--   hasメソッドを使いエラーチェックを行い、firstメソッドを使いエラーメッセージを1つだけ表示させる。 -->
          </div>
          <div class="pull-right submit-btn">
            <button type="submit" class="btn"> <!-- class="btn-success"一次削除中-->
            <img src="{{ asset('images/icons/post.png') }}" alt="post.png">
            </button>
          </div>
        </form>
      <tr>
    </table>
    <table>
      <tr>
        <td>フォロー数： {{ $cntFollows }}</td>
        <td>フォロワー数： {{ $cntFollowers }}</td>
      </tr>
      <tr>
        <td>
          <a class="btn btn-primary" href="/follow-list">フォローリスト</a>
        </td>
        <td>
          <a class="btn btn-primary" href="/follower-list">フォロワーリスト</a>
        </td>
        <td>
          <a class="btn btn-primary" href="/search">ユーザー検索</a>
        </td>
        <td>
          <a class="btn btn-primary" href="/profile">ログインユーザープロフィール</a><!-- デバッグ用、後に削除予定 -->
        </td>
      </tr>
    </table>
    <h2 class='page-header'>投稿一覧</h2>
    <a>
      @if($errors->has('id'))
        {{ $errors->first('id') }}
      @endif
    </a><!-- 投稿削除に失敗した時のエラーメッセージ -->
    <table class='table table-hover'>
      @foreach ($posts as $post)
      <tr>
        @if($post->user_id === Auth::id())
        <td><a href="/profile"><img src="{{ asset('/storage/userIcon/'. $post->image) }}" alt="{{ $post->image }}"></a></td>
        @else
        <td><a href="/other/profile/{{ $post->user_id }}"><img src="{{ asset('/storage/userIcon/'. $post->image) }}" alt="{{ $post->image }}"></a></td>
        @endif
        <td>{{ $post->name }}</td> <!--ユーザーネーム-->
        <td>{{ $post->post }}</td> <!--投稿内容-->
        <td>{{ $post->created_at }}</td> <!--投稿日-->
        @if($post->user_id === Auth::id())
        <!-- if文で、controller側で設定したuser_idとログイン中のIDが完全に一致した場合、下記を表示させる -->
        <td>
          <a class="btn" href="/post/edit/{{ $post->id }}">
            <img src="{{ asset('images/icons/edit.png') }}" alt="edit.png">
          </a><!--class="btn-primary"を一時削除-->
        </td> <!--更新ボタン-->
        <td>
          <form action="/post/delete/{{ $post->id }}" method="post" onclick="return confirm('このつぶやきを削除します。よろしいでしょうか？')">
          @method('DELETE')
          @csrf
          <input type="hidden" name="id" value="{{ $post->id }}">
          <!-- type="hidden"と入力しているのは、$post->idの情報を見せないようにするため -->
          <button type="submit" class="btn"><!--class="btn-danger"を一時削除-->
            <img src="{{ asset('images/icons/trash.png') }}" alt="trash.png">
          </button>
          </form>
        </td> <!--削除ボタン-->
        @else
        <!-- 上述のIDで情報不一致だった場合、下記を表示させる（アイコンを表示させない） -->
        <td></td>
        <td></td>
        @endif
      </tr>
      @endforeach
    </table>
  </div>
@endsection
