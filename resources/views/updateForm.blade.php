@extends('layouts.app')

@section('content')
  <div class='container'>
    <!-- それぞれの変数において値を取得できなかった場合、処理を止める。 -->
    @if( $post === null || is_numeric($post))
      <a>メッセージ情報を取得できませんでした。もう一度やり直してください。</a>
    @else
      <h2 class='page-header'>投稿内容を変更する</h2>
      <form action="/post/edit/" method="post">
        @method('PUT')
        @csrf
        <div class="form-group">
          <img src="{{ asset('/storage/userIcon/'. Auth::user()->image) }}" alt="{{ Auth::user()->image }}">
          <!-- laravelはログイン中のusersの情報は空気を読んで基本的に出した状態にしてくれる。
          そのため、コントローラーで直接呼び出す予定が無い場合は「Auth::user()->カラム名」という記載をすれば呼び出す事が可能となる。 -->
          <input type="hidden" name="postId" value="{{ $post->id }}">
          <!-- type="hidden"と入力しているのは、$post->idの情報を見せないようにするため -->
          <input type="text" name="post" value="{{ $post->post }}" class="form-control">
            @if($errors->has('postId'))
              {{ $errors->first('postId') }}
            @endif
            @if($errors->has('post'))
              {{ $errors->first('post') }}
            @endif
        </div>
        <div class="pull-right submit-btn">
          <button type="submit" class="btn">
            <img src="{{ asset('images/icons/post.png') }}" alt="post.png">
          </button>
        </div>
      </form>
    @endif
  </div>
@endsection
