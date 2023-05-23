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
        Client::create([
            'name' => '吉田',
            'external_client_id' => 1,
        ]);

        Client::create([
            'name' => '田中',
            'external_client_id' => 1,
        ]);

        Client::create([
            'name' => '鈴木',
            'external_client_id' => 1,
        ]);

        Client::create([
            'name' => '今田',
            'external_client_id' => 1,
        ]);
    }
}
