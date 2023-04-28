<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    // 下位管理者（三田）が閲覧できないページ（要確認）
    private $sandaAdminHidingRoute = [
    ];
    // 作業者が閲覧できないページ
    private $userHidingRoute = [  
          'rebar.list'          // 鉄筋情報一覧画面
        , 'rebar.select'        // 鉄筋情報入力方法・工場選択画面
        , 'rebar.register'      // 鉄筋情報手入力画面
        , 'rebar.confirm'       // 鉄筋情報手入力確認画面
        , 'rebar.edit'          // 鉄筋情報手入力編集画面
        , 'rebar.csv-register'  // 鉄筋情報CSVアップロード画面
        , 'rebar.csv-upload'    // 鉄筋情報CSV読み込みアップロード画面
        , 'rebar.csv-confirm'   // 鉄筋情報CSVアップロード確認画面
        , 'rebar.done'          // 鉄筋情報登録・編集完了画面
        , 'rebar.complete'      // 鉄筋情報登録・編集完了処理
        , 'rebar.detail'        // 鉄筋情報詳細画面
        , 'calculate.ready'     // 計算開始確認画面
        , 'calculate.confirm'   // 計算結果確認画面
        , 'calculate.complete'  // 計算結果登録処理
        , 'calculate.done'      // 計算結果完了画面
        , 'spare.list'          // 工場別予備材一覧
        , 'spare.edit'          // 予備材編集画面
        , 'spare.complete'      // 予備材編集完了処理
    ];

    // *******************************************
    // コンストラクタ
    // *******************************************
    public function __construct()
    {
        // 権限別表示処理
        $this->middleware(function ($request, $next) {
            // 使用ユーザー情報取得
            $user = Auth::user();
            // 権限別の表示可否
            if ($user['authority'] == 1) { // 上位管理者
                return $next($request);
            } else if ($user['authority'] == 2) {
                return $next($request); // 下位管理者(三田の下位管理者がアクセス制限されるみたいなので要確認)
            } else if ($user['authority'] == 3) { // 作業員
                $this->checkHidingRoute($this->userHidingRoute); 
            } else {
                abort(404, '閲覧権限がありません。');
            }
        });
    }

    // *******************************************
    // 表示可否確認処理
    // *******************************************
    public function checkHidingRoute($hidingRoute)
    {
        $setValue = true;
        // ルート名を取得
        $routeName = Route::currentRouteName();

        // 該当ページと一致すれば非表示
        if (in_array($routeName, $hidingRoute)) {
            $setValue = false;
        }
        if (!$setValue) {
            abort(404, '閲覧権限がありません。');
        }
        return;
    }
}