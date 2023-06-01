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
            'name' => 'オープンハウス',
            'external_client_id' => 'test1',
        ]);
        \App\Models\Client::create([
            'name' => 'アイ工務店',
            'external_client_id' => 'test2',
        ]);
        \App\Models\Client::create([
            'name' => 'タマホーム',
            'external_client_id' => 'test3',
        ]);
    }
}
