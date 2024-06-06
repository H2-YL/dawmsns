<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [ // バリデーションルール
            'name' => ['required', 'between:4,12', 'string'],
            'email' => ['required', 'max:255', 'string', 'email', 'unique:users'],
            'password' => ['required', 'between:8,128', 'string'],
            'password_confirmation' => ['required', 'same:password'],
        ],
        [ // 違反した種類に対するエラーメッセージ
            // nameに対するメッセ
            'name.required' => 'ユーザー名を入力して下さい。',
            'name.between' => 'ユーザー名は4文字以上12文字以内で入力して下さい。',
            'name.string' => 'ユーザー名には全角漢字カタカナひらがな数字記号もしくは半角英数記号を入力して下さい。',
            // emailに対するメッセ
            'email.required' => 'メールアドレスを入力して下さい。',
            'email.max' => 'メールアドレスは255文字以内で入力してください。',
            'email.string' => 'メールアドレスには利用可能な文字を入力してください。',
            'email.email' => 'メールアドレスは正しい形式で入力してください。',
            'unique:users' => '入力されたメールアドレスはすでに登録されています。別のメールアドレスを登録してください。',
            // passwordに対するメッセ
            'password.required' => 'パスワードを入力してください。',
            'password.between' => 'パスワードは8文字以上128文字以内で入力してください。',
            'password.string' => 'パスワードには半角英数記号を入力してください。',
            // password_confirmationに対するメッセ
            'password_confirmation.required' => 'パスワード確認を入力してください。',
            'password_confirmation.same' => 'パスワードと確認の入力が一致しません。',
        ]);
    }
        // required:入力されてない
        // between:最小値,最大値で文字が少ないか多いか
        // string:文字列型か否か

        // max:この数値を超えていないかどうか
        // unique:テーブル名:ここにある情報と照らし合わせて重複しているか否か


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // パスワードはセキュリティ対策にハッシュ化した上で保存する
        ]);
    }
}
