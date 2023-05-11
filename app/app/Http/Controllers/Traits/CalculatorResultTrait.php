<?php

namespace App\Http\Controllers\Traits;


trait CalculatorResultTrait
{

    // *******************************************
    // 計算番号に紐づく一覧の取得
    // *******************************************
    public function getCalculationResultList(
        $calculationResultModel
      , $calculation_id
    ) {
        // 工場IDが必要になったらここで取得
        // $params['factory_id'] = Auth::user()->id;
        $params['code'] = $calculation_id;

        // 計算対象一覧情報を取得
        $calculationResultList = $this->getCalculationResultListData($calculationResultModel, $params);

        return $calculationResultList;
    }

    /* **************************************** */
    /* 計算対象一覧情報を取得
    /* **************************************** */
    private function getCalculationResultListData(
        $calculationResultModel
      , $params
    ) {
        $list = array();
        $list = $calculationResultModel->getCalculationResultListCondition($params)->get();

        return $list;
    }

    /* **************************************** */
    /*  表示用のリストに加工
    /* **************************************** */
    private function convertResultDisplayData($calculationResultList)
    {
        $resultDisplayList = [];
        if (!empty($calculationResultList)) {
            foreach ($calculationResultList as $calculation) {
                // 鉄筋径→カット回数順→切断順番
                // 長さ
                $resultDisplayList[$calculation['diameter_id']][$calculation['times']]['data'][$calculation['cutting_order']]['length'] = $calculation['length'];
                // 切断本数
                $resultDisplayList[$calculation['diameter_id']][$calculation['times']]['data'][$calculation['cutting_order']]['number'] = $calculation['set_number'];
                // 吐き出し口
                $resultDisplayList[$calculation['diameter_id']][$calculation['times']]['data'][$calculation['cutting_order']]['port'] = $calculation['port_id'];

                // 端材の長さ管理
                if (empty($resultDisplayList[$calculation['diameter_id']][$calculation['times']]['left'])) {
                    $resultDisplayList[$calculation['diameter_id']][$calculation['times']]['left'] = 8000 - $calculation['length'];
                } else {
                    $resultDisplayList[$calculation['diameter_id']][$calculation['times']]['left'] = $resultDisplayList[$calculation['diameter_id']][$calculation['times']]['left'] - $calculation['length'];
                }
            }
        }
        
        return $resultDisplayList;
    }
}
