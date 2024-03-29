<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


class TestCalculatorController extends Controller
{
    // 元になる鉄筋一本の長さ
    const DEFAULT_LENGTH = 8000;

    public function index()
    {
        // 2から60の間のランダムな数値を20個生成
        echo "【初期データ】<br>";
        $numbers = [];
        for ($i = 0; $i < 40; $i++) {
            $numbers[] = rand(2, 60);
            echo $numbers[$i].'本<br>';
        }

        // 各数字の約数の一覧を格納する配列
        $divisors = [];
        // 存在する約数の一覧を格納する配列
        $exist_divisors = [];
        // 各数字に対して約数を取得し、$divisors に格納する
        foreach ($numbers as $num) {
            $divisors[$num] = $this->getDivisors(22, $num);
            // 重複なく約数を統合する
            $exist_divisors = array_unique(array_merge($exist_divisors, $divisors[$num]));
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
                        $groups[$n][$key] = $key;
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
        $this->getGroupResult('D10', $groups);
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
        echo "<br><br>";
        echo "【共通約数の組み合わせ】    ".$size."<br>";
        foreach ($groups as $divisor => $array) {
            echo "<br>共通約数  ".$divisor." ：<br>";
            foreach ($array as $temp_length => $number) {
                $times = $arr[$temp_length]/$divisor;
                echo $temp_length.'　'.$arr[$temp_length].'本（'.$divisor.'本 X '.$times.'回カット必要）<br>';
                $length = explode('_',$temp_length)[0];
                $data[$size][$divisor][$length] = $times;
            }
        }
        return $data;
    }

    public function getTestDataFromCsv($ports)
    {        
        $csv = file(storage_path('app/public/real_data.csv'));
        $data = array();
        // CSVのデータを読み込む
        foreach($csv as $key => $value){
            // ヘッダースキップ
            if($key == 0 ) {
                continue;
            }
            // 1行のデータをコンマで分割する
            $temp   = explode(',', $value);
            $length = str_replace("\r\n", '', $temp[0]); // 切断長
            $set    = str_replace("\r\n", '', $temp[1]); // 数量
            $rap    = str_replace("\r\n", '', $temp[2]); // スターラップ数
            $size   = str_replace("\r\n", '', $temp[3]); // 径
            $amount = $set*$rap;
            // 吐き出し口を長さ対応で出す場合は分岐必要
            $port   = $ports[rand(0, 4)]; // 吐出口

            // 同じ部材カテゴリ・長さごとの数を数える
            if (isset($data[$size][$port.'-'.$length])) {
                $data[$size][$port.'-'.$length] = $data[$size][$port.'-'.$length]+$amount;
            } else {
                $data[$size][$port.'-'.$length] = $amount;
            }
        }
        return $data;
    }

    // 共通約数と以前のロジック組み合わせ
    public function forth()
    {
        // *****************************************************
        // テストデータ生成　Start
        // *****************************************************
        $data = array();
        $ports = ['A', 'B', 'C', 'D', 'E'];
        $data = $this->getTestDataFromCsv($ports);
        // $lengths = array();
        // 吐き出し口の配列作成
        
        // 鉄筋径の管理
        // $sizes = ['D10', 'D13', 'D16', 'D19', 'D22'];
        // 同じ鉄筋径・長さ・吐き出し口の配列を作成
        // for ($i=0; $i < 30; $i++) { 
        //     $port = $ports[rand(0, 4)];
        //     $size =  $sizes[rand(0, 4)];
        //     $length = rand(50, 400)*10;
        //     $data[$size][$port.'-'.$length] = rand(10, 100);
        // }

        // *****************************************************
        // テストデータ生成　End
        // *****************************************************

        $dataArray = [];
        foreach ($data as $size => $temp_array)
        {
// テストデータ出力用関数（後で削除）
$this->getInitialData($size, $temp_array);

            // 各数字の約数の一覧を格納する配列
            $divisors = [];
            // 存在する約数の一覧を格納する配列
            $exist_divisors = [];

            // 紐づく物理最大数を取得
            $max_limit = $this->getMaxNumber($size);
            // Numが物理幅以上の場合は２５の倍数とあまりに分ける
            // 後で約数は25以下にしかとらないとかにしとく
            $array = $this->getAdjustedByNum($max_limit, $temp_array);
// dd($array);
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
// dd($divisors);                
                    foreach ($divisors as $key => $divisor) {
                        // dd($divisor);
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

        // 鉄筋径別に同時切断設定
        foreach ($dataArray as $size => $array) {
            echo "□■□■□■□■□■□■□■□■□■□■□■□■　鉄筋径：".$size." 　計算開始　□■□■□■□■□■□■□■□■□■□■□■□■<br><br>";
            foreach ($array as $cutting_num => $arr) {
                // *****************************************************
                // 切断する素材のリストを吐き出すだけの関数（テスト時に使用するだけなので後で削除必要）
                // *****************************************************
                echo '<br>＝＝＝＝＝＝＝同時切断：　'.$cutting_num.'本 開始 ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ <br>';
                $message = '【切断する素材のリスト（切断ロジック計算開始時）】';
                $this->getItemListMessage($message, $cutting_num, $arr, $size);   
                // 切断りストをリセット
                $lengths = array();
                foreach ($arr as $port_length => $amount) {
                    $temp = explode("-",$port_length);
                    // 切断するリストに入れる
                    for ($i=0; $i < $amount; $i++) { 
                        $lengths[] = $temp[1];
                    }
                    // 未切断の本数を更新
                    // $array[$port_length] = $amount - ($cutting_num*$set_num);
                    // 人組でも現在の最小切断数より大きければ、そのままの最小数で続行
                    // if ($array[$port_length] > $cutting_num) {
                    //     $min_renew_flg = false;
                    // } 
                }
                // 切断指示作成
                $test = $this->getCombination($lengths, $size);
                echo '<br>＝＝＝＝＝＝＝同時切断：　'.$cutting_num.'本 終了 ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ <br><br><br>';
                
            }
            echo "<br><br>□■□■□■□■□■□■□■□■□■□■□■□■　鉄筋径：".$size." 　計算終了　□■□■□■□■□■□■□■□■□■□■□■□■<br><br>";
        }
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

    // 紐づく物理最大数を返す
    public function getMaxNumber($size)
    {
        // 鉄筋径別、同時に切断可能な最大数
        $max_limit['D10'] = 25;
        $max_limit['D13'] = 19;
        $max_limit['D16'] = 15;
        $max_limit['D19'] = 12;
        $max_limit['D22'] = 10;

        return $max_limit[$size];
    }

    // テストデータ出力用関数（後で削除）
    public function getInitialData($size, $temp_array)
    {
        echo "【".$size."径　初期データ一覧】<br> ";
        foreach($temp_array as $length => $num) {
            echo '長さ：　'.$length.'mm　本数：　'.$num.'本<br>';
        }
    }


    // *****************************************************
    // 切断する素材のリストを吐き出すだけの関数（テスト時に使用するだけなので後で削除必要）
    // *****************************************************
    public function getItemListMessage($message, $cutting_num, $array, $size)
    {
        // 初期の入力値で最小の本数を取得
        echo '<br>'.$message.'<br>';
        foreach ($array as $port_length => $amount) {
            echo "鉄筋径：　".$size."　長さ：　".$port_length."mm"."　本数：　".$cutting_num*$amount."本（".$cutting_num."本 X ".$amount."回カット必要）<br>";
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