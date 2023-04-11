<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CsvCalculatorTest extends Command
{
    

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testBatch:testDayo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'コマンド実行のテストだよ';

    // 元になる鉄筋一本の長さ
    const DEFAULT_LENGTH = 8000;

    // 鉄筋径別、同時に切断可能な最大数
    const max_limit_D10 = 25;
    const max_limit_D13 = 19;
    const max_limit_D16 = 15;
    const max_limit_D19 = 12;
    const max_limit_D22 = 10;


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 使用例
        $length = 8000;
      
        // ランダムな配列を生成
        $part_lengths = [4550,4500,4000,3500,3450,3000,2950,2500,2450,2000,1500,1000,665,800,4890,4050,3700,2700,2600,2520,2400,2200,2150,1700,1600,1552,1485,1390,1300,1200,840,735,525,5000,4300,3040];

        // 配列の合計値を計算
        $total = array_sum($part_lengths);

        // 初期値としての最小誤差を計算
        $unit = floor($total / $length);
        $min_diff = abs($total - $length*$unit);

        // 初期値としての最適な組み合わせを設定
        $optimal_combination = $part_lengths;
        
        // 総当たり計算で最適な組み合わせを探す
        for ($i = 0; $i < count($part_lengths); $i++) {
            for ($j = $i + 1; $j < count($part_lengths); $j++) {
                // $part_lengths[$i] と $part_lengths[$j] を入れ替える
                $temp = $part_lengths[$i];
                $part_lengths[$i] = $part_lengths[$j];
                $part_lengths[$j] = $temp;

                // 新しい合計値を計算
                $new_total = array_sum($part_lengths);
                
                // 新しい誤差を計算
                $new_diff = abs($new_total - $length);
dd($new_diff); 
                // 誤差が最小値よりも小さい場合は最小値と最適な組み合わせを更新
                if ($new_diff < $min_diff) {
                    $min_diff = $new_diff;
                    $optimal_combination = $part_lengths;
                }
                
                // 元に戻す
                $temp = $part_lengths[$i];
                $part_lengths[$i] = $part_lengths[$j];
                $part_lengths[$j] = $temp;
            }
        }

        // 結果を出力
        var_dump("最適な組み合わせ: ");
        var_dump($optimal_combination);
        var_dump("誤差: " . $min_diff);
dd(99);


        
        $csv = file(storage_path('app/public/test.csv'));
        // CSVのデータを読み込む
        foreach($csv as $key => $value){
            // ヘッダースキップ
            if($key == 0 ) {
                continue;
            }
            // 1行のデータをコンマで分割する
            $temp = explode(',', $value);
            $item_no     = str_replace("\r\n", '', $temp[0]); // No
            $item_name   = str_replace("\r\n", '', $temp[1]); // 筋種
            $size        = str_replace("\r\n", '', $temp[2]); // 鉄筋径
            $amount      = str_replace("\r\n", '', $temp[3]); // 本数
            $length      = str_replace("\r\n", '', $temp[4]); // 長さ
            $port        = str_replace("\r\n", '', $temp[5]); // 吐出口

            // 同じ部材カテゴリ・長さごとの数を数える
            $data[$size][$port.'-'.$length][$item_no.'-'.$item_name] = $amount;
            // 同じ鉄筋径・長さ・吐出し口の数を数える
            if(isset($data[$size][$port.'-'.$length]['total'])) {
                $data[$size][$port.'-'.$length]['total'] = $data[$size][$port.'-'.$length]['total']+$amount;
            } else {
                $data[$size][$port.'-'.$length]['total'] = $amount;
            }
        }

dd($data);
        // 鉄筋径別に同時切断設定
        foreach ($data as $size => $array) {
            // 一番少ない本数を取得
            $min = $this->getMinAmount($array);
            while ($min > 0) {
                // trueの時新たな最小値を取得する必要がある
                $min_renew_flg = true;
// $calculator_result[$size] = $this->getResult($array);
                foreach ($array as $port_length => $val) {
                    // 同時切断数を満たさないものはスキップ
                    if ($val['total'] < $min) {
                        continue;
                    }

                    $calculator_result[$size][$port_length] = $min;
                    // 残り合計本数を更新
                    $array[$port_length]['total'] = $val['total'] - $min;
                    // まだ新たな最小値を取得する必要がないためフラグ変更
                    $min_renew_flg = false;
dd($val['total']);             
                    // if ($min_temp > $val['total'] || $val['total'] != 0) {
                    //     $min_temp = $val['total'];
                    // }
dd($array);

                }
                $min = $min_temp;
                $count++;
            }
        }
dd($min);

        $count = 1;
        var_dump('$min');
        var_dump($min);

        while ($min > 0) {
            $min_temp = $min;
            foreach ($data as $k => $val) {
                // n回目時の残り本数を更新
                $val['total'] = $val['total'] - $min;
                if ($min_temp > $val['total'] || $val['total'] != 0) {
                    $min_temp = $val['total'];
                }
            }
            $min = $min_temp;
            $count++;
        }
        dd($count);
        foreach ($variable as $key => $value) {
            if ($min > 0) {

            }
        }

        return Command::SUCCESS;
    }

    /*
     * 一番少ない本数を取得
     *
    */
    public function getMinAmount($array)
    {
        $min = 0;
        // 初期の入力値で最小の本数を取得
        foreach ($array as $port_length => $val) {
            if($min == 0) {
                $min = $val['total'];
            }
            if($min > $val['total']) {
                $min = $val['total'];
            }
        }
        return $min;
    }

    /*
     * 
     *
    */
    public function getResult($array)
    {
// dd(array);
        foreach ($array as $port_length => $val) {
            // n回目時の残り本数を更新
            $val['total'] = $val['total'] - $min;
            if ($min_temp > $val['total'] || $val['total'] != 0) {
                $min_temp = $val['total'];
            }
        }
        $min = $min_temp;
        $count++;
    }
}
