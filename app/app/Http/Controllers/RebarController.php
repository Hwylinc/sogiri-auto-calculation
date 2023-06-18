<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diameter;
use App\Models\CalculationRequests;
use App\Models\Client;
use App\Models\CalculationCode;
use App\Models\Component;
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


        // メーカーを全件取得
        $clients = Client::get_all();

        // defaultが小野工場のため、2を渡すようにする
        $factory_checked = Auth::user()['factory_id'];

        // viewを表示する
        return view('rebar.select')->with([
            'clients' => $clients 
            , 'factory_checked' => $factory_checked
        ]); 
    }

    // *******************************************
    // 鉄筋情報入力保存
    // *******************************************
    public function postSelect(Request $request)
    { 
        // validation処理
        $request->validate([
            'client_id' => 'required',
            'house_name' => 'required',
        ]);

        // クライアント名を取得する
        $client = Client::get_by_id($request->get('client_id'));
    
        // sessionに履歴を保存
        $request->session()->push('rebar.select.his', [
            'client_name' => $client['name'],
            'house_name' => $request->get('house_name')
        ]);
        $request->session()->put('rebar.select.now', [
            'client_id' => $request->get('client_id'),
            'client_name' => $client['name'],
            'house_name' => $request->get('house_name'),
            'factory_id' => $request->get('factory_id'),
        ]);

        $disameter = Diameter::get_first(0);

        // getRegisterに遷移
        return redirect()->route('rebar.register', ['diameter' => $disameter['id']]);
    }
    
    // *******************************************
    // 鉄筋情報手入力画面
    // *******************************************
    public function getRegister(Request $request, $select_diameter)
    {

        if( $request->session()->missing('rebar.select.now') ) {
            return redirect()->route('rebar.select');
        }

        $view_data = [];

        try {
            // initial variable
            $exist_info = [];

            // 鉄筋径情報取得
            $diameterIns = new Diameter();
            $diameters = $diameterIns->get_all();
            $view_data["select_diameter"] = Diameter::get_by_id($select_diameter);

            $view_data['components'] = Component::get_all();

            // pagenation
            $page = $this->pagenation($diameters, $select_diameter);

            // session get info
            $exist_info = [];
            if ($request->session()->has('rebar.data.diameter_' . $select_diameter)) {
                $exist_info = $request->session()->get('rebar.data.diameter_' . $select_diameter);
            }

            // get select info
            $view_data['select_info'] = $request->session()->get('rebar.select.now');

            // 選択できるfacotry_id
            $view_data['factory_id'] = Auth::user()['factory_id'];

            $view_data['diameters'] = $diameters;
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
        $reMess = "";

        try {

            if( $request->has('input') ){
                $input_data = $request->get('input');
                $checked_comp = $request->get('component');
                ksort($input_data);

                foreach( $input_data as $key => $data ){
                    if( in_array($key, $checked_comp) ) {
                        $newRow = [];
                        ksort($data['data']);
                        $count = 1;
                        foreach( $data['data'] as $order => $row ){
                            if ($row['length'] || $row['number']) {
                                $message = $this->customValidate($row);
                                $row['display_order'] = $count;
                                if ( !($message == 'OK') && $errorFlg ) {
                                    $transArrayFlg = true;
                                    $errorFlg = false;
                                    $reMess = $message;
                                } 
                                $newRow[$count] = $row;
                                $count++;
                            } 
                        }

                        if($count === 1 ) {
                            $transArrayFlg = true;
                            $reMess = "選択された部材は、入力必須です";
                        }

                        // 新しく作成した部材毎のデータを配列に追加
                        if ($transArrayFlg) {
                            array_push($newRows, ['id' => $data['id'], 'name' => $data['name'], 'data' => $data]);
                            $request->session()->flash('message.error', $reMess);
                        } else {
                            array_push($newRows, ['id' => $data['id'],'name' => $data['name'],  'data' => $newRow]);    
                        }
                    }
                }
                
                if ($transArrayFlg) {
                    $newRows = [];
                    foreach( $input_data as $key => $data ) {
                        array_push($newRows, $data);
                        $request->session()->flash('message.error', $reMess);
                    }
                }

                $rebarRef = [
                    'diamenter_id' => $input['select_diameter'],
                    'input' => $newRows,
                ];
                
                $request->session()->put('rebar.data.diameter_' . $input['select_diameter'], $rebarRef);                
            }

            // 確認画面の更新時処理
            if ($request->get('process') === 'update') {
                return redirect()->route('rebar.confirm', $request->get('select_diameter'));
            }

            // 入力画面の更新時処理
            if( $request->get('process') === 'insert' ){
                if ( (int)$input['action'] !== -1 ) {
                    $nextPage = $transArrayFlg ? $input['select_diameter'] : $input['action'];
                    return redirect()->route('rebar.register', $nextPage);
                } else {
                    $disameter = Diameter::get_first(0);
                    return redirect()->route('rebar.confirm', $disameter);
                }
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

        if( $request->session()->missing('rebar.select.now') ) {
            return redirect()->route('rebar.select');
        }

        // initial variable
        $view_data = [];

        // 鉄筋径情報取得
        $diameters = Diameter::get_all();
        $view_data["select_diameter"] = Diameter::get_by_id($select_diameter);

        // ダミーデータ
        $component = ['鉄筋', '横筋', 'ストレート筋', 'コーナー筋', 'スラブ補強筋', '継手筋', 'Z筋'];

        // pagenation
        $page = $this->pagenation($diameters, $select_diameter);

        $exist_info = $request->session()->get("rebar.data.diameter_" . $select_diameter) ?? [];

        // get select info
        $view_data['select_info'] = $request->session()->get('rebar.select.now');

        $view_data['components'] = $component;
        $view_data['diameters'] = $diameters;
        $view_data['page'] = $page;
        $view_data['exist_info'] = $exist_info;
        $view_data['error'] = $request->session()->has('message.error') ? "1" : "0";
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
                                    $this->insertExe($request, $row, $request->get('select_diameter'), $data['id']);
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
                $input_datas = $request->session()->get('rebar.data');

                if (!$input_datas) {
                    // return new throw('管理者エラー');
                }

                foreach ($input_datas as $diameter_id => $arr_inputs) {
                    foreach ($arr_inputs['input'] as $component_id => $arr_input) {
                        foreach ($arr_input['data'] as $regist_data) {
                            if ($regist_data['length'] && $regist_data['number']) {
                                $this->insertExe($request, $regist_data, $arr_inputs['diamenter_id'], $arr_input['id']);
                            }
                        }
                    }
                }

                return redirect()->route('rebar.confirm', 1);
            }
        } catch(Exception $e) {
            dd($e->getMessage());
        }
    }
    
    // *******************************************
    // 鉄筋情報登録・編集完了画面
    // *******************************************
    public function getComplete(Request $request)
    {

        try {

            // session確認
            if( $request->session()->missing('rebar.select.now') ) {
                return redirect()->route('rebar.select');
            }

            // DB処理
            $calculation_select_info = $request->session()->get('rebar.select.now');
            // dd($calculation_info);
            $calculation_query = CalculationCode::insert($calculation_select_info);

            $calculation_input_data = $request->session()->get('rebar.data');
            $user = Auth::user();

            foreach( $calculation_input_data as $key => $diameter ) {
                foreach( $diameter['input'] as $rows ) {
                    foreach( $rows['data'] as $row ) {
                        $row['diameter_id'] = $diameter['diamenter_id'];
                        $row['component_id'] = $rows['id'];
                        $row['port_id'] = 1;
                        $row['user_id'] = $user['id'];
                        $row['code'] = $calculation_query['code'];
                        CalculationRequests::ins($row);
                    }
                }
            }
            


            // 画面表示用データ
            $select_data = $request->session()->get('rebar.select.now');

            // sessionから入力情報の削除
            $request->session()->forget('rebar');

            // 画面表示
            return view('rebar.done')->with($select_data); 

        } catch(Exception $e) {
            dd($e->getMessage());
        }
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
    private function insertExe($request, array $data, $diameterId, $comonentId)
    {

        $user = Auth::user();

        $data['diameter_id'] = $diameterId;
        $data['component_id'] = $comonentId;
        $data['port_id'] = 1;
        $data['user_id'] = $user['id'];
        $data['code'] = $request->session()->get('rebar.select.now.code');
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

    public function attributes(): array
{
    return [
        'house_name' => '邸名',
    ];
}
}
