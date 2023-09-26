<?php

namespace App\Http\Controllers;

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
        return view('rebar.csv_upload');
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
                    'maker_name' => 'required',
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
            $name_of_mansion = '';
            foreach($uploadedData as $key => $value){
                if ($key == 1) {
                    $name_of_mansion = mb_substr($value, 5, NULL, "UTF-8");
                }
                if($key >= 0 && $key <= 2) continue; //1行目が見出しなど、取得したくない場合
                if(!$value) continue; //空白行が含まれていたら除外
                $aryCsv[] = explode(",", $value);
            }
            // dd($aryCsv);
            return view('rebar.csv_regist_before')
                    ->with([
                        'name_of_mansion' => $name_of_mansion,
                        'csv_data'        => $aryCsv,
                        'maker_name'      => $request->maker_name,
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
                default:
                    # code...
                    break;
            }
            return redirect()->back()->withErrors($messages);
        }
        dd('鉄筋情報CSV読み込みアップロード画面');
    }


    // *******************************************
    // 鉄筋情報CSVアップロード確認画面
    // *******************************************
    public function getCsvResult(Request $request)
    {
        dd($request->all());
        try {
            // $request->validate([

            // ]);
            return view('rebar.csv_regist_before')
                ->with([
                    'result_flag'     => true,
                    'name_of_mansion' => $request->name_of_mansion,
                    'maker_name'      => $request->maker_name,
                    ]);
        } catch (\Exception $ex) {
            $messages = new MessageBag;
            $messages->add('', '鉄筋情報の登録に失敗しました。');
            return redirect()->back()->withErrors($messages);
        }
        
        dd('鉄筋情報CSVアップロード確認画面');
    }
}
