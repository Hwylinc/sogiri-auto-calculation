<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// model
use App\Models\Diameter;
use App\Models\Spare;

class SpareController extends BaseController
{
    public static $select_id = 1;  // 選択されている鉄筋径のid
    // *******************************************
    // コンストラクタ
    // *******************************************
    public function __construct()
    {
        parent::__construct();
    }
    
    // *******************************************
    // 予備材一覧画面
    // *******************************************
    public function getList(Request $resuest, $factry_id)
    {
        $diameterIns = new Diameter();
        $spareIns = new Spare();

        $diameters = $diameterIns->get_all();
        $spares = $spareIns->get_by_id($factry_id);

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
            $this->addFlash($resuest, 'error', 'データが存在しません');
        }

        $with = [
            'diameters' => $diameters,
            'select_id' => $factry_id,
            'show_spares' => $show_spares,
            // 'screen' => $value["screen"],
            'screen' => 'list',
        ];

        return $this->view('spare.list', $with);
    }
    
    // *******************************************
    // 予備材編集画面
    // *******************************************
    public function getEdit(Request $request, $factry_id)
    {
        $diameterIns = new Diameter();
        $spareIns = new Spare();

        $diameters = $diameterIns->get_all();
        $spares = $spareIns->get_by_id($factry_id);

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
            $this->addFlash($request, 'error', 'データが存在しません');
        }

        $with = [
            'diameters' => $diameters,
            'select_id' => $factry_id,
            'show_spares' => $show_spares,
            // 'screen' => $value["screen"],
            'screen' => 'edit',
        ];

        return $this->view('spare.list', $with);
    }
    
    // *******************************************
    // 編集完了処理
    // *******************************************
    public function postComplete(Request $request)
    {
        $spareIns = new Spare();

        $ids = $request->input('priority');
        $select_id = $request->input('select_id');

        if(!is_null($ids)) {
            // 選択項目が6個以上の場合は元の画面に戻す
            if(count($ids) > 5) {
                $this->addFlash($request, 'error', '6個以上は選択できません。');
                return back()->withInput();
            } else {
                $spareIns->update_all_priority_reset($select_id);
                foreach($ids as $order => $id) {
                    $spareIns->update_priority($id, $order);
                }
        
                $this->addFlash($request, 'success', '登録が完了しました。');
            }
        }

        return redirect()->route('spare.list', ['factry_id' => $select_id])->withInput();
    }
}
