<?php

namespace App\Http\Controllers;

// 下記は vendor\laravel\src\Illuminateのディレクトリに存在している。
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $follows = DB::table('follows')
        ->where('follower_id',Auth::id())->pluck('user_id')->toArray();
        // pluck：カラムのデータのみを取り出せるメソッドで、今回は"user_id"から取り出す。
        // 後で配列にして使うために、toArrayメソッドを使っている。

        $cntFollows   = count($follows); // フォローのカウント
        $cntFollowers = DB::table('follows')
                        ->where('user_id',Auth::id())
                        ->pluck('follower_id')->count(); // フォロワーのカウント

        // カウント対象はフォローとフォロワーのため、自身の情報を追加前に記述

        array_push($follows , Auth::id());
        // 自分のIDの情報を追加し自分の投稿も閲覧可能にする。

        $posts = DB::table('posts')
        ->leftJoin('users','posts.user_id','=','users.id') // 左のレコード全てと結合条件に合致する右のレコードを返す
        ->orderBy('posts.created_at', 'desc')//日付を降順にして表示
        ->whereIn('users.id',$follows) // idの値が$followsもとい自身のidの値である事が条件
        ->select('posts.id','posts.post','posts.user_id','posts.created_at','users.name','users.image')
        // 元々「'user.id'」を入れていたが、「posts.id」とカラム名が同じ。
        // 後のカラム名が優先されるため、エラーを起こさないためには不要なカラム名を消すか、as文を使って名前を変える。
        // 何か必要で追加した可能性アリ、「'users.id',」は一時的に取り除いておく。
        ->get();
        // getで条件に合った全てのデータを取得

        return view('index',
        [
            'posts' => $posts,
            'follows' => $follows ,
            'cntFollows' => $cntFollows ,
            'cntFollowers' => $cntFollowers
        ]);
        // return vierの中にある[]内には、['bladeで使いたい変数' => [$]から始まるメソッド内に記載されている変数]
        // を記述する。
        // また、内容がシンプルであれば、「compact('bladeで使いたい変数')」でも可能だが、詳細は後述にて。

        // $posts = DB::table('posts')
        // ->orderBy('created_at', 'desc')//日付
        // ->where('user_id','=',Auth::id())
        // ->get();
        // return view('index', ['posts' => $posts]);
        // データベースを接続しデータ取得。postsテーブルを指定し、->getでデータをまとめて取得
    }

    // // 投稿画面のメソッド（今回は必要無し）
    // public function createForm() {
    //     $user = DB::table('users')
    //     ->where('id', '=' , Auth::id())
    //     ->select('image')
    //     ->first();
    //     // dd($user);

    //     return view('createForm', compact('user'));
    //     // viewsディレクトリの中にあるcreateForm.blade.phpを呼び出す
    //     // 「compact('bladeで使いたい変数')」を使っているがindexメソッドの後半に書かれている補足と内容は一緒。
    //     // 違う点は必ず1つだけの変数にしか適用できない点。今回の場合は「$user」が該当する。
    // }

    // 投稿処理のメソッド
     public function create(Request $request) {
        // 「Request $request」は、画面側から「〇〇の情報を欲しい」などの要求全般を指す。
        // それに対し、サーバーが処理を行って該当ページを返す事をレスポンスと言う。
        // たとえば、「投稿を送信した」→もう一度元のページを表示したいとリクエスト。
        // サーバー側が「了解、該当ページを送ります」というレスポンス。
        // この際にblade側で重要な要素である「name」属性があったはず。
        // $request->input('name')で該当name属性に記載されているものをリクエストするか。

        $validMessage =
        [ // 違反した種類に対するエラーメッセージ
            // postに対するメッセージ
            'post.required' => 'メッセージを入力して下さい。',
            'post.max' => 'メッセージは400文字以内で入力して下さい。'
        ];

        $request->validate([
            'post' => ['max:400','required'],
        ],
        $validMessage);
        // dd($validMessage);

        $post = $request->input('post');
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

    // 編集画面のメソッド
    public function updateForm($postId = null) {

        $post = DB::table('posts') // postsテーブル呼び出し
            ->where([
                ['id',$postId],
                ['user_id',Auth::id()]
            // idの値が$postIdで、かつuser_idがログインユーザーidの場合
            ])
            ->select('id','post','user_id')
            ->first(); // 1行のデータのみ取得。投稿内容を取得したいだけなのでfirstで良い。

        return view('updateForm', compact('post'));
        // 情報取得した上でupdateForm.blade.phpを呼び出す
    }

    // 編集処理のメソッド
    public function update(Request $request) {
        $validMessage =
        [ // 違反した種類に対するエラーメッセージ
            // idに対するメッセージ
            'postId.required' => 'メッセージの編集に失敗しました。もう一度やり直してください。',
            'postId.alpha_dash' => 'メッセージの編集に失敗しました。もう一度やり直してください。',
            // upPostに対するメッセージ
            'post.required' => 'メッセージを入力してください。',
            'post.max' => 'メッセージは400文字以内で入力して下さい。'
        ];

        $request->validate([
            'postId' => ['required','alpha_num'],
            'post' => ['required','max:400'],
        ],
        $validMessage);
        // dd($validMessage);

        $id = $request->input('postId');
        $up_post = $request->input('post');
        // 該当name属性のフォームから送られた値を変数として取得する
        DB::table('posts') // postsテーブル呼び出し
            ->where('id', $id) // idの値が$idである事が条件
            ->update(
                ['post' => $up_post,
                 'updated_at' => Carbon::now(),
                 // postをinput
                // Carbon::now()：現在の日付処理を行う
                ]
                // postをupdated_atの情報の更新処理を行う
            );

        return redirect('/top');
        // 更新した後、index画面に戻る
    }

    // 投稿削除のメソッド
    public function delete(Request $request) {
        // Request $requestで、画面側から欲しい情報を要求する。
        $validMessage =
        [ // 違反した種類に対するエラーメッセージ
            // idに対するメッセージ
            'id.required' => 'メッセージの削除に失敗しました。もう一度やり直してください。',
            'id.alpha_dash' => 'メッセージの削除に失敗しました。もう一度やり直してください。',
        ];

        $request->validate([
            'id' => ['required','alpha_num'],
        ],
        $validMessage);
        // dd($validMessage);

    $postId = $request->input('id');
    // 該当name属性のフォームから送られた値を変数として取得する
    DB::table('posts')
        ->where('id', $postId)
        ->delete();
        // posts.idの値が$postIdである場合、情報を削除する。

        return redirect('/top');
    }
    // idに準じた情報をpostsテーブルから削除する。
    // 最終的にindex画面に戻る

    // ユーザー検索のメソッド
    public function search(Request $request) {

        $validMessage =
        [ // 違反した種類に対するエラーメッセージ
            // nameに対するメッセージ
            'name.string' => 'ユーザー名には全角漢字カタカナひらがな数字記号もしくは半角英数記号を入力して下さい。',
        ];

        $request->validate([
            'name' => ['string'],
        ],
        $validMessage);

        if($request->isMethod("get")){
            $request->session()->flash('_old_input',[
                'name' => $request->input('name'),
                // もしメソッドがgetだった場合、
                // $request->session()->flashを使い、フラッシュデータにする。
                // フラッシュデータ：一時的に保存できる情報で、リダイレクト後にセッションから削除されるようになっている。
                // _old_inputを使ってinput('')内のname属性が場合、
                // 情報を保持しながら表示させる（formのinputの値）
                // つまりサーチした後に検索ワードが消えなくなる。
            ]);
        }
        $keyword =\old('name');

        // $keyword = $request->input('keyword');
        // if($request->isMethod("post")){
        //     $request->session()->flash('_old_input',[
        //         'keyword' => $keyword,
        //     ]);
        // }   // oldを使って情報を保持しながら検索を行う
        $users = DB::table('users')
        ->where('id','!=', Auth::id())
        // Auth::idを使い、ログインしているユーザーのIDと等しくないユーザーを表示
        // 「!=」：等しくない
        ->where('name','like',"%{$keyword}%") // カラム'name'とkeywordが部分一致してればOK
        // 「%」の部分をワイルドカードといい、「like」とセットで使う。
        // この記述はSQLにおいて「どんな配列でも良い」という役割を果たしている。
        ->get();
        // $follower = $request->input('follower');
        $follows = DB::table('follows')
        ->where('follower_id',Auth::id())->pluck('user_id')->toArray();
        // follower_idがログインユーザーidと同一の場合、pluckでuser_idの情報を配列化する
        // そうする事で、$followsに格納される値はフォロー中のuser_idが配列として抽出される
        // pluckで配列化したのにtoArrayを追加する理由は有るのか？既に連想配列化されているのに。
        // コレクション化したインスタンスを配列に直すためにオブジェクトとして置き換えてあげる。
        // ちなみにtoArray無しだと、type arrayじゃないからbladeでin_arrayは使えないと
        // エラーが起きる。

        return view('search.search', ['users' => $users, 'follows' => $follows]);
    }

    // フォローするメソッド
    public function followCreate(Request $request) {
        $validMessage =
        [ // 違反した種類に対するエラーメッセージ
            // followに対するメッセージ
            'targetUserId.required' => 'フォローに失敗しました。もう一度やり直してください。',
            'targetUserId.numeric' => 'フォローに失敗しました。もう一度やり直してください。',
            // バリデーション確認用：follow.alpha
        ];

        $request->validate([
            'targetUserId' => ['required','numeric'],
        ],
        $validMessage);

        $follow = $request->input('targetUserId');
        DB::table('follows')->insert([
            'user_id' => $follow,
            'follower_id' => Auth::id(),
            // 条件はuser_idが$followerの値であり、かつfollower_idがログイン中のユーザーのidである事
        ]);
        return back()->withInput();
        // 直前のページに戻る、直前の情報を保持したい場合は「withInput」を追加する。
    }

    // フォローをはずすメソッド
    public function followDelete(Request $request) {
        $validMessage =
        [ // 違反した種類に対するエラーメッセージ
            // followerに対するメッセージ
            'targetUserId.required' => 'フォロー解除に失敗しました。もう一度やり直してください。',
            'targetUserId.numeric' => 'フォロー解除に失敗しました。もう一度やり直してください。',
            // バリデーション確認用：follower.alpha（アルファベットのみ）
        ];

        $request->validate([
            'targetUserId' => ['required','numeric'],
        ],
        $validMessage);

        $follower = $request->input('targetUserId');
        DB::table('follows')
            ->where([
                ['user_id', $follower],
                ['follower_id', Auth::id()]
                // 条件はuser_idが$followerの値であり、かつfollower_idがログイン中のユーザーのidである事
            ])
            ->delete();
        return back()->withInput();

    }

    // フォローリストのメソッド
    public function followList() {
        $follows = DB::table('follows')
        ->leftJoin('users','follows.user_id','=','users.id')
        ->orderBy('users.name', 'desc')//日付

        ->where('follows.follower_id',Auth::id())
        // follower_idがログインユーザーのidと同一であるかが条件
        ->select('users.id','users.name','users.image')
        ->get();
        // フォローしている人の情報を取得

        $posts = DB::table('follows')
        ->Join('users','follows.user_id','=','users.id')
        ->Join('posts','follows.user_id','=','posts.user_id')
        ->orderBy('posts.created_at', 'desc')//日付を降順にして表示
        ->where('follows.follower_id',Auth::id())
        // follower_idがログインユーザーのidと同一であるかが条件
        ->select('users.id','users.name','users.image','posts.post','posts.created_at')
        ->get();
        // フォローしている人の投稿内容を取得

        return view('follows.followList',
        [
            'follows' => $follows,
            'posts' => $posts
        ]);
    }

    // フォロワーリストのメソッド
    public function followerList() {
        $followers = DB::table('follows')
        ->leftJoin('users','follows.follower_id','=','users.id')
        ->orderBy('users.name', 'desc')//日付

        // ->where('posts.user_id','=',Auth::id())
        ->where('follows.user_id',Auth::id())
        ->select('users.id','users.name','users.image')
        ->get();
        // フォローしてくれている人の情報を取得

        $posts = DB::table('follows')
        ->Join('users','follows.follower_id','=','users.id')
        ->Join('posts','follows.follower_id','=','posts.user_id')
        ->orderBy('posts.created_at', 'desc')//日付を降順にして表示
        ->where('follows.user_id',Auth::id())
        // follows.user_idの値とログイン中のidの値が同じである事が条件
        ->select('users.id','users.name','users.image','posts.post','posts.created_at')
        ->get();
        // フォローしてくれている人の投稿内容を取得

        return view('follows.followerList',
        [
            'followers' => $followers,
            'posts' => $posts
        ]);
        // ['bladeに送る変数名' => 値（今回の場合$followersと$posts）]
    }

    // プロフィールのメソッド
    public function profile() {
        $users = DB::table('users')
        ->where('id',Auth::id())
        ->select('id','name','image','email','bio')
        ->first();

        $posts = DB::table('posts')
        ->where('user_id',Auth::id())
        ->orderBy('created_at', 'desc')//日付を降順にして表示
        ->select('id','user_id','post','created_at')
        ->get();

        return view('profiles.profile',
        [
            'users'  => $users,
            'posts' => $posts
        ]);
    }


    // プロフィール編集画面のメソッド
    public function profileUpdate() {
        // dd(1);
        $user = DB::table('users')
        ->where('id', Auth::id()) // ユーザーIDがログイン中のIDと同じである事が条件
        ->first(); // 編集したい自分のデータのみを取得
        // dd($user);
        return view('profiles.profileUpdate', compact('user'));
    }

    // プロフィール編集処理のメソッド
    public function profileComplete(Request $request) {
        $validMessage =
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
            'email.unique' => '入力されたメールアドレスはすでに登録されています。別のメールアドレスを登録してください。',
            // passwordに対するメッセ
            'password.required' => 'パスワードを入力してください。',
            'password.between' => 'パスワードは8文字以上128文字以内で入力してください。',
            'password.string' => 'パスワードには半角英数記号を入力してください。',
            // password_confirmationに対するメッセ
            'password_confirmation.required' => 'パスワード確認を入力してください。',
            'password_confirmation.same' => 'パスワードと確認の入力が一致しません。',
            // bioに対するメッセ
            'bio.max' => 'メッセージは400文字以内で入力してください。',
            // imageに対するメッセ
            'image.image' => '画像形式のファイルをアップロードしてください。',
            'image.max' => 'アップロードするファイルは20MB以下にしてください。'
        ];

            $request->validate([
            'name' => ['required', 'between:4,12', 'string'],
            'email' => ['required', 'max:255', 'string', 'email', Rule::unique('users','email')->ignore(Auth::id(),'id' )],
            // uniqueキーで設定したいデータベースと比較して、何を比較対象から除外したいか。今回はAuth::idとidが一致してる場合。
            'password' => ['nullable', 'between:8,128', 'string'],
            'password_confirmation' => ['same:password'],
            'bio' => ['max:400'],
            'image' => ['nullable','image', 'max:204800'], // 縦棒とカンマは一緒に使えない
          ],
          $validMessage );

        // uniqueキーを使う場合は、後述のif文でも問題ないが、上述で記載した方がスマート。
        // if(Auth::user()->email == $request->input('upEmail')){
        //   $request->validate([
        //     'upName' => ['required', 'between:4,12', 'string'],
        //     'upEmail' => ['required', 'max:255', 'string', 'email'],
        //     'upPassword' => ['required', 'between:8,128', 'string'],
        //     'password_confirmation' => ['required', 'same:upPassword'],
        //     'upBio' => ['max:400'],
        //     // 'image' => ['image', 'file|size:20480'],
        //   ],
        //   $validMessage );
        // } else {
        //     $request->validate([
        //     'upName' => ['required', 'between:4,12', 'string'],
        //     'upEmail' => ['required', 'max:255', 'string', 'email','unique:users,email'],
        //     'upPassword' => ['required', 'between:8,128', 'string'],
        //     'password_confirmation' => ['required', 'same:upPassword'],
        //     'upBio' => ['max:400'],
        //     // 'image' => ['image', 'file|size:20480'],
        //   ],
        //   $validMessage );
        // }

        // if (isset($file)) {
        //     $file_name = $request->file('image')->getClientOriginalName();
        //     $request->file('image')->storeAs('/public/userIcon/',$file_name);


        $file = $request->file('image');
        // 該当name属性のフォームから送られた値をファイルデータとして取得する
        if (isset($file)) {
            // もしファイルに値が入っている場合、普通に画像のアップロードを行う。
            // 今回の場合だと、アップロードしたい画像が選択されていた場合。
            $file_name = $file->getClientOriginalName();
            // 拡張子を含めアップロードしたファイルのファイル名を取得。
            $file->storeAs('/public/userIcon/',$file_name);
            // storeAsで、パス名を設定しながらアップロードしたいファイル名を決める。
        } else {
            // そうでない場合、つまりファイルが値に入っていない場合。
            // 分かりやすく言うと、アップロードするファイルが存在しない場合。
            // 手間はかかるがfile_nameにログイン中のユーザーの画像を再アップロードさせる。
            $file_name = Auth::user()->image;
        }
        // null判定の処理であるissetを使う。
        // ただし、そうでない即ち既に画像が入っている状態だった場合は、手間はかかるけどfile_nameにログイン中のユーザーの画像をアップロードさせる。
        // ディレクトリ名をstore内に記述する事で、ディレクトリを作成しつつ、その場所に画像を保存してくれる。storeAsの場合、名前を付ける事も出来る。
        // しかし、このままではアップロードしたファイルを閲覧する事が出来ない。
        // 外部に公開される即ちページ上に表示できるようにするにはpublic/storageのディレクトリに
        // ファイルが存在しなくてはならないのだが、ファイルが保存される場所はstrage/app/publicとなっている。
        // この時、mysqlでphp artisan storage:linkを実行する事でシンボリックリンクという
        // ショートカット的なものを作成する事が可能。
        // そうする事で、初めてアップロードしたファイルを閲覧する事が出来る。

        $pass = $request->input('password');

        if($pass){
            DB::table('users')
            ->where('id', Auth::id())
            ->update(
                [
                'password'   => Hash::make($request->input('password')),
                ]);
        }

        // dd($request->file('upImage'));
        // dd($file_name);
        DB::table('users')
        ->where('id', Auth::id())
        ->update(
             [
            'name'       => $request->input('name'),
            'email'      => $request->input('email'),
            'bio'        => $request->input('bio'),
            'updated_at' => Carbon::now(),
            'image'      => $file_name,
            ]);


        // パスワードが空でも更新処理を行える方法の別案
        // if(){
        //     $pass = Hash::make($request->input('upPassword'));
        // }else{
        //     $pass = Auth::user()->password;
        // }
        // DB::table('users')
        // ->where('id', Auth::id())
        // ->update(
        //     ['name'       => $request->input('upName'),
        //      'email'      => $request->input('upEmail'),
        //      'password'   => $pass
        //      'bio'        => $request->input('upBio'),
        //      'updated_at' => Carbon::now(),
        //     ]
        //     // 'image'      => $up_profile,
        // );

        return redirect('/profile');
    }


    // 他のユーザーのプロフィールのメソッド
    public function otherUserProfile($targetUserId = null) {
        // 「= null」を追加する事で、無い時もある場合の処理を追加できる。
        // もちろんweb.php側でURLに「$targetUserId」が無いパターンの追加も必要。

        // 他のユーザー情報の取得
        $user = DB::table('users')
        ->where('id',$targetUserId)
        ->select('id','name','image','bio')
        ->first();

        // 他のユーザーの投稿を取得
        $posts = DB::table('posts')
        ->where('user_id',$targetUserId)
        ->orderBy('created_at', 'desc')//日付を降順にして表示
        ->select('id','user_id','post','created_at')
        ->get();

        // 他のユーザーをフォローしているかの有無を取得
        $follow = DB::table('follows')
        ->where([
            ['user_id',$targetUserId],
            ['follower_id',Auth::id()]
        ])
        ->count();

        // ログイン中のユーザーだったら強制的にプロフィール画面へ遷移
        if($targetUserId == Auth::id()){
            return redirect('/profile');
        } else {
            // ログイン中でないなら他ユーザープロフィールを表示
            return view('profiles.otherProfile',
            [
                'user'  => $user,
                'posts' => $posts,
                'follow' => $follow
            ]);

        }
    }

    //     // ユーザー検索のメソッド
    // public function searc(Request $request) {
    //     // 「Request $request」は、他のファイルから情報を貰うために使う。
    //     // dd(1);
    //     if($request->isMethod("get")){
    //         $request->session()->flash('_old_input',[
    //             'keyword' => $request->input('keyword'),
    //             // 「''」内のnameがあった場合、それを持ってくる（formのinputの値）
    //         ]);
    //     }
    //     $keyword =\old('keyword');

    //     // $keyword = $request->input('keyword');
    //     // if($request->isMethod("post")){
    //     //     $request->session()->flash('_old_input',[
    //     //         'keyword' => $keyword,
    //     //     ]);
    //     // }   // oldを使って情報を保持しながら検索を行う
    //     $users = DB::table('users')
    //     ->where('id','!=', Auth::id())
    //     // Auth::idを使い、ログインしているユーザーのIDと等しくないユーザーを表示
    //     // 「!=」：等しくない
    //     ->where('name','like',"%{$keyword}%")
    //     ->get();
    //     // $follower = $request->input('follower');
    //     $follows = DB::table('follows')
    //     ->where('follower_id','=',Auth::id())->pluck('user_id')->toArray();
    //     return view('search.search', ['users' => $users, 'follows' => $follows]);
    // }






    //     // dd(1);
    //     $users = DB::table('users')
    //     ->where('id','=',Auth::id())
    //     ->select('id','name','image','email','bio')
    //     ->first();

    //     $posts = DB::table('posts')
    //     ->where('user_id','=',Auth::id())
    //     ->orderBy('created_at', 'desc')//日付を降順にして表示
    //     ->select('id','user_id','post','created_at')
    //     ->get();

    //     return view('profiles.otherProfile',
    //     [
    //         'users'  => $users,
    //         'posts' => $posts
    //     ]);
    // }

    // // 編集画面のメソッド、IDをそれぞれで使う参考資料
    // public function updateForm($postId) {
    //     $post = DB::table('posts') // postsテーブル呼び出し
    //         ->where('id', $postId) // どこにある情報？→idのカラムが$idのレコードを取得
    //         ->first(); // 1行のデータのみ取得。投稿内容を取得したいだけなのでfirstで良い。
    //     return view('updateForm', compact('post'));
    //     // 情報取得した上でupdateForm.blade.phpを呼び出す
    // }



    // bladeファイルを表示させる際にのみ、相対パスも一緒に記述しなくてはならない。

    // フォロワーの検索に関してはindexのメソッドで使われているものの応用でOK。
    // 厳密にはtableのpostsをusersにすれば良い。

    // フォローの追加、削除に関しては投稿の追加、削除の応用でOK。
    // CRUDのCreate（insert）とDeleteを使う事で成り立つ。


    // 他のユーザーのプロフィール、いったん保留！！
    //     public function otherUserProfile() {
    //     // dd(1);
    //     $user = DB::table('users')
    //     ->where('id','=',Auth::id())
    //     ->select('id','name','image','email','bio')
    //     ->first();

    //     return view('profiles.profile', [ 'user' => $user]);
    // }

}
