<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Auth;

trait CalculatorTrait
{
    // *******************************************
    // 計算対象のコード一覧取得
    // *******************************************
    public function getCalculationRequestCodeList(
        $calculationCodeModel
      , $factory_id
    ) {
        // 検索に使用するパラメータ設定
        $params['factory_id'] = $factory_id;
        $params['calculation_status'] = config('const.calculation_status_id.mikeisan');

        // 計算対象一覧情報を取得
        $calculationRequestCodeList = $this->getCalculationRequestCodeListData($calculationCodeModel, $params);

        return $calculationRequestCodeList;
    }

    /* **************************************** */
    /* 計算対象のコード一覧情報を取得
    /* **************************************** */
    private function getCalculationRequestCodeListData(
        $calculationCodeModel
      , $params
    ) {
        $list = array();
        $list = $calculationCodeModel->getCalculationRequestListCondition($params)->get();

        return $list;
    }

    /* **************************************** */
    /* 予備材一覧情報を取得
    /* **************************************** */
    private function getSpareList($spareModel) {
        $list = [];
        $spareList = $spareModel->getSpareListCondition()->get()->toArray();    
        if (!empty($spareList)) {
            foreach ($spareList as $spare) {
                $list['D'.$spare['size']][] = intval($spare['name']);
            }
        }
        return $list;
    }


    // *******************************************
    // 計算対象一覧の取得
    // *******************************************
    public function getCalculationRequestList(
        $calculationRequestModel
      , $calculationRequestCodeList
    ) {
        foreach ($calculationRequestCodeList as $key => $value) {
            $params[]['code'] = $value['code'];
        }

        // 計算対象一覧情報を取得
        $calculationRequestList = $this->getCalculationRequestListData($calculationRequestModel, $params);

        return $calculationRequestList;
    }

    /* **************************************** */
    /* 計算対象一覧情報を取得
    /* **************************************** */
    private function getCalculationRequestListData(
        $calculationRequestModel
      , $params
    ) {
        $list = array();
        $list = $calculationRequestModel->getCalculationRequestListCondition($params)->get();

        return $list;
    }

