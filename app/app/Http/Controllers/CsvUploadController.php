<?php

namespace App\Http\Controllers;

use App\Models\CalculationRequests;
use App\Models\CalculationCode;
use App\Models\Diameter;
use App\Models\Component;
use App\Http\Controllers\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;

class CsvUploadController extends Controller
{
    // *******************************************
    // 鉄筋情報CSVアップロード画面
    // *******************************************
    public function getCsvRegister()
    {
        return view('csv.csv_upload');
    }
    
    // *******************************************
    // 鉄筋情報CSV読み込みアップロード確認画面
    // Requestはバリデーションにより適宜最適なものを使用してください
    // *******************************************
    public function postCsvUpload(Request $request)
    {
        try {
            // CSVファイルが存在するかの確認
            if ($request->hasFile('csvFile')) {
                $validate = Validator::make($request->all(), [
                    'client_name' => 'required',
                ]);
            
                if ($validate->fails()) {
                    $messages = new MessageBag;
                    $messages->add('', 'メーカーを入力してください。');
                    return redirect()->back()->withErrors($messages);
                }
                //拡張子がCSVであるかの確認
                if ($request->csvFile->getClientOriginalExtension() !== "csv") {
                    throw new Exception('不適切な拡張子です。');
                }
                //ファイルの保存
                $newCsvFileName = $request->csvFile->getClientOriginalName();
                $request->csvFile->storeAs('public/csv', $newCsvFileName);
            } else {
                throw new Exception('CSVファイルの取得に失敗しました。');
            }
            //保存したCSVファイルの取得
            $csv = Storage::disk('local')->get("public/csv/{$newCsvFileName}");
            // OS間やファイルで違う改行コードをexplode統一
            $csv = str_replace(array("\r\n", "\r"), "\n", $csv);
            // $csvを元に行単位のコレクション作成。explodeで改行ごとに分解
            $uploadedData = collect(explode("\n", $csv));
            $aryCsv = [];
            $house_name = '';
            foreach($uploadedData as $key => $value){
                if ($key == 1) {
                    $house_name = mb_substr($value, 5, NULL, "UTF-8");
                }
                if($key >= 0 && $key <= 2) {
                    continue; //1行目が見出しなど、取得したくない場合
                }
                if(!$value) {
                    continue; //空白行が含まれていたら除外
                }
                // str_replaceは配列をバラした時にダブルクオートがつきすぎるため
                $aryCsv[] = explode(",", str_replace('"', '', $value));
            }
            if (empty($aryCsv) && count($aryCsv) == 0) {
                throw new Exception('csvファイル内は空です');
            }
            // csvデータの配列を径ごとにグループ化
            $grouped_csv = $this->groupItemsByColumn($aryCsv, '2');
            $components = Component::select([
                'id',
                'name',
            ])->get();
            Storage::delete('public/csv/'. $newCsvFileName);

            return view('csv.csv_regist_before')
                    ->with([
                        'house_name'      => $house_name,
                        'csv_data'        => $grouped_csv,
                        'client_name'     => $request->client_name,
                        'components'      => $components,
                    ]);
        } catch (\Exception $ex) {
            $messages = new MessageBag;
            switch ($ex->getMessage()) {
                case 'CSVファイルの取得に失敗しました。':
                    $messages->add('', 'CSVを選択してください。');
                    break;
                case '不適切な拡張子です。':
                    $messages->add('', '正しい形式のCSVファイルをアップロードしてください。');
                    break;
                case 'csvファイル内は空です':
                    $messages->add('', 'CSVファイル内が空です。');
                    break;
                default:
                    $messages->add('', '正しい形式のCSVファイルのアップロードに失敗しました。');
                    break;
            }
            return redirect()->back()->withErrors($messages);
        }
    }


