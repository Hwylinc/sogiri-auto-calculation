<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


class CsvCalculatorController extends Controller
{
    // 元になる鉄筋一本の長さ
    const DEFAULT_LENGTH = 8000;

    // 鉄筋径別、同時に切断可能な最大数
    const max_limit_D10 = 25;
    const max_limit_D13 = 19;
    const max_limit_D16 = 15;
    const max_limit_D19 = 12;
    const max_limit_D22 = 10;

    public function index ()
    {        
        $lengths = array();
        echo "【切断する素材の長さリスト】<br>";
        for ($i=0; $i < 30; $i++) { 
            $lengths[$i] = rand(50, 400)*10;
            echo "　　".$lengths[$i]."mm<br>";
        }
        echo "<br>";
        // $lengths = array(500, 600, 700, 800, 900, 1000, 1200, 1400, 1500, 1600, 1700, 1800, 1900, 2000, 2500, 3000, 3500, 3600, 3800, 4000);
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
    
}
