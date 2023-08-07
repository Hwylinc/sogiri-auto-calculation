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
    public function getSelect(Request $request)
    {           
        
        // メーカーを全件取得
        $clients = Client::get_all();

        // ログインユーザのfactory_idを取得する
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
        // session保存内容を削除
        $request->session()->forget('rebar.data');

        // validation処理
        $request->validate([
              'client_id' => 'required'
            , 'house_name' => 'required',
        ]);

        // クライアント名を取得する
        $client = Client::get_by_id($request->get('client_id'));
    
        // sessionに履歴を保存
        $request->session()->put('rebar.select.now', [
              'client_id' => $request->get('client_id')
            , 'client_name' => $client['name']
            , 'house_name' => $request->get('house_name')
            // , 'factory_id' => $request->get('factory_id') 工場選択非表示の間は、デフォルト2で対応
            , 'factory_id' => 2
        ]);

        // idが一番若いdiameterを取得する
        $disameter = Diameter::get_first(0);

        // getRegisterに遷移
        return redirect()->route(
              'rebar.register'
            , ['diameter' => $disameter['id']]
        );
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
            // 鉄筋径情報取得
            $diameters = Diameter::get_all();
            $view_data["select_diameter"] = Diameter::get_by_id($select_diameter);

            $view_data['components'] = Component::get_all();

            // pagenation
            $view_data['page'] = $this->pagenation($diameters, $select_diameter);

            // session get info
            $view_data['exist_info'] = [];
            if ($request->session()->has('rebar.data.diameter_' . $select_diameter)) {
                $view_data['exist_info'] = $request->session()->get('rebar.data.diameter_' . $select_diameter);
            }

            // get select info
            $view_data['select_info'] = $request->session()->get('rebar.select.now');

            // 選択できるfacotry_id
            $view_data['factory_id'] = Auth::user()['factory_id'];
            $view_data['diameters'] = $diameters;
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

        $newRows = [];
        $transArrayFlg = false;
        $message = "";

        try {

            if( $request->has('input') ){
                $input_data = $request->get('input');
                $checked_comp = $request->get('component');
                ksort($input_data);

                foreach( $input_data as $key => $data ){
                    if( $checked_comp && in_array($key, $checked_comp) ) {
                        $newRow = [];
                        ksort($data['data']);
                        $count = 1;
                        foreach( $data['data'] as $order => $row ){
                            if (
                                ( !is_null($row['length']) && $row['length'] !== "" ) 
                                || ( !is_null($row['number']) && $row['number'] !== "" )
                            ) {
                                // validation check
                                $message = $this->customValidate($row);
                                $row['display_order'] = $count;
                                $newRow[$count] = $row;
                                $count++;
                                if ( !($message == 'OK') ) {
                                    $transArrayFlg = true;
                                    break 2;
                                } 
                            } 
                        }

                        if($count === 1 ) {
                            $transArrayFlg = true;
                            $message = "選択された部材は、入力必須です";
                            break;
                        }

                        array_push($newRows, ['id' => $data['id'],'name' => $data['name'],  'data' => $newRow]);   
                    }
                }
                
                if ($transArrayFlg) {
                    $newRows = [];
                    foreach( $input_data as $key => $data ) {
                        array_push($newRows, $data);
                        $request->session()->flash('message.error', $message);
                    }
                }

                $rebarRef = [
                    'diamenter_id' => $request->get('select_diameter'),
                    'input' => $newRows,
                ];
                
                $request->session()->put('rebar.data.diameter_' . $request->get('select_diameter'), $rebarRef);                
            }

            // 確認画面の更新時処理
            if ($request->get('process') === 'update') {
                return redirect()->route('rebar.confirm', $request->get('select_diameter'));
            }

            // 入力画面の更新時処理
            if( $request->get('process') === 'insert' ){

                if( $transArrayFlg ) {
                    return redirect()->route('rebar.register', $request->get('select_diameter'));
                }

                if ( (int)$request->get('action') !== -1 ) {
                    return redirect()->route('rebar.register', $request->get('action'));
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
        // session確認
        if( $request->session()->missing('rebar.select.now') ) {
            return redirect()->route('rebar.select');
        }

        // initial variable
        $view_data = [];

        // 鉄筋径情報取得
        $diameters = Diameter::get_all();
        $view_data["select_diameter"] = Diameter::get_by_id($select_diameter);

        // pagenation
        $view_data['page'] = $this->pagenation($diameters, $select_diameter);

        $view_data['exist_info'] = $request->session()->get("rebar.data.diameter_" . $select_diameter) ?? [];

        // get select info
        $view_data['select_info'] = $request->session()->get('rebar.select.now');

        $view_data['diameters'] = $diameters;
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

            // sessionから値を取得
            $calculation_select_info = $request->session()->get('rebar.select.now');
            $calculation_input_data = $request->session()->get('rebar.data');

            // データが1つも登録されていない場合は、前ページに戻る
            if (is_null($calculation_input_data)) {
                throw new Exception("データが1つも登録されていません。");
            }

            $user = Auth::user();
            
            // DBに保存
            $calculation_query = CalculationCode::insert($calculation_select_info);
            
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
            $this->addFlash($request, 'error', $e->getMessage());
            return back()->withInput();
        }
    }
    
    // *******************************************
    // 鉄筋情報詳細画面
    // *******************************************
    public function getDetail($calculation_id)
    {
        dd('鉄筋情報詳細画面');
    }

    /** 独自バリデーション
     * 
     */
    private function customValidate(array $data): string 
    {
        if ( is_null($data['number']) || $data['number'] === "" ) {
            return '長さを入力した場合、本数は入力必須です';
        } elseif ( is_null($data['length']) || $data['length'] === "" ) {
            return '本数を入力した場合、長さは入力必須です';
        } else {
            return 'OK';
        }
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
