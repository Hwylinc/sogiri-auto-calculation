<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CalculationCode;

class CalculationCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $house_name = ['佐藤邸', '田中邸', '山田邸', '山本邸', '斎藤邸', '藤田邸', '藤原邸', '加藤邸', '岡本邸'];
        $clients = \App\Models\Client::all();
        for ($i=0; $i < 7; $i++) { 
            $code = uniqid();
            $client_id = $clients[mt_rand(0, 2)]['id'];
            \App\Models\CalculationCode::create([
                'code'               => $code,
                'client_id'          => $client_id,
                'house_name'         => $house_name[$i],
                'factory_id'         => mt_rand(1, 2),
                'calculation_status' => 2,
            ]);
        }
    }
}
