<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Exception;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $requestStore;

    public function store($request, $exe, $value) 
    {

        $this->requestStore = $request;

        try {
            DB::beginTransaction();
            $reteurn_result =  $this->$exe($request, $value);
            DB::commit();
            return $reteurn_result;
        } catch(Exception $e) {
            DB::rollBack();
            $this->addFlash('error', $e->getMessage());
            return back()->withInput();
        }
    }


    /**
     * view画面に遷移したい場合に利用する
     *
     * @param String $route_name　ルート名
     * @param Array $with   返したい値がある場合に利用
     * @return void
     */
    public function view(String $route_name, Array $with=[]) 
    {
        return view($route_name)->with($with);
    }

    /**
     * flashにメッセージを使用したい場合に利用
     *
     * @param String $messageKbn success:成功　error:エラー
     * @param String $messageTxt　メッセージ内容
     * @return void
     */
    public function addFlash(String $messageKbn, String $messageTxt)
    {
        $sessionStore = $this->requestStore->session();
        $messageKbn = 'message.' . $messageKbn;

        if ($sessionStore->has('$messageKbn')) {
            $sessionStore->push($messageKbn, $messageTxt);
        } else {
            $sessionStore->flash($messageKbn, $messageTxt);
        }
    }

    /**
     * エラーで元画面に戻る時に利用
     *
     * @param [type] $message   メッセージ内容
     * @return void
     */
    public function throwError($message) {
        throw new Exception($message);
    }
}
