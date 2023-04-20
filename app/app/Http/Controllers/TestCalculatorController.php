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


    // 共通約数と以前のロジック組み合わせ
    public function forth()
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

dd($dataArray);
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
        echo "【".$size."径　初期データ一覧】<br>";
        foreach($temp_array as $length => $num) {
            echo '長さ：　'.$length.'mm　本数：　'.$num.'本<br>';
        }
    }
}