    /* **************************************** */
    /*  計算処理開始（計算結果の配列取得）
    /* **************************************** */
    public function getCalculationList(
        $spareList
      , $calculationRequestList
    ) {
        $data = array();
        $calculationList = array();
        $exception = array();
        // 例外処理で使う長さの一覧を取得
        $exception_lengths = [4500, 5000, 5500, 6000, 6500, 7000, 7500, 8000];
        foreach ($calculationRequestList as $value) {
            if (in_array($value['requests_length'], $exception_lengths)) {
                // 例外処理に渡す値
                // 既に値がある場合は追加する
                if ( isset($exception['D'.$value['size']][$value['port_id'].'-'.$value['requests_length']]) ) {
                    $exception['D'.$value['size']][$value['port_id'].'-'.$value['requests_length']] = $exception['D'.$value['size']][$value['port_id'].'-'.$value['requests_length']] + $value['number'];
                } else {
                    $exception['D'.$value['size']][$value['port_id'].'-'.$value['requests_length']] = $value['number'];
                }            
            } else {
                // 既に値がある場合は追加する
                if ( isset($exception['D'.$value['size']][$value['port_id'].'-'.$value['requests_length']]) ) {
                    $data['D'.$value['size']][$value['port_id'].'-'.$value['requests_length']] = $data['D'.$value['size']][$value['port_id'].'-'.$value['requests_length']] + $value['number'];
                } else {
                    $data['D'.$value['size']][$value['port_id'].'-'.$value['requests_length']] = $value['number'];
                }
            }
        }

        $dataArray = [];        
        foreach ($data as $size => $temp_array)
        {
            // D10/13/16以外は対応しない
            if ($size == 'D10' || $size == 'D13' || $size == 'D16') {
                // 各数字の約数の一覧を格納する配列
                $divisors = [];
                // 存在する約数の一覧を格納する配列
                $exist_divisors = [];
                // 13以上25以下の素数の一覧を取得
                $prime_numbers = [13, 17, 19, 23];
                // 紐づく物理最大数を取得
                $max_limit = $this->getMaxNumber($size);
                // Numが物理幅以上の場合は２５の倍数とあまりに分ける
                // 後で約数は25以下にしかとらないとかにしとく
                $array = $this->getAdjustedByNum($max_limit, $temp_array);
                // 素数をマイナス１した数値と１に分ける
                $array = $this->getAdjustedByPrimeNum($prime_numbers, $array);
                // 各数字に対して約数を取得し、$divisors に格納する
                foreach ($array as $length => $num) {
                    $divisors[$length] = $this->getDivisors($max_limit, $num);
                    // 重複なく約数を統合する
                    $exist_divisors = array_unique(array_merge($exist_divisors, $divisors[$length]));
                }

                // 約数一覧を降順
                rsort($exist_divisors);
                // 共通する約数を持つ数字をグループ化するための配列
                $groups = [];
                if (!empty($exist_divisors)) {
                    // 存在する約数をループ
                    foreach ($exist_divisors as $n) {
                        // 各数字の約数の一覧に現在回している約数が含まれている確認するループ
                        foreach ($divisors as $key => $divisor) {
                            if (array_key_exists($n, $divisor)) {
                                $groups[$n][$key] = $divisor[$n];
                                // unset($divisors[$key]);
                            }
                        }
                        // group内に一つしかなければ共通約数ではないのでグループにはしない
                        if(array_key_exists($n, $groups)) {
                            if (count($groups[$n]) <= 1) {
                                unset($groups[$n]);
                            } else {
                                // 複数ある場合は共通約数として括り、以後のループにその数値は考慮されないように除外する
                                foreach (array_keys($groups[$n]) as  $value) {
                                    unset($divisors[$value]);
                                }
                            }
                        }
                    }
                }
                $dataArray = $this->getGroupResult($size, $groups, $array, $dataArray);
            }
        }
        // 鉄筋径別に同時切断設定
        foreach ($dataArray as $size => $array) {
            foreach ($array as $cutting_num => $arr) {
                // 切断りストをリセット
                $lengths = array();
                $count = 0;
                foreach ($arr as $port_length => $amount) {
                    $temp = explode("-",$port_length);
                    // 切断するリストに入れる
                    for ($i=0; $i < $amount; $i++) { 
                        $lengths['port'][$count]   = $temp[0];
                        $lengths['length'][$count] = $temp[1];
                        $count++;
                    }
                }
                // 切断指示作成
                $calculationList['target'][$size][$cutting_num] = $this->getCombination($spareList, $lengths, $size);                
            }            
        }
        
        $exceptionArray = [];
        // 例外処理に値があれば同時切断０の配列に変更
        if (!empty($exception)) {
            foreach ($exception as $size => $temp_array) {
                foreach ($temp_array as $key => $requiredNumber) {
                    $exceptionArray[$size][$requiredNumber][$key] = $requiredNumber;
                }
            }
        }       
        // 例外処理に同時切断数でまとめる
        foreach ($exceptionArray as $size => $array) {
            foreach ($array as $requiredNumber => $arr) {
                foreach ($arr as $port_length => $amount) {
                    $temp = explode("-",$port_length);
                    
                    $calculationList['exception'][$size][$requiredNumber][0]['port'][]   = $temp[0];
                    $calculationList['exception'][$size][$requiredNumber][0]['length'][] = $temp[1];
                }
            }            
        }
        return $calculationList;
    }

    public function getAdjustedByNum($max_limit, $temp_array)
    {
        $array = [];
        foreach ($temp_array as $length => $num) {
            if($num > $max_limit) {
                // 本数を物理制限数で割ったときの商
                $multiplication = number_format(floor($num / $max_limit), 0);
                $multiple_num = $multiplication*$max_limit;
                
                $array[$length.'_adjusted1'] = $multiple_num;
                $array[$length.'_adjusted2'] = $num - $multiple_num;
            } else {
                $array[$length] =$num;
            }
        }
        return $array;
    }

