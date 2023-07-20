<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spare;
use App\Models\Diameter;
use App\Models\CalculationCode;
use App\Models\CalculationGroup;
use App\Models\CalculationResult;
use App\Models\CalculationRequests;
use App\Models\CalculationGroupCalculationCode;
use App\Http\Controllers\Traits\DiameterTrait;
use App\Http\Controllers\Traits\CalculatorTrait;
use App\Http\Controllers\Traits\CalculatorResultTrait;
use Illuminate\Support\Facades\DB;
use Auth;

class CalculatorController extends BaseController
{
    use DiameterTrait;
    use CalculatorTrait;
    use CalculatorResultTrait;
    // *******************************************
    // コンストラクタ
    // *******************************************
    public function __construct() {
        parent::__construct();
    }
    
    
    // *******************************************
    // 計算開始確認画面
    // *******************************************
    public function getReady(
        CalculationCode $calculationCodeModel
    ) {
        // BaseControllerでログインは確認済み
        $user = Auth::user();
        //  userに紐づく工場
        $factory_id = $user['factory_id'];

        //  userに紐づく工場の依頼中（未計算）コードの情報一覧取得
        $calculationRequestCodeList = $this->getCalculationRequestCodeList($calculationCodeModel, $factory_id);

        return view('calculator.ready', [
            'calculationRequestCodeList' => $calculationRequestCodeList
        ]);
    }
    
    // *******************************************
    // 計算結果登録処理
    // *******************************************
    public function getCaliculationStart(
        CalculationCode $calculationCodeModel
      , CalculationGroup $calculationGroupModel
      , CalculationRequests $calculationRequestModel
      , CalculationResult $calculationResultModel
      , CalculationGroupCalculationCode $calGroupCalCodeModel
      , Diameter $diameterModel
      , Spare $spareModel
    ) {
        // BaseControllerでログインは確認済み
        $user = Auth::user();
        //  userに紐づく工場
        $factory_id = $user['factory_id'];

        //  userに紐づく工場の依頼中（未計算）コードの情報一覧取得
        $calculationRequestCodeList = $this->getCalculationRequestCodeList($calculationCodeModel, $factory_id);

        // 計算対象一覧の取得
        if ($calculationRequestCodeList->isNotEmpty()) {
            $calculationRequestList = $this->getCalculationRequestList($calculationRequestModel, $calculationRequestCodeList);
        }
        if ( empty($calculationRequestList) ) {
            abort(403, 'エラーが発生しました。');
        }
        // 予備材一覧取得
        $spareList = $this->getSpareList($spareModel);
        // 計算処理開始（計算結果の配列取得）
        $calculationList = $this->getCalculationList($spareList, $calculationRequestList);

        // 計算結果グループコード（計算結果登録に使用する）
        $resultGroupCode = $this->getResultGroupCode($calculationGroupModel);
        // 計算結果登録用データに加工
        $convertData = $this->convertCalculationData($diameterModel, $calculationList, $resultGroupCode);

        // DBトランザクション開始
        DB::beginTransaction();   
        try {
            // 計算コードの中の計算ステータスを未計算から計算済みに変更する
            $this->updateCodeStatus($calculationCodeModel, $calculationRequestCodeList);
            // 計算グループ作成
            $this->createCalculationGroup($calculationGroupModel, $resultGroupCode);
            // 中間テーブル作成
            $this->createCalGroupCalCode($calGroupCalCodeModel, $calculationRequestCodeList, $resultGroupCode);     
            // 計算結果保存
            $this->createCalculation($calculationResultModel, $convertData);
            // DBにコミット
            DB::commit();

        } catch (\Exception $e) {
            // 失敗時はDBロールバック
            DB::rollback();
            // 失敗時はエラー表示
            abort(403, $e);
        }

        return redirect()->route('calculate.complete',['group_code'=> $resultGroupCode]);
    }
        
    // *******************************************
    // 計算結果完了画面
    // *******************************************
    public function getComplete(
      $group_code
    ) {
        return view('calculator.complete', [
            'group_code'   => $group_code
        ]);    
    }
    