    // *******************************************
    // 鉄筋情報CSVアップロード確認画面
    // *******************************************
    public function getCsvResult(Request $request)
    {
        try {
            $request->session()->put('rebar.select.now', [
                'client_name' => $request->get('client_name')
                , 'house_name' => $request->get('house_name')
                , 'factory_id' => 2
            ]);
            if( $request->has('input') ){
                $input_data = $request->get('input');
                $checked_comp = $request->get('component');
                ksort($input_data);
                $diameters = Diameter::select([
                    'id',
                    'size',
                ])->get();
                // DBの保存されている径のサイズで回す
                foreach ($diameters as $diameter) {
                    $newRows = [];
                    // csvファイル内の径の存在チェック
                    if (!empty($input_data[$diameter->size])) {
                        // 径ことにグループ化されているのでその中にdeiplsy_order等の値を追加
                        foreach ($input_data[$diameter->size] as $key => $data) {
                            if( $checked_comp[$diameter->size] && in_array($key, $checked_comp[$diameter->size]) ) {
                                $newRow = [];
                                ksort($data['data']);
                                $count = 1;
                                foreach( $data['data'] as $order => $row ){
                                    $row['display_order'] = $count;
                                    $newRow[$count] = $row;
                                    $count++;
                                }
                                array_push($newRows, ['id' => $data['id'],'name' => $data['name'],  'data' => $newRow]); 
                            }
                        }
                        $csvRef = [
                            'diamenter_id' => $diameter->id,
                            'input' => $newRows,
                        ];
                        // csvファイル内のデータをセッションに保存
                        $request->session()->put('rebar.data.diameter_' . $diameter->id, $csvRef);
                        $newRows = [];
                    }
                }
            }
            // sessionから値を取得
            $calculation_select_info = $request->session()->get('rebar.select.now');
            $calculation_input_data = $request->session()->get('rebar.data');

            // データが1つも登録されていない場合は、前ページに戻る
            if (is_null($calculation_input_data)) {
                throw new Exception("データが1つも登録されていません。");
            }
            
            // DBに保存
            $calculation_query = CalculationCode::insert($calculation_select_info);
            session()->push('rebar.select.now.code', $calculation_query['code']);
            foreach( $calculation_input_data as $key => $diameter ) {
                foreach( $diameter['input'] as $rows ) {
                    foreach( $rows['data'] as $row ) {
                        $row['diameter_id'] = $diameter['diamenter_id'];
                        $row['component_id'] = $rows['id'];
                        $row['port_id'] = 1;
                        $row['user_id'] = auth()->user()->id;
                        $row['code'] = $calculation_query['code'];
                        CalculationRequests::ins($row);
                    }
                }
            }

            return view('csv.csv_regist_before')
                ->with([
                    'result_flag'      => true,
                    'house_name'       => $request->house_name,
                    'client_name'      => $request->client_name,
                    'code'             => $calculation_query['code'],
                    ]);
        } catch (\Exception $ex) {
            $messages = new MessageBag;
            switch ($ex->getMessage()) {
                case '筋種が空です':
                    $messages->add('', '筋種が空です');
                    break;
                case '径が空です':
                    $messages->add('', '径が空です');
                    break;
                case '長さが空です':
                    $messages->add('', '長さが空です');
                    break;
                case '本数が空です':
                    $messages->add('', '本数が空です');
                    break;
                default:
                    $messages->add('', '鉄筋情報の登録に失敗しました。');
                    break;
            }
            return redirect()->back()->withErrors($messages);
        }
        
        dd('鉄筋情報CSVアップロード確認画面');
    }

    /**
     *  配列をグループ化
     * */ 
    function groupItemsByColumn(Collection|array $items, String $column) : array
    {
        $items = is_array($items) ? $items : $items->toArray();
        if (empty($items)) return [];
        
        return array_reduce($items, function (array $acc, array $el) use ($column) {
            $group = $el[$column];
            $acc[$group][] = $el;
            return $acc;
        }, []);
    }
}