    /*
    *
    * 素数をマイナス１した数値と１に分ける
    *
    */
    public function getAdjustedByPrimeNum($prime_numbers, $array)
    {
        $data = [];
        // 素数から引く数
        $subtrahend = 1;
        foreach ($array as $length => $num) {
            // 素数かどうか確認
            if (in_array($num, $prime_numbers)) {
                // 素数の場合、定められた数値（現状では１）を引いて素数でなくす
                $non_prime_num = $num - $subtrahend;
                // 定められた数値（現状では１）と元の数値からそれを引いたものに分けて配列に登録
                $data[$length.'_adjusted3'] = $non_prime_num;
                $data[$length.'_adjusted4'] = $subtrahend;
            } else {
                $data[$length] = $num;
            }
        }
        return $data;
    }



    // 約数を取得する関数
    public function getDivisors($max_limit, $n) 
    {
        $divisors = [];
        for ($i = 1; $i <= sqrt($n); $i++) {
            if ($n % $i == 0) {
                // $i が約数である場合
                $temp_divisors[] = $i;
                // sqrt($n) と $i の商も約数である場合、重複しないように追加する
                if ($n / $i != $i) {
                    $temp_divisors[] = $n / $i;
                }
            }
        }
        // 小さい順にソートしてから返す
        if (!empty($temp_divisors)) {
            sort($temp_divisors);
            foreach ($temp_divisors as $num) {
                // 返す約数の最大値は物理許容本数
                if ($num <= $max_limit) {
                    $divisors[$num] = $num;
                }
            }
        }
        return $divisors;
    }

    // 約数グループ分け結果出力
    public function getGroupResult($size, $groups, $arr, $dataArray)
    {
        $data = $dataArray;
        foreach ($groups as $divisor => $array) {
            
            foreach ($array as $temp_length => $number) {
                $times = $arr[$temp_length]/$divisor;
                
                $length = explode('_',$temp_length)[0];
                // 既に値がある場合は追加する
                if ( isset($data[$size][$divisor][$length]) ) {
                    $data[$size][$divisor][$length] = $data[$size][$divisor][$length] + $times;
                } else {
                    $data[$size][$divisor][$length] = $times;
                }
            }
        }

        return $data;
    }

     /*
     * 最適な組み合わせを
    */
    public function getCombination(
        $spareList
      , $lengths
      , $size
    ) {
        $data = [];
        $port_list = $lengths['port'];
        $cut_list  = $lengths['length'];
        // shuffle($cut_list);
        // 鉄筋径ごとの生材の長さを取得（8000の場所をこの変数に後程置き換え）
        // $material_length =
        
        $rods = [8000];
        $cuts = [];

        // 予備材リスト
        $spare_cut_list =$this->getSpareBySize($spareList, $size);

        while (count($cut_list) > 0) {
            $best_cut_index = -1;
            $best_cut_waste = 8000;
            for ($i = 0; $i < count($rods); $i++) {
                $rod = $rods[$i];
                for ($j = 0; $j < count($cut_list); $j++) {
                    $cut = $cut_list[$j];
                    $port_id = $port_list[$j];
                    if ($cut > $rod) continue;
                    $waste = $rod - $cut;
                    if ($waste < $best_cut_waste) {
                        $best_cut_waste = $waste;
                        $best_cut_index = $j;
                        $best_cut_rod = $i;
                    }
                    if ($best_cut_waste == 0) break;
                }
                if ($best_cut_waste == 0) break;
            }

            if ($best_cut_index == -1) {
                // Need a new rod
                $rods[] = 8000;
                continue;
            }

            $cut = $cut_list[$best_cut_index];
            $cuts[] = [$cut, $best_cut_rod, $port_id];
            $rods[$best_cut_rod] -= $cut;
            array_splice($cut_list, $best_cut_index, 1);
        }
        $result = array();
        $count = 0;
        
        foreach ($cuts as $i => $cut) {
            // dd($cut);
            if ($count != $cut[1]+1) {
                if($i != 0) {
                    $left = 8000 - $result[$cut[1]];
                    //　予備材を使って端材を最小化
                    if (!empty($spare_cut_list)) {
                        $used_spare_list = $this->cutLeftWithSpare($left, $spare_cut_list);
                        if (!empty($used_spare_list['used'])) {
                            foreach ($used_spare_list['used'] as $key => $value) {
                                $data[$cut[1]]['length'][] = $value;
                                $data[$cut[1]]['port'][] = 0;
                            }
                        }
                    }
                    // $data[$cut[1]]['waste'] = $used_spare_list['waste'];
                }
                $data[$cut[1]+1]['length'][] = $cut[0];
                $data[$cut[1]+1]['port'][] = $cut[2];
                $count = $cut[1]+1;
            } else {
                $data[$cut[1]+1]['length'][] = $cut[0];
                $data[$cut[1]+1]['port'][] = $cut[2];
            }
            if (empty($result[$cut[1]+1])) {
                $result[$cut[1]+1] = $cut[0];
            } else {
                $result[$cut[1]+1] = $cut[0]+$result[$cut[1]+1];
            }
        }        
        
        $left = 8000 - end($result);
        //　予備材を使って端材を最小化
        if (!empty($spare_cut_list)) {
            $used_spare_list = $this->cutLeftWithSpare($left, $spare_cut_list);
            if (!empty($used_spare_list['used'])) {
                foreach ($used_spare_list['used'] as $key => $value) {
                    $data[$cut[1]+1]['length'][] = $value;
                    $data[$cut[1]+1]['port'][] = 0;
                }
            }
        }
        

        // $data[$cut[1]+1]['waste'] = $used_spare_list['waste'];
        return $data;
    }