    // *******************************************
    // 計算結果履歴一覧画面
    // *******************************************
    public function getList(
        CalculationGroupCalculationCode $calGroupCalCodeModel
      , CalculationCode $calculationCodeModel
    ) {
        // BaseControllerでログインは確認済み
        $user = Auth::user();
        //  userに紐づく工場
        $factory_id = $user['factory_id'];

        // ユーザーの工場IDに紐づく計算結果番号一覧と紐作り計算依頼番号の取得
        $calculationGroupCodes = $this->getCalGroupCodesList($calGroupCalCodeModel, $factory_id);

        return view('calculator.list', [
            'calculationGroupCodes'   => $calculationGroupCodes
        ]);    
    
    }
    
    // *******************************************
    // 計算結果詳細画面
    // *******************************************
    public function getDetail(
        CalculationCode $calculationCodeModel
      , CalculationGroup $calculationGroupModel
      , CalculationRequests $calculationRequestModel
      , CalculationResult $calculationResultModel
      , CalculationGroupCalculationCode $calGroupCalCodeModel
      , Diameter $diameterModel
      , $group_code
      , $page_tab = null
      , $calculation_id = null
      , $diameter_id = null
    ) {
        // BaseControllerでログインは確認済み
        $user = Auth::user();
        //  userに紐づく工場
        $factory_id = $user['factory_id'];

        // 現在開いているタブを取得
        $page_tab = empty($page_tab) ? 'result' : $page_tab;
        
        // 鉄筋径一覧取得
        $diameterList = $diameterModel->all();
        // 鉄筋径を表示用のリストに加工
        $diameterDisplayList = $this->convertDiameterDisplayData($diameterList);
        // 選択されている鉄筋径の値
        $diameter_id = empty($diameter_id) ? $diameterList[0]['id'] : $diameter_id;
        $diameter_length = Diameter::get_by_id($diameter_id)['length'];
        
        // 計算結果に紐づく計算依頼コードを取得する
        $calGroupCalCodeList = $this->getCalGroupCalCodeList($calGroupCalCodeModel, $group_code);

        // 選択されている計算番号の値
        $calculation_id = empty($calculation_id) ? $calGroupCalCodeList[0]['code'] : $calculation_id;
        // 計算依頼コード情報一覧を取得
        $calculationRequestCodeList = $this->calculationRequestCodes($calculationCodeModel, $calGroupCalCodeList, $factory_id);     
        // 紐づく計算依頼情報一覧を取得
        $calculationRequestList = $this->getCalculationRequestList($calculationRequestModel, $calculationRequestCodeList);
        //　依頼情報一覧を表示用リストに加工 
        $calculationRequestDisplayList = $this->convertRequestDisplayData($calculationRequestList);
       
        // 紐づく計算結果一覧を取得
        $calculationResultList = $this->getCalculationResultList($calculationResultModel, $group_code);
        // 紐づく計算結果が存在するか確認
        // if ($calculationResultList->isEmpty()) {
        //     abort(403, '該当する結果がありません。計算結果番号を確認してください。');
        // }
        // 計算結果一覧を表示用のリストに加工
        $resultDisplayList = $this->convertResultDisplayData($calculationResultList);
        // 例外処理の一覧を取得
        $exceptionList = $this->getExceptionList($calculationResultModel, $group_code);
        // 例外処理の一覧を表示用のリストに加工
        $exceptionDisplayList = $this->convertExceptionDisplayData($exceptionList);

        return view('calculator.detail', [
            'resultDisplayList'              => $resultDisplayList
          , 'calculationRequestDisplayList'  => $calculationRequestDisplayList 
          , 'diameterDisplayList'            => $diameterDisplayList
          , 'exceptionDisplayList'           => $exceptionDisplayList
          , 'calculationRequestCodeList'     => $calculationRequestCodeList
          , 'group_code'                     => $group_code
          , 'page_tab'                       => $page_tab 
          , 'calculation_id'                 => $calculation_id
          , 'diameter_id'                    => $diameter_id 
          , 'diameter_length'                => $diameter_length
        ]);   
    }
}
