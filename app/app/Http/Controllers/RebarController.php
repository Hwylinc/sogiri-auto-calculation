<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diameter;
use App\Models\CalculationRequests;
use Exception;
use Illuminate\Support\Facades\Auth;

class RebarController extends BaseController
{
    // *******************************************
    // コンストラクタ
    // *******************************************
    public function __construct()
    {
        parent::__construct();
    }

    // *******************************************
    // 鉄筋情報一覧画面
    // *******************************************
    public function getList()
    {
        dd('鉄筋情報一覧画面');
    }
    
    // *******************************************
    // 鉄筋情報入力方法・工場選択画面
    // *******************************************
    public function getSelect()
    {
        dd('鉄筋情報入力方法・工場選択画面');
    }
    
    // *******************************************
    // 鉄筋情報手入力画面
    // *******************************************
    public function getRegister(Request $request, $select_diameter)
    {
        try {
            // initial variable
            $exist_info = [];

            // 鉄筋径情報取得
            $diameterIns = new Diameter();
            $diameters = $diameterIns->get_all();

            $view_data = [];
            

            // ダミーデータ
            $compDetail = [
                ['id' => 1, 'name' => '鉄筋部材a-a'],
                ['id' => 2, 'name' => '鉄筋部材a-b'],
                ['id' => 3, 'name' => '鉄筋部材a-c'],
            ];
            $component = ['鉄筋部材a', '鉄筋部材b', '鉄筋部材c', '鉄筋部材d'];

            // pagenation
            $page = $this->pagenation($diameters, $select_diameter);

            // session get info
            $exist_info = [];
            if ($request->session()->has('rebar.diameter_' . $select_diameter)) {
                $exist_info = $request->session()->get('rebar.diameter_' . $select_diameter);
            }

            $view_data['components'] = $component;
            $view_data['diameters'] = $diameters;
            $view_data['comp_detail'] = $compDetail;
            $view_data['page'] = $page;
            $view_data['exist_info'] = $exist_info;
        } catch (Exception $e) {
            $this->addFlash($request, 'error', $e->getMessage());
            return back()->withInput();
        }
        

        
        return view('rebar.form_register')->with($view_data);
    }

    // *******************************************
    // 鉄筋情報一時保存
    // *******************************************
    public function postStore(Request $request) 
    {

        $input = $request->all();
        $newRows = [];
        $errorFlg = true;
        $transArrayFlg = false;
        $message = "";

        try {
            if ($request->has('input')) {
                $reInput = $request->get('input');
                ksort($reInput);
                foreach ($reInput as $key => $array) {
                    $newRow = [];
                    ksort($array['data']);
                    $count = 1;
                    foreach($array['data'] as $order => $row) {
                        if ($row['length'] || $row['number']) {
                            $message = $this->customValidate($row);
                            $row['display_order'] = $count;
                            if ( !($message == 'OK') && $errorFlg ) {
                                $transArrayFlg = true;
                                $errorFlg = false;
                            } 
                            $newRow[$count] = $row;
                            $count++;
                        }
                    }

                    // 新しく作成した部材毎のデータを配列に追加
                    if ($transArrayFlg) {
                        array_unshift($newRows, ['id' => $array['id'], 'data' => $newRow]);    // 1つ目のエラーを先頭に追加
                        $request->session()->flash('message.error', $message);
                    } else {
                        array_push($newRows, ['id' => $array['id'], 'data' => $newRow]);    
                    }
                }
    
                $rebarRef = [
                    'diamenter_id' => $input['select_diameter'],
                    'input' => $newRows,
                ];
                
                $request->session()->put('rebar.diameter_' . $input['select_diameter'], $rebarRef);
            } 
            if ( (int)$input['action'] !== -1 ) {
                $nextPage = $transArrayFlg ? $input['select_diameter'] : $input['action'];
                return redirect()->route('rebar.register', $nextPage);
            } else {
                return $this->postComplete($request);
            }
        } catch (Exception $e) {
            $this->addFlash($request, 'error', $e->getMessage());
            return back()->withInput();
        }
        

        
    }
    
