@extends('layouts.app')

@section('content')

<div class='container'>
    <h2 class='page-header'>新しく投稿をする</h2>
    <form action="/post/create" method="post">
    <!-- ↑「『/post/create』に対してPOST通信で値を送る」という記述、その後の詳細はPostsControllersにて -->
      @csrf
      <div class="form-group">
        <img src="{{ asset('/storage/userIcon/'. $user->image) }}" alt="{{ $user->image }}">
        <input type="text" name="post" class="form-control" placeholder="何をつぶやこうか…？">
      </div>
      <div class="pull-right submit-btn">
        <button type="submit" class="btn"> <!-- class="btn-success"一次削除中-->
          <img src="{{ asset('images/icons/post.png') }}" alt="post.png">
        </button>
            @if($errors->has('post'))
              {{ $errors->first('post') }}
            @endif
      </div>
    </form>
  </div>

@endsection