    /* **************************************** */
    /*  鉄筋径別の予備材リストを取得
    /* **************************************** */
    public function getSpareBySize($spareList, $size)
    {
        $list = [];
        if ( array_key_exists($size, $spareList) ) {
            $list = $spareList[$size];
        }
        return $list;
    }    
    
    /* **************************************** */
    /*  紐づく物理最大数を返す
    /* **************************************** */
    private function getMaxNumber($size)
    {
        // 鉄筋径別、同時に切断可能な最大数
        $max_limit['D10'] = 25;
        $max_limit['D13'] = 19;
        $max_limit['D16'] = 15;
        $max_limit['D19'] = 12;
        $max_limit['D22'] = 10;
        
        return $max_limit[$size];
    }

    /* **************************************** */
    /* 予備材リストを用いた端材の切断結果を取得
    /* **************************************** */
    public function findCutList($left_length, $spare_cut_list) 
    {
        // $spare_cut_listを昇順にソートする
        sort($spare_cut_list); 
        // $spare_cut_listの数を取得する
        $cut_count = count($spare_cut_list);
        // $spare_cut_listの要素数分、0で初期化された配列を作成する
        $cut_result = array_fill(0, $cut_count, 0); 
        while ($left_length > 0) { // left_lengthが0になるまで繰り返す
            $i = $cut_count - 1;
            while ($i >= 0 && $spare_cut_list[$i] > $left_length) { // left_lengthよりも大きい$spare_cut_listの要素を探す
                $i--;
            }
            if ($i < 0) { // left_lengthよりも小さい$spare_cut_listの要素がない場合、処理を中断する
                break;
            }
            $cut_result[$i]++; // 切断する$spare_cut_listの要素のカウントを1増やす
            $left_length -= $spare_cut_list[$i]; // 切断した分、left_lengthを減らす
        }

        return $cut_result; // 切断結果を返す
    }


    // *******************************************
    // 予備材リストを用いた端材の切断結果をテスト出力
    // *******************************************
    public function cutLeftWithSpare($left, $spare_cut_list)
    {
        $return_data = [];
        $used_spare_result = $this->findCutList($left, $spare_cut_list);
        if ($used_spare_result === false) {
            
        } else {
            sort($spare_cut_list); 

            $cutCount = count($spare_cut_list);
            $waste = $left;
            for ($j = 0; $j < $cutCount; $j++) {
                if ($used_spare_result[$j] > 0) {
                    $return_data['used'][] = $spare_cut_list[$j];
                    $waste -= $spare_cut_list[$j] * $used_spare_result[$j];
                }
            }
            $return_data['waste'] = $waste;
        }

        return $return_data;
    }