    // *******************************************
    // 鉄筋情報手入力確認画面
    // *******************************************
    public function getConfirm(Request $request, $select_diameter)
    {   
        // initial variable
        $view_data = [];
        $diameterIns = new Diameter();
        $error = "0";

        // 鉄筋径情報取得
        $diameters = $diameterIns->get_all();

        // ダミーデータ
        $compDetail = [
            ['id' => 1, 'name' => '鉄筋部材a-a'],
            ['id' => 2, 'name' => '鉄筋部材a-b'],
            ['id' => 3, 'name' => '鉄筋部材a-c'],
        ];
        $component = ['鉄筋部材a', '鉄筋部材b', '鉄筋部材c', '鉄筋部材d'];
        $client_id = 1;
        $house_name = 'urano';

        // pagenation
        $page = $this->pagenation($diameters, $select_diameter);

        $where = [
            'diameter_id' => $select_diameter,
            'client_id' => $client_id,
            'house_name' => $house_name,
        ];

        $exist_info = [];
        if ($request->session()->has('error_data')) {
            $get_result = $request->session()->get('error_data');
            foreach($get_result as $index => $row) {
                foreach($row['data'] as $key => $data) {
                    $exist_info["c_". $row['id']][] = $data;
                }
            }
            $error = "1";
            $request->session()->forget('error_data');
        } else {
            $get_result = CalculationRequests::getWhereCalucReq($where);
            foreach($get_result as $index => $row) {
                $exist_info["c_" . $row['component_id']][] = $row;
            }
        }

        $view_data['components'] = $component;
        $view_data['diameters'] = $diameters;
        $view_data['comp_detail'] = $compDetail;
        $view_data['page'] = $page;
        $view_data['exist_info'] = $exist_info;
        $view_data['error'] = $error;
        return view('rebar.confirm')->with($view_data);
    }
    
    // *******************************************
    // 鉄筋情報手入力編集画面
    // *******************************************
    public function getEdit($calculation_id, $diameter)
    {
        dd('鉄筋情報手入力編集画面');
    }
    
    // *******************************************
    // 鉄筋情報CSVアップロード画面
    // *******************************************
    public function getCsvRegister()
    {
        dd('鉄筋情報CSVアップロード画面');
    }

    // *******************************************
    // 鉄筋情報CSV読み込みアップロード画面
    // Requestはバリデーションにより適宜最適なものを使用してください
    // *******************************************
    public function postCsvUpload(Request $request)
    {
        dd('鉄筋情報CSV読み込みアップロード画面');
    }

    // *******************************************
    // 鉄筋情報CSVアップロード確認画面
    // *******************************************
    public function getCsvConfirm()
    {
        dd('鉄筋情報CSVアップロード確認画面');
    }

    // *******************************************
    // 鉄筋情報登録・編集完了処理 
    // Requestはバリデーションにより適宜最適なものを使用してください
    // *******************************************
    public function postComplete(Request $request)
    {

        try {
            // completeからの処理
            if ($request->get('process') === 'update') {
                [$create_data, $error] = $this->input_check($request);

                if( $error ) {    // エラーが存在した場合
                    $request->session()->put('error_data', $create_data);
                } else {
                    foreach( $create_data as $data ) {
                        foreach( $data['data'] as $row ) {
                            if ($row['length'] && $row['number']) {
                                
                                if( $row['id'] === '-999' ) {   // idが存在しない場合は新規登録
                                    $this->insertExe($row, $request->get('select_diameter'), $data['id']);
                                } else {    // idが存在する場合は更新処理
                                    CalculationRequests::updateById((int)$row['id'], [
                                        'length' => (int)$row['length'],
                                        'number' => (int)$row['number'],
                                        'port_id' => 1,
                                        'display_order' => (int)$row['display_order'],
                                    ]);
                                }
                            } 
                        }
                    }
                }

                return redirect()->route('rebar.confirm', $request->get('select_diameter'));
            } else {
                // registerからの処理
                $input_datas = $request->session()->get('rebar');

                if (!$input_datas) {
                    // return new throw('管理者エラー');
                }

                foreach ($input_datas as $diameter_id => $arr_inputs) {
                    foreach ($arr_inputs['input'] as $component_id => $arr_input) {
                        foreach ($arr_input['data'] as $regist_data) {
                            if ($regist_data['length'] && $regist_data['number']) {
                                $this->insertExe($regist_data, $arr_inputs['arr_inputs'], $arr_input['id']);
                            }
                        }
                    }
                }

                return redirect()->route('rebar.register', 1);
            }
        } catch(Exception $e) {
            dd($e->getMessage());
        }
    }
    
