<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diameter;
use App\Models\CalculationResult;
use App\Models\CalculationRequest;
use App\Http\Controllers\Traits\DiameterTrait;
use App\Http\Controllers\Traits\CalculatorTrait;
use App\Http\Controllers\Traits\CalculatorResultTrait;
use Illuminate\Support\Facades\DB;

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
        CalculationRequest $calculationRequestModel
     , $calculation_id
    ) {
        // 計算対象一覧の取得
        $calculationRequestList = $this->getCalculationRequestList($calculationRequestModel, $calculation_id);
        
        if ( empty($calculationRequestList) ) {
            abort(403, '計算番号が不正な値です');
        }

        return view('calculator.ready', [
            'calculation_id' => $calculation_id
        ]);
    }
    
    // *******************************************
    // 計算結果登録処理
    // *******************************************
    public function getCaliculationStart(
        CalculationRequest $calculationRequestModel
      , CalculationResult $calculationResultModel
      , Diameter $diameterModel
      , $calculation_id
    ) {
        // 計算対象一覧の取得
        $calculationRequestList = $this->getCalculationRequestList($calculationRequestModel, $calculation_id);
        if ( empty($calculationRequestList) ) {
            abort(403, '計算番号が不正な値です');
        }
        // 計算処理開始（計算結果の配列取得）
        $calculationList = $this->getCalculationList($calculationRequestList);
        // 計算結果登録用データに加工
        $convertData = $this->convertCalculationData($diameterModel, $calculationList, $calculation_id);

        // DBトランザクション開始
        DB::beginTransaction();
        
        try {
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


        return redirect()->route('calculate.complete',['calculation_id'=> $calculation_id]);
    }
        
    // *******************************************
    // 計算結果完了画面
    // *******************************************
    public function getComplete(
        CalculationResult $calculationResultModel
      , Diameter $diameterModel
      , $calculation_id
      , $diameter_id = null
    ) {
        // 鉄筋径一覧取得
        $diameterList = $diameterModel->all();
        // 表示用のリストに加工
        $diameterDisplayList = $this->convertDiameterDisplayData($diameterList);
        
        $diameter_id = empty($diameter_id) ? $diameterList[0]['id'] : $diameter_id;        
        // 紐づく計算結果を取得
        $calculationResultList = $this->getCalculationResultList($calculationResultModel, $calculation_id);
        // 表示用のリストに加工
        $resultDisplayList = $this->convertResultDisplayData($calculationResultList);

        return view('calculator.complete', [
            'resultDisplayList'   => $resultDisplayList
          , 'diameterDisplayList' => $diameterDisplayList
          , 'calculation_id'      => $calculation_id
          , 'diameter_id'         => $diameter_id 
        ]);    
    }
    
    // *******************************************
    // 計算結果履歴一覧画面
    // *******************************************
    public function getList()
    {
        dd('計算結果履歴一覧画面');
    
    }
    
    // *******************************************
    // 計算結果詳細画面
    // *******************************************
    public function getDetail($calculation_id)
    {
        dd('計算結果詳細画面');
    
    }
}
