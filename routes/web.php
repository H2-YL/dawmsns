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
    Route::get('/top', 'index'); // index画面
    Route::get('/post/create-form', 'createForm'); // 投稿画面
    Route::get('/post/{id}/update-form', 'updateForm'); // 更新画面（idに準じた値を挟む）

    Route::post('/post/create', 'create');

    Route::put('/post/update', 'update');

    Route::delete('/post/delete', 'delete');

    Route::get('/follow-list', 'followList');


});

});
