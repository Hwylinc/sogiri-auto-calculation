<?php

namespace App\Http\Controllers\Traits;


trait CalculatorResultTrait
{

    // *******************************************
    // ユーザーの工場IDに紐づく計算結果番号一覧と紐作り計算依頼番号の取得
    // *******************************************
    private function getCalGroupCodesList(
        $calGroupCalCodeModel
      , $factory_id
    ) {
        $dataArray = [];
        $params['factory_id'] = $factory_id;
        // ユーザーの工場IDに紐づく計算結果番号一覧の取得
        $list = $this->getCalGroupCodesListData($calGroupCalCodeModel, $params);
        if (!empty($list)) {
            foreach ($list as $value) {
                $dataArray[$value['group_code']][$value['group_id']][] = $value;
            }
        }
        return $dataArray;
    }

    /* **************************************** */
    /* ユーザーの工場IDに紐づく計算結果番号一覧の取得
    /* **************************************** */
    private function getCalGroupCodesListData(
        $calGroupCalCodeModel
      , $params
    ) {
        $list = array();
        $list = $calGroupCalCodeModel->getCalGroupCodeByFactIdCondition($params)->get()->toArray();

        return $list;
    }

    // *******************************************
    // 計算結果番号に紐づく例外一覧の取得
    // *******************************************
    public function getExceptionList(
        $calculationResultModel
      , $group_code
    ) {
        $params['group_code'] = $group_code;

        // 計算対象一覧情報を取得
        $calculationResultList = $this->getExceptionListData($calculationResultModel, $params);

        return $calculationResultList;
    }
    /* **************************************** */
    /* 計算結果対象一覧情報を取得
    /* **************************************** */
    private function getExceptionListData(
        $calculationResultModel
      , $params
    ) {
        $list = array();
        $list = $calculationResultModel->getExceptionListCondition($params)->get();

        return $list;
    }

    // *******************************************
    // 計算結果番号に紐づく一覧の取得
    // *******************************************
    public function getCalculationResultList(
        $calculationResultModel
      , $group_code
    ) {
        $params['group_code'] = $group_code;

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

    /* **************************************** */
    /*  例外処理内容を表示用のリストに加工
    /* **************************************** */
    private function convertExceptionDisplayData($exceptionList)
    {
        $exceptionDisplayList = [];
        if (!empty($exceptionList)) {
            foreach ($exceptionList as $key => $exception) {
                // 長さ
                $exceptionDisplayList[$exception['diameter_id']][$key]['length'] = $exception['length'];
                // 切断本数
                $exceptionDisplayList[$exception['diameter_id']][$key]['number'] = $exception['set_number'];
                // 吐き出し口
                $exceptionDisplayList[$exception['diameter_id']][$key]['port'] = $exception['port_id'];
            }
        }
        
        return $exceptionDisplayList;
    }


    /* **************************************** */
    /*  計算済みのコードを取得
    /* **************************************** */
    private function getCalculatedList($calculationResultModel, $codeList)
    {
        $calculatedList = [];
        if (!empty($codeList)) {
            // 計算コード一覧をループ
            foreach ($codeList as $params) {
                // 計算コードに紐づく計算結果があるかどうか確認
                $data = $this->getCalculatedCodeData($calculationResultModel, $params);
                if (!empty($data['code'])) {
                    // 計算コードに紐づく計算結果があればそのコードをリストに追加
                    $calculatedList[] = $data['code'];
                }
            }
        }
        return $calculatedList;
    }    
    
    /* **************************************** */
    /* 計算済みのコード情報を取得
    /* **************************************** */
    private function getCalculatedCodeData(
        $calculationResultModel
      , $params
    ) {
        $data = $calculationResultModel->getCalculationResultListCondition($params)->first(['code']);
        
        return $data;
    }

    /* **************************************** */
    /* 計算結果に紐づく計算依頼コード一覧を取得する
    /* **************************************** */
    private function getCalGroupCalCodeList(
        $calGroupCalCodeModel
      , $group_code
    ) {
        $data = [];
// $group_code = 'sadfasdfa';
        $data = $calGroupCalCodeModel->where('group_code', $group_code)->get()->toArray();

        return $data;
    }

    /* **************************************** */
    /* 計算依頼コード情報一覧を取得
    /* **************************************** */
    private function calculationRequestCodes(
        $calculationCodeModel
      , $calGroupCalCodeList
      , $factory_id
    ) {
        // 検索に使用するパラメータ作成
        $params['factory_id'] = $factory_id;
        $params['calculation_status'] = 1;

        if (!empty($calGroupCalCodeList)) {
            foreach ($calGroupCalCodeList as $value) {
                $params['code'] = $value['code'];
                $temp_data = $this->calculationRequestCodesData($calculationCodeModel, $params);
                // 紐づく情報がない場合はエラー画面を表示
                if (empty($temp_data)) {
                    abort(403, '別工場の情報です。閲覧権限がありません。');
                } else {
                    $calculationRequestCodeList[$value['code']] = $temp_data->toArray();
                }
            }
        }

        return $calculationRequestCodeList;
    }

    /* **************************************** */
    /* 計算依頼コードに紐づく依頼情報一覧を取得
    /* **************************************** */
    private function calculationRequestCodesData(
        $calculationCodeModel
      , $params
    ) {
        $data = $calculationCodeModel->getCalculationRequestListCondition($params)->first();

        return $data;
    }
    
    /* **************************************** */
    /*  計算依頼を表示用のリストに加工
    /* **************************************** */
    private function convertRequestDisplayData($calculationRequestList)
    {
        $requestDisplayList = [];
        if (!empty($calculationRequestList)) {
            foreach ($calculationRequestList as $key => $value) {         
                $requestDisplayList[$value['code']][$value['diameter_id']][$value['component_name']][$key]['length'] = $value['requests_length'];
                $requestDisplayList[$value['code']][$value['diameter_id']][$value['component_name']][$key]['number'] = $value['number'];
                $requestDisplayList[$value['code']][$value['diameter_id']][$value['component_name']][$key]['port'] = $value['port_id'];
            }
        }
        return $requestDisplayList;
    }
}
