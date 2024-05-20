<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use  Carbon\Carbon;

class PostsController extends Controller
{
    // テスト画面のメソッド
    public function hello() {
        echo 'Hello World!!<br>';
        echo 'コントローラーから';
    }

    // index画面のメソッド
    public function index() {
        $posts = DB::table('posts')->get();
        return view('index', ['posts' => $posts]);
        // データベースを接続しデータ取得。postsテーブルを指定し、->getでデータをまとめて取得
    }

    // 投稿画面のメソッド
    public function createForm() {
        return view('createForm');
        // viewsディレクトリの中にあるcreateForm.blade.phpを呼び出す
    }

    // 投稿処理のメソッド
     public function create(Request $request) {
        $post = $request->input('newPost');
        // postやget等の通信でフォームから値が送られる場合、$request変数に値を渡す。createForm.bladeの「<input>タグのname=newPost」とリンクしている。
        DB::table('posts')->insert([
            // postsテーブルに下記内容を追加する
            'post' => $post, // フォームの内容を追加する
            'user_id' => Auth::id(), // ログイン中のユーザーIDで投稿される
            'created_at' => Carbon::now(), // 投稿した時間で反映される
            'updated_at' => Carbon::now(), // 投稿した時間で反映される
            // Carbon::now()：日付処理を行う
        ]);

        return redirect('/top');
        // 投稿完了後、indexに戻る
    }

    // 更新画面のメソッド
    public function updateForm($postId) {
        $post = DB::table('posts') // postsテーブル呼び出し
            ->where('id', $postId) // どこにある情報？→idのカラムが$idのレコードを取得
            ->first(); // 1行のデータのみ取得
        return view('updateForm', compact('post'));
        // 情報取得した上でupdateForm.blade.phpを呼び出す
    }

    // 更新処理のメソッド
    public function update(Request $request) {
        $id = $request->input('id');
        $up_post = $request->input('upPost');
        //「id」「upPost」というnameでフォームから送られた値を別の変数で取得する
        DB::table('posts') // postsテーブル呼び出し
            ->where('id', $id) // どこ？→idカラムが$idのレコードを取得
            ->update(
                ['post' => $up_post,
                 'updated_at' => Carbon::now(),
                // Carbon::now()：日付処理を行う
                ]
                // postとupdated_atの情報の更新処理を行う
            );

        return redirect('/top');
        // 更新した後、index画面に戻る
    }

    // 投稿削除のメソッド
    public function delete(Request $request) {
    $postId = $request->input('id');
    DB::table('posts')
        ->where('id', $postId)
        ->delete();

        return redirect('/top');
    }
    // idに準じた情報をpostsテーブルから削除する。
    // 最終的にindex画面に戻る

    public function search() {
        // dd(1);
        $users = DB::table('users')->get();
        return view('search.search', ['users' => $users]);
    }

    public function followList() {
        // dd(1);
        return view('follows.followList');
    }

        public function followerList() {
        // dd(1);
        return view('follows.followerList');
    }

        public function profile() {
        // dd(1);
        return view('profiles.profile');
    }

    // bladeファイルを表示させる際にのみ、相対パスも一緒に記述しなくてはならない。

    // フォロワーの検索に関してはindexのメソッドで使われているものの応用でOK。
    // 厳密にはtableのpostsをusersにすれば良い。

    // フォローの追加、削除に関しては投稿の追加、削除の応用でOK。
    // CRUDのCreate（insert）とDeleteを使う事で成り立つ。
}
