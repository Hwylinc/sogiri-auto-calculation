<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpareController extends BaseController
{
    // *******************************************
    // コンストラクタ
    // *******************************************
    public function __construct()
    {
        parent::__construct();
    }
    
    // *******************************************
    // 予備材一覧画面
    // *******************************************
    public function getList($factry_id)
    {
        dd('予備材一覧画面');
    }
    
    // *******************************************
    // 予備材編集画面
    // *******************************************
    public function getEdit($factry_id)
    {
        dd('予備材編集画面');
    }
    
    // *******************************************
    // 編集完了処理
    // *******************************************
    public function postComplete(Request $request)
    {
        dd('編集完了処理');
    }
}
