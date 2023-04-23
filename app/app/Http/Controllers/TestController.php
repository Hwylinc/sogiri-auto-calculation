<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

// model
use App\Models\Diameter;
use App\Models\Spare;


class TestController extends Controller
{

    public static $select_id = 1;  // 選択されている鉄筋径のid

    public function get($request, $value) {

        $diameterIns = new Diameter();
        $spareIns = new Spare();

        $diameters = $diameterIns->get_all();
        $spares = $spareIns->get_by_id($value["select_id"]);

        // 予備材をHTMLで扱いやすくするために加工
        $show_spares = [];
        $add_array = [];
        foreach ($spares as $index => $spare) {
            if ($index != 0 && $index % 12 == 0) {
                array_push($show_spares, $add_array);
                $add_array = [];
            } 
            array_push($add_array, $spare);

            if ($index === (count($spares) - 1)) {
                if (count($add_array) !== 12) {
                    for ($i = count($add_array); $i < 12; $i++) {
                        array_push($add_array, [
                            'name' => "　",
                            'priority_flg' => 0,
                            'id' => 999999999,
                        ]);
                    }
                }
                array_push($show_spares, $add_array);
            }
        }

        // 件数が0件の時の処理
        if (count($diameters) == 0) {
            $this->addFlash('error', 'データが存在しません');
        }

        $with = [
            'diameters' => $diameters,
            'select_id' => $value["select_id"],
            'show_spares' => $show_spares,
            'screen' => $value["screen"],
        ];

        return $this->view('home', $with);
    }

    public function edit($request, $value) 
    {

        $spareIns = new Spare();

        $ids = $request->input('priority');

        // 選択項目が6個以上の場合は元の画面に戻す
        if(count($ids) > 5) {
            $this->addFlash('error', '6個以上は選択できません。');
            return back()->withInput();
        }

        $spareIns->update_all_priority_reset($value["select_id"]);
        $spareIns->update_priority($ids);

        $this->addFlash('success', '登録が完了しました。');
        
        return redirect()->route('spare', ['screen' => 'list', 'select_id' => $value["select_id"]])->withInput();
        
    }
}

?>