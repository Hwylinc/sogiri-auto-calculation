<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CalculationRequests;
use App\Models\CalculationCode;
use App\Models\Diameter;

class CalculationRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calculation_code = \App\Models\CalculationCode::where('id', 1)->first();
        
        $data = array();
        $data = $this->getTestDataFromCsv();

        $insert_data = [];
        $insert_data['code'] = $calculation_code['code'];

        foreach ($data as $size => $value) {
            $str = ltrim($size, 'D');
            $diameter = \App\Models\Diameter::where('size',$str)->first();
            $count = 1;
            foreach ($value as $port_length => $number) {
                $temp = explode('-',$port_length);
                $insert_data['length']        = $temp[1];
                $insert_data['number']        = $number;
                $insert_data['diameter_id']   = $diameter['id'];
                $insert_data['component_id']  = rand(1, 5);
                $insert_data['port_id']       = intval($temp[0]);
                $insert_data['client_id']     = 1;
                $insert_data['house_name']    = 'テスト邸';
                $insert_data['display_order'] = $count;
                $insert_data['user_id']       = 1;

                \App\Models\CalculationRequests::create([
                    "code"          => $insert_data["code"],
                    "length"        => $insert_data["length"],
                    "number"        => $insert_data["number"],
                    "diameter_id"   => $insert_data["diameter_id"],
                    "component_id"  => $insert_data["component_id"],
                    "port_id"       => $insert_data["port_id"],
                    "client_id"     => $insert_data["client_id"],
                    "house_name"    => $insert_data["house_name"],
                    "display_order" => $insert_data["display_order"],
                    "user_id"       => $insert_data["user_id"],
                ]);
                $count++;
            }
        }
    }

    public function getTestDataFromCsv()
    {        
        $csv = file(storage_path('app/public/real_data.csv'));
        $data = array();
        // CSVのデータを読み込む
        foreach($csv as $key => $value){
            // ヘッダースキップ
            if($key == 0 ) {
                continue;
            }
            // 1行のデータをコンマで分割する
            $temp   = explode(',', $value);
            $length = str_replace("\r\n", '', $temp[0]); // 切断長
            $set    = str_replace("\r\n", '', $temp[1]); // 数量
            $rap    = str_replace("\r\n", '', $temp[2]); // スターラップ数
            $size   = str_replace("\r\n", '', $temp[3]); // 径
            $amount = $set*$rap;
            // 吐き出し口を長さ対応で出す場合は分岐必要
            $port   = rand(1, 5); // 吐出口

            // 同じ部材カテゴリ・長さごとの数を数える
            if (isset($data[$size][$port.'-'.$length])) {
                $data[$size][$port.'-'.$length] = $data[$size][$port.'-'.$length]+$amount;
            } else {
                $data[$size][$port.'-'.$length] = $amount;
            }
        }
        return $data;
    }
}
