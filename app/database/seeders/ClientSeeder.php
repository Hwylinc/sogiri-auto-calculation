<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;


class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        \App\Models\Client::create([
            'name' => '幸栄建設',
            'external_client_id' => '001',
        ]);
        \App\Models\Client::create([
            'name' => 'Panasonic Homes',
            'external_client_id' => '002',
        ]);
        \App\Models\Client::create([
            'name' => '住友不動産',
            'external_client_id' => '003',
        ]);
        \App\Models\Client::create([
            'name' => 'アーネストワン',
            'external_client_id' => '004',
        ]);
        \App\Models\Client::create([
            'name' => '一建設',
            'external_client_id' => '005',
        ]);
        \App\Models\Client::create([
            'name' => 'ファースト住建',
            'external_client_id' => '006',
        ]);
        \App\Models\Client::create([
            'name' => 'ビオラホーム',
            'external_client_id' => '007',
        ]);
        \App\Models\Client::create([
            'name' => '秀光ビルド',
            'external_client_id' => '008',
        ]);
        \App\Models\Client::create([
            'name' => 'メークス',
            'external_client_id' => '009',
        ]);
        \App\Models\Client::create([
            'name' => '吉村一建設',
            'external_client_id' => '010',
        ]);
        \App\Models\Client::create([
            'name' => '三菱地所ホーム',
            'external_client_id' => '011',
        ]);
        \App\Models\Client::create([
            'name' => '小林住宅',
            'external_client_id' => '012',
        ]);
        \App\Models\Client::create([
            'name' => '積水ハウス',
            'external_client_id' => '013',
        ]);
        \App\Models\Client::create([
            'name' => '大道',
            'external_client_id' => '014',
        ]);
        \App\Models\Client::create([
            'name' => '大野組',
            'external_client_id' => '015',
        ]);
        \App\Models\Client::create([
            'name' => '谷田工務店',
            'external_client_id' => '016',
        ]);
        \App\Models\Client::create([
            'name' => '北斗建設',
            'external_client_id' => '017',
        ]);
        \App\Models\Client::create([
            'name' => '野原住環境',
            'external_client_id' => '018',
        ]);
        \App\Models\Client::create([
            'name' => 'ケイアイプランニング',
            'external_client_id' => '019',
        ]);
        \App\Models\Client::create([
            'name' => 'ビオラホーム',
            'external_client_id' => '020',
        ]);
        \App\Models\Client::create([
            'name' => 'ヤマト住建',
            'external_client_id' => '021',
        ]);
        \App\Models\Client::create([
            'name' => 'タクトホーム',
            'external_client_id' => '022',
        ]);
//          Client::create([
//             'name' => '吉田',
//              'external_client_id' => 1,
//      ]);

//        Client::create([
//            'name' => '田中',
//            'external_client_id' => 1,
//        ]);

//        Client::create([
//            'name' => '鈴木',
//            'external_client_id' => 1,
//        ]);

//        Client::create([
//            'name' => '今田',
//            'external_client_id' => 1,
    }
}
