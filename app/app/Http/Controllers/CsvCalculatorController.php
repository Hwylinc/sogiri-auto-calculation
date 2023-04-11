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
            $data[$size][$port.'-'.$length] = rand(10, 40);
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
            $lengths[] = 'test';
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
                $test = $this->getCombination($lengths);
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
    public function getCombination($lengths)
    {
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
        $count = 0;
        echo "<br>【切断の組み合わせ】<br>";
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
        echo "端材：　".$left."mm<br>";
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

}
