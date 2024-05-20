<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();
// RegisterControllerとLoginControllerは上述にuse～での記載は不要。
// ここの記述だけで使えるようになる。
// しかし厳密にはいずれのControllerにも最初から記載されているfunctionのメソッドのみ。
// 追加で何かメソッドを記述する場合は、やはりuse～の記載が必要になるため注意！


Route::group(['middleware' => ['auth']], function() {
    // 未ログイン時だと使えない状態

    Route::get('/', function () {
        return view('auth.login'); // プリインストール画面
    });

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::controller(PostsController::class)->group(function() {
    Route::get('/hello', 'hello'); // テスト用画面、URL：/helloでhelloメソッドをgetで呼び出す
    Route::get('/top', 'index'); // 03-01：index画面
    Route::get('/post/create', 'createForm'); // 03-03：投稿画面
    Route::get('/post/edit/{postId}', 'updateForm'); // 04-01：編集画面（idに準じた値を挟む）

    Route::post('/post/create', 'create'); // 投稿完了（blade無し）
    Route::put('/post/edit', 'update'); // 04-02：投稿編集完了（blade無し）
    Route::delete('/post/delete/{postId}', 'delete'); // 03-04：投稿削除完了（blade無し）
    // create、update、deleteは基本的に使い回し可能
    // putとdeleteはpost扱い（ただしhtmlではGETかPOSTしか使えないため、そちらでは@method="post"、@method('PUT')のようにする。）

    // ↑ログイン、トップ画面、メッセージ投稿・編集・削除


    // ↓ユーザー検索画面、検索、ユーザーフォロー・フォロー削除
    Route::get('/search', 'search'); // 05-01：ユーザー検索画面



    // ↓フォロー、フォロワー
    Route::get('/follow-list', 'followList'); // 06：フォローリスト画面

    Route::get('/follower-list', 'followerList'); // 07：フォロワーリスト画面


    // ↓ログインユーザープロフィール、プロフィール編集画面・編集、ユーザープロフィール画面
    Route::get('/profile', 'profile'); // 08：ログインユーザープロフィール画面



    // メソッド名はディレクトリを気にする必要は無いので、たとえばフォローリストの場合なら「follows.followList」では動かない
    // 上述で記述したい場合は「followList」でOK。

});

});
