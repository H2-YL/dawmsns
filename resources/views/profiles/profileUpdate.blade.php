@extends('layouts.app')

@section('content')
  <div class='container'>
    @if( $user === null )
      <a>ユーザー情報の取得に失敗しました。もう一度やり直してください。</a>
    @else
      <table class='table table-hover'>
        <form action="/profile/edit" method="post" enctype="multipart/form-data">
          @method('PUT')
          @csrf
          <tr>
            <td><img src="{{ asset('/storage/userIcon/'. $user->image) }}" alt="{{ $user->image }}"></td> <!--プロフ画像。ファイル名を指定する際に文字列と変数の結合のために「.」を使用している。-->
            <td>UserName</td> <!--ユーザー名-->
            <td>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
              @if($errors->has('name'))
                {{ $errors->first('name') }}
              @endif
            </td> <!--ユーザーネーム編集-->
          </tr>
          <tr>
            <td></td> <!--空欄-->
            <td>MailAddress</td><!--メールアドレス-->
            <td>
              <input type="text" name="email" value="{{ old('email', $user->email) }}" class="form-control">
              @if($errors->has('email'))
                {{ $errors->first('email') }}
              @endif
            </td> <!--メルアド編集-->
          </tr>
          <tr>
            <td>
            </td> <!--空欄-->
            <td>PassWord</td><!--新パスワード入力欄（空欄可）-->
            <td>
              <input type="password" name="password" value="" class="form-control">
              @if($errors->has('password'))
                {{ $errors->first('password') }}
              @endif
            </td> <!--新パスワード入力-->
          </tr>
          <tr>
            <td>
            </td> <!--空欄-->
            <td>PassWord confirm</td><!--新パスワードの確認入力欄-->
            <td>
              <input type="password" name="password_confirmation" value="" class="form-control">
              @if($errors->has('password_confirmation'))
                {{ $errors->first('password_confirmation') }}
              @endif
            </td> <!--新パスワード入力-->
          </tr>
          <tr>
            <td></td> <!--空欄-->
            <td>Bio</td><!--自己紹介文（任意）-->
            <td>
              <input type="text" name="bio" value="{{ old('bio', $user->bio) }}" class="form-control">
              @if($errors->has('bio'))
                {{ $errors->first('bio') }}
              @endif
            </td> <!--自己紹介編集-->
          </tr>
          <tr>
            <td></td> <!--空欄-->
            <td>Icon Image</td><!--ユーザーアイコン用の画像（任意）-->
            <td>
              <input type="file" name="image" value="{{ old('image', $user->image) }}" class="form-control">
              @if($errors->has('image'))
                {{ $errors->first('image') }}
              @endif
            </td> <!--ユーザーアイコン用の画像編集-->
          </tr>
          <tr>
          <tr>
            <td></td> <!--空欄-->
            <td></td> <!--空欄-->
            <td class="btn-pr-update">
              <button type="submit" class="btn btn-primary">
              更新
              </button> <!-- 更新ボタン -->
            </td>
          </tr>
        </form>
      </table>
    @endif
  </div>
@endsection
