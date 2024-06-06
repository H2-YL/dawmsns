@extends('layouts.app')

@section('content')

<div>
  <form action="/search" method="GET">
  @csrf
    <input type="text" name="name" value="{{ old('name') }}">
    <input type="image" src="images/icons/search.png" alt="search.png">
    @if($errors->has('name'))
      {{ $errors->first('name') }}
    @endif
  </form>
</div> <!--検索バー-->
      <table>
      @foreach ($users as $user)
        <tr>
          <td><img src="{{ asset('/storage/userIcon/'. $user->image) }}" alt="{{ $user->image }}"></td><!--ユーザーアイコン-->
          <td>{{ $user->name }}</td> <!--ユーザー名-->
          @if(in_array($user->id,$follows))
          <!--in_arrayは指定した値が配列に存在するか確認する関数。今回は$followsの中に
              $user->idの値が格納された配列の情報が存在する場合、以下の処理を行う-->
          <td>
            <form action="/follow/delete" method="post">
            @csrf
            <input type="hidden" name="targetUserId" value="{{ $user->id }}">
            <button type="submit" class="btn btn-danger">フォローをはずす</button>
            @if($errors->has('targetUserId'))
              {{ $errors->first('targetUserId') }}
            @endif
          </form>
          </td>
          @else
          <td>
            <form action="/follow/create" method="post">
            @csrf
            <input type="hidden" name="targetUserId" value="{{ $user->id }}">
            <button type="submit" class="btn btn-primary">フォローする</button>
            @if($errors->has('targetUserId'))
              {{ $errors->first('targetUserId') }}
            @endif
            </form>
          </td>
          @endif
        </tr>
      @endforeach
      </table>

@endsection
