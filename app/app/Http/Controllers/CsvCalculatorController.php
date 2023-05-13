<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


class CsvCalculatorController extends Controller
{
    // 元になる鉄筋一本の長さ
    const DEFAULT_LENGTH = 8000;

    public function calTest ()
    {    
        $lengths = array();

        echo "【切断する素材の長さリスト】<br>";
        for ($i=0; $i < 30; $i++) { 
            $lengths[$i] = rand(50, 400)*10;
            echo "　　".$lengths[$i]."mm<br>";
        }
        echo "<br>";

        $lengths = array(500, 600, 700, 800, 900, 1000, 1200, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2500, 3000, 3500, 3600, 3800, 4000);
        $num_lengths = count($lengths);

        $cut_list = $lengths;
        shuffle($cut_list);

        $rods = [8000];
        $cuts = [];

        while (count($cut_list) > 0) {
            $best_cut_index = -1;
            $best_cut_waste = 8000;
            for ($i = 0; $i < count($rods); $i++) {
                $rod = $rods[$i];
                for ($j = 0; $j < count($cut_list); $j++) {
                    $cut = $cut_list[$j];
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
            $cuts[] = [$cut, $best_cut_rod];
            $rods[$best_cut_rod] -= $cut;
            array_splice($cut_list, $best_cut_index, 1);
        }
        $result = array();
        echo "【使用した8000mmの棒の設置回数】 <br>" . count($rods) . "回<br><br>";
        $count = 0;
        echo "【切断の組み合わせ】<br><br>";
        foreach ($cuts as $i => $cut) {
            if ($count != $cut[1]+1) {
                if($i != 0) {
                    echo "合計：　".$result[$cut[1]]."mm<br>";
                    $left = 8000 - $result[$cut[1]];
                    echo "端材：　".$left."mm<br><br>";
                }
                echo "設置" . ($cut[1]+1) . "回目:<br>" . $cut[0] . "mm (" . ($i+1) . "カット目)<br>";
                $count = $cut[1]+1;
            } else {
                echo $cut[0] . "mm (" . ($i+1) . "カット目)<br>";
            }
            if (empty($result[$cut[1]+1])) {
                $result[$cut[1]+1] = $cut[0];
            } else {
                $result[$cut[1]+1] = $cut[0]+$result[$cut[1]+1];
            }
        }
        echo "合計：　".end($result)."mm<br>";
        $left = 8000 - end($result);
        echo "端材：　".$left."mm";
    }    
    
    public function calSecondTest()
    {
        // *****************************************************
        // テストデータ生成　Start
        // *****************************************************
        $data = array();
        $lengths = array();
        // 吐き出し口の配列作成
        $ports = ['A', 'B', 'C', 'D', 'E'];
        // 鉄筋径の管理
        $sizes = ['D10', 'D13', 'D16', 'D19', 'D22'];
        // 同じ鉄筋径・長さ・吐き出し口の配列を作成
        for ($i=0; $i < 30; $i++) { 
            $port = $ports[rand(0, 4)];
            $size =  $sizes[rand(0, 4)];
            $length = rand(50, 400)*10;
            $data[$size][$port.'-'.$length] = rand(10, 100);
        }
        // *****************************************************
        // テストデータ生成　End
        // *****************************************************
        // 鉄筋径別に同時切断設定
        foreach ($data as $size => $array) {
            echo "□■□■□■□■□■□■□■□■□■□■□■□■　鉄筋径：".$size." 　計算開始　□■□■□■□■□■□■□■□■□■□■□■□■<br><br>";
            // 一番少ない本数を取得
            $min = $this->getMinAmount($size, $array);
            $min_renew_flg = false;
            // 全て切断しているかの確認ループ
            while ($min > 0) {                
                // trueの時新たな最小値を取得する必要がある
                if ($min_renew_flg) {
                    $min = $this->getMinAmount($size, $array);   
                }
                // trueの時新たな最小値を取得する必要がある
                $min_renew_flg = true;
                if ($min <= 0) {
                    continue;
                }
                // *****************************************************
                // 切断する素材のリストを吐き出すだけの関数（テスト時に使用するだけなので後で削除必要）
                // *****************************************************
                echo '<br>＝＝＝＝＝＝＝同時切断：　'.$min.'本 開始 ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ <br>';
                $message = '【切断する素材のリスト（切断ロジック計算開始時）】';
                $this->getItemListMessage($message, $min, $array, $size);   
                // 切断りストをリセット
                $lengths = array();
                foreach ($array as $port_length => $amount) {
                    $temp = explode("-",$port_length);
                    // 同時切断数を満たさないものはスキップ
                    if ($amount < $min) {
                        continue;
                    }
                    // 最小値で何セット切断できるか取得
                    $set_num = floor($amount / $min);
                    // 切断するリストに入れる
                    for ($i=0; $i < $set_num; $i++) { 
                        $lengths[] = $temp[1];
                    }
                    // 未切断の本数を更新
                    $array[$port_length] = $amount - ($min*$set_num);
                    // 人組でも現在の最小切断数より大きければ、そのままの最小数で続行
                    if ($array[$port_length] > $min) {
                        $min_renew_flg = false;
                    }                 
                }
                // 切断指示作成
                $test = $this->getCombination($lengths, $size);
                echo '<br>＝＝＝＝＝＝＝同時切断：　'.$min.'本 終了 ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ <br><br><br>';
            }
            $message = '【切断計算後の素材のリスト】';
            $this->getItemListMessage($message, $min, $array, $size);
            echo "<br><br>□■□■□■□■□■□■□■□■□■□■□■□■　鉄筋径：".$size." 　計算終了　□■□■□■□■□■□■□■□■□■□■□■□■<br><br>";
        }
    }


    // *****************************************************
    // 切断する素材のリストを吐き出すだけの関数（テスト時に使用するだけなので後で削除必要）
    // *****************************************************
    public function getItemListMessage($message, $min, $array, $size)
    {
        // 初期の入力値で最小の本数を取得
        echo '<br>'.$message.'<br>';
        foreach ($array as $port_length => $amount) {
            echo "鉄筋径：　".$size."　長さ：　".$port_length."mm"."　本数：　".$amount."本<br>";
            if($min == 0) {
                $min = $amount;
            }
            if($min > $amount) {
                $min = $amount;
            }
        }
    }



    /*
     * 最適な組み合わせを
    */
    public function getCombination($lengths, $size)
    {
        $num_lengths = count($lengths);

        $cut_list = $lengths;
        shuffle($cut_list);

        $rods = [8000];
        $cuts = [];

        // 予備材リスト
        $spare_cut_list =$this->getSpareBySize($size);

        while (count($cut_list) > 0) {
            $best_cut_index = -1;
            $best_cut_waste = 8000;
            for ($i = 0; $i < count($rods); $i++) {
                $rod = $rods[$i];
                for ($j = 0; $j < count($cut_list); $j++) {
                    $cut = $cut_list[$j];
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
            $cuts[] = [$cut, $best_cut_rod];
            $rods[$best_cut_rod] -= $cut;
            array_splice($cut_list, $best_cut_index, 1);
        }
        $result = array();
        $count = 0;
        echo "<br>【切断の組み合わせ】<br>";
        foreach ($cuts as $i => $cut) {
            if ($count != $cut[1]+1) {
                if($i != 0) {
                    echo "合計：　".$result[$cut[1]]."mm<br>";
                    $left = 8000 - $result[$cut[1]];
                    echo "仮端材：　".$left."mm<br><br>";
                    
                    //　予備材を使って端材を最小化
                    $this->cutLeftWithSpare($left, $spare_cut_list);
                }
                echo "設置" . ($cut[1]+1) . "回目:<br>" . $cut[0] . "mm (" . ($i+1) . "カット目)<br>";
                $count = $cut[1]+1;
            } else {
                echo $cut[0] . "mm (" . ($i+1) . "カット目)<br>";
            }
            if (empty($result[$cut[1]+1])) {
                $result[$cut[1]+1] = $cut[0];
            } else {
                $result[$cut[1]+1] = $cut[0]+$result[$cut[1]+1];
            }
        }
        echo "合計：　".end($result)."mm<br>";
        $left = 8000 - end($result);
        echo "端材：　".$left."mm<br><br>";
        //　予備材を使って端材を最小化
        $this->cutLeftWithSpare($left, $spare_cut_list);
    }


    /*
     * 一番少ない本数を取得
    */
    public function getMinAmount($size, $array)
    {
        // 鉄筋径別、同時に切断可能な最大数
        $max_limit['D10'] = 25;
        $max_limit['D13'] = 19;
        $max_limit['D16'] = 15;
        $max_limit['D19'] = 12;
        $max_limit['D22'] = 10;
    
        $min = 0;
        $max = 0;
        // 初期の入力値で最小の本数を取得
        foreach ($array as $port_length => $amount) {
            if ($amount == 0) {
                continue;
            }
            if($min == 0) {
                $min = $amount;
            }
            if($min > $amount) {
                $min = $amount;
            }
        }
        // 最小値が鉄筋径別、同時に切断可能最大数より大きい場合は、最大数に合わせる
        if ($max_limit[$size] < $min) {
            $min = $max_limit[$size];
        }
        return $min;
    }

    /*
     * 鉄筋径別の予備材リストを取得
    */
    public function getSpareBySize($size)
    {
        if($size == 'D10') {
            $list = array(180, 280, 320, 370, 410, 550, 580, 700, 765, 800, 840, 860, 870, 900, 910, 915, 1000, 1100, 1200, 1250, 1300, 1310, 1325, 1360, 1500, 1760, 1780, 1800, 1960, 2235, 2660, 2690, 2730, 3560, 3600);
        } elseif ($size == 'D13') {
            $list = array(180, 250, 280, 330, 550, 860, 870, 910, 940, 1000, 1050, 1100, 1200, 1300, 1310, 1325, 1400, 1450, 1500, 1530, 1550, 1600, 1650, 1720, 1760, 1780, 1800, 1960, 2000, 2100, 2235, 2660, 2690, 3600);
        } elseif ($size == 'D16') {
            $list = array(420, 825, 860, 870, 1310, 1376, 1380, 1400, 1500, 1760, 1780, 1800, 1960, 2000, 2235, 2300, 2660, 2690, 3560, 3600);
        } elseif ($size == 'D19') {
            $list = array(930, 1580, 1680, 1710, 1800, 1830, 2200, 2230, 2280, 2580, 2730, 3630);
        } elseif ($size == 'D22') {
            $list = array(2000, 2400);
        }
        return $list;
    }

    /*
     * 予備材リストを用いた端材の切断結果をテスト出力
    */
    public function cutLeftWithSpare($left, $spare_cut_list)
    {
        $used_spare_result = $this->findCutList($left, $spare_cut_list);
        if ($used_spare_result === false) {
            echo "予備材で切断できません。<br><br>";
        } else {
            echo "【予備材使用の計算結果】<br>";
            $cutCount = count($spare_cut_list);
            $waste = $left;
            for ($j = 0; $j < $cutCount; $j++) {
                if ($used_spare_result[$j] > 0) {
                    echo "{$spare_cut_list[$j]}mm x {$used_spare_result[$j]}<br>";
                    $waste -= $spare_cut_list[$j] * $used_spare_result[$j];
                }
            }
            echo '<span style="color:#0000FF;">最終端材：'.$waste.'mm</span><br><br>';
        }
    }

    /*
     * 予備材リストを用いた端材の切断結果を取得
    */
    public function findCutList($left_length, $spare_cut_list) {
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
}