    // *******************************************
    // DB登録用に入力情報を変換
    // *******************************************
    private function convertCalculationData(
        $diameterModel
      , $calculationList
      , $resultGroupCode
    ) {
        $convertData = [];
        $count = 0;
        $times = [];
        
        // 鉄筋径ごとにループ
        if (!empty($calculationList)) {
            foreach ($calculationList as $target => $targetValue) {
                foreach ($targetValue as $size => $setNumberArray) {
                    $sizeInt = (int)ltrim($size, 'D');
                    $diameterId = $diameterModel->where('size', $sizeInt)->first(['id']);            
                    $cutTimes[$sizeInt] = 1;
                    // 同時切断数ごとにループ
                    if (!empty($setNumberArray)) {
                        foreach ($setNumberArray as $setNumber => $setArray) {
                            // セット数ごとにループ
                            if (!empty($setArray)) {
                                foreach ($setArray as $times => $combination) {
                                    if (!empty($combination['length'])) {
                                        foreach ($combination['length'] as $preCuttingOrder => $length) {
                                            $convertData[$count]['group_code']    = $resultGroupCode;                         //計算依頼グループID   
                                            $convertData[$count]['diameter_id']   = $diameterId['id'];                        //鉄筋径ID
                                            if ($target == 'exception') {
                                                $convertData[$count]['times']         = 0;                                    //切断順番
                                                $convertData[$count]['cutting_order'] = 0;                                    //切断順番 
                                            } else {
                                                $convertData[$count]['times']         = $cutTimes[$sizeInt];                  //切断順番
                                                $convertData[$count]['cutting_order'] = $preCuttingOrder + 1;                 //切断順番 
                                            }
                                            $convertData[$count]['length']        = $length;                                  //長さ 
                                            $convertData[$count]['set_number']    = $setNumber;                               //同時切断セット本数	  
                                            $convertData[$count]['port_id']       = $combination['port'][$preCuttingOrder];   //吐出口ID 
                                            
                                            $count++;
                                        }
                                    }
                                    $cutTimes[$sizeInt]++;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $convertData;
    }

    // *******************************************
    // 計算データをDBに登録
    // *******************************************
    private function createCalculation(
        $calculationResultModel
      , $convertData
    ) {
        foreach ($convertData as $insertData) {
            // 計算データをDBに登録
            $calculationResultModel->create($insertData);
        }
    }

    // *******************************************
    // 計算グループをDBに登録
    // *******************************************
    private function createCalculationGroup(
        $calculationGroupModel
      , $resultGroupCode
    ) {
        $insertData['group_code'] = $resultGroupCode;

        // 計算グループをDBに登録
        $calculationGroupModel->create($insertData);
    }


    // *******************************************
    // 中間テーブルをDBに登録
    // *******************************************
    private function createCalGroupCalCode(
        $calGroupCalCodeModel
      , $calculationRequestCodeList
      , $resultGroupCode
    ) {
        if (!empty($calculationRequestCodeList)) {
            foreach ($calculationRequestCodeList as $key => $value) {
                $convertData[$key]['code'] = $value['code'];
                $convertData[$key]['group_code'] = $resultGroupCode;
            }
            foreach ($convertData as $insertData) {
                // 中間テーブルDBに登録
                $calGroupCalCodeModel->create($insertData);
            }
        }
    }



    /* **************************************** */
    /*  計算結果グループコード（計算結果登録に使用する）
    /* **************************************** */
    public function getResultGroupCode(
        $calculationGroupModel
    ) {
        // 重複判定フラグ
        $unique_flg = false;
        while (!$unique_flg) {
            // 新規のコード作成
            $resultGroupCode = uniqid();
            $calculationGroup = $calculationGroupModel->where('group_code', $resultGroupCode)->first();
            // 同じコードが設定されたものがないかの確認
            if (empty($calculationGroup)) {
                // 重複がない場合はループを抜ける
                $unique_flg = true;
            }
        }
        
        return $resultGroupCode;
    }
    
    // *******************************************
    // 計算コードの中の計算ステータスを変更する
    // *******************************************
    private function updateCodeStatus(
        $calculationCodeModel
      , $calculationRequestCodeList
    ) {
        $params['calculation_status'] = config('const.calculation_status_id.keisanzumi');
        foreach ($calculationRequestCodeList as $codeInfo) {
            $calculationCodeModel->where('code', $codeInfo['code'])->update(['calculation_status' => $params['calculation_status']]);
        }
    }
}
