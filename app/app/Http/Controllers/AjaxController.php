<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
    // *******************************************
    // 鉄筋情報CSV読み込みアップロード画面
    // *******************************************
    public function postCsvUpload()
    {
        dd('鉄筋情報CSV読み込みアップロード画面');
    }

    // *******************************************
    // 鉄筋入力情報をセッションに保存
    // *******************************************
    public function postRebarUpdate($factry_id, $diameter)
    {
        dd('鉄筋入力情報をセッションに保存');
    }
}