    // *******************************************
    // 鉄筋情報登録・編集完了画面
    // *******************************************
    public function getComplete()
    {
        dd('鉄筋情報登録・編集完了画面');
    }
    
    // *******************************************
    // 鉄筋情報詳細画面
    // *******************************************
    public function getDetail($calculation_id)
    {
        dd('鉄筋情報詳細画面');
    }

    /**
     * 入力データに対してのチェック
     *
     * @param [type] $request
     * @return void
     */
    private function input_check($request): array
    {
        $request_input = $request->get('input'); // 入力内容の取得
        $error_flg = 1;     // 1: なし, 2: あり, 3: 済み
        $comp_rows = [];    // 鉄筋径全体のデータ
        $message = "";      

        ksort($request_input);  // 部材毎に並び替え

        foreach($request_input as $comp => $component_data) {
            $comp_row = [];      // component毎のデータを格納
            $count = 1;          // 表示順
            $comp_datas = $component_data['data'];

            ksort($comp_datas); // 表示順に並べ替え

            // 1行に対してのエラーチェック & データ加工
            foreach( $comp_datas as $order => $row ) {
                if ( $row['length'] || $row['number'] ) {
                    $message = $this->customValidate($row); // validation check

                    $row['display_order'] = $count;

                    $comp_row[$count] = $row;

                    $count++;

                    if ( !($message == 'OK') && $error_flg == 1 ) {
                        $error_flg = 2;
                    }
                } else {
                    // 既に存在しているデータ箇所が空白の場合は、削除処理を行う
                    if( $row['id'] && $row !== '-999' ) {
                        CalculationRequests::deleteById($row['id']);
                    }
                }
            }

            // 作成したデータの結合
            $createData = [
                'id' => $component_data['id'],
                'data' => $comp_row,
            ];

            // 新しく作成した部材毎のデータを配列に追加
            if( $error_flg == 2 ) {
                array_unshift($comp_rows, $createData);
                $request->session()->flash('message.error', $message);
                $error_flg = 3;
            } else {
                array_push($comp_rows, $createData);
            }
        }

        $error = $error_flg == 3 ? true : false;

        return [$comp_rows, $error];
    }

    /** 独自バリデーション
     * 
     */
    private function customValidate(array $data): string 
    {
        if ($data['number'] || $data['length']) {
            if ( !$data['number'] ) {
                return '長さを入力した場合、本数は入力必須です';
            } elseif ( !$data['length'] ) {
                return '本数を入力した場合、長さは入力必須です';
            } else {
                return 'OK';
            }
        }
    }

    /**
     * insert処理実行
     *
     * @param array $data
     * @param [type] $diameterId
     * @param [type] $comonentId
     * @return void
     */
    private function insertExe(array $data, $diameterId, $comonentId)
    {
        // ダミーデータ
        $client_id = 1;
        $house_name = 'urano';
        $code = '不明';

        $user = Auth::user();

        $data['client_id'] = $client_id;
        $data['house_name'] = $house_name;
        $data['diameter_id'] = $diameterId;
        $data['component_id'] = $comonentId;
        $data['port_id'] = 1;
        $data['user_id'] = $user['id'];
        $data['code'] = $code;
        CalculationRequests::ins($data);
    }

    /**
     * pagenation作成処理
     *
     * @param [type] $diameters
     * @param [type] $select_diameter
     * @return array
     */
    private function pagenation($diameters, $select_diameter): array
    {   

        $page = ['now' => $select_diameter];

        foreach($diameters as $index => $diameter) {
            if ($select_diameter == $diameter->id) {
                if (count($diameters) !== $index+1 ) {
                    if ( $index !== 0 ) {
                        $page['prev'] = $diameters[$index-1];
                    }
                    $page['next'] = $diameters[$index+1];
                } else {
                    $page['prev'] = $diameters[$index-1];
                    $page['next'] = ['id' => -1];
                }
            }
        }

        return $page;
    }
}
