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
            $divisors[$num] = $this->getDivisors($num);
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
        $this->getResult($groups);
    }


    // 約数を取得する関数
    public function getDivisors($n) {
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
        sort($temp_divisors);
        // 小さい順にソートしてから返す
        if (!empty($temp_divisors)) {
            foreach ($temp_divisors as $num) {
                $divisors[$num] = $num;
            }
        }
        return $divisors;
    }

    // 出力
    public function getResult($groups)
    {
        echo "<br><br>";
        echo "【共通約数の組み合わせ】<br>";
        foreach ($groups as $divisor => $array) {
            echo "<br>共通約数  ".$divisor." ：";
            $count = 0;
            foreach ($array as $number) {
                if ($count == 0) {
                    echo $number.'本';
                } else {
                    echo ', '.$number.'本';
                }
                $count++;
            }
        }
    }
}