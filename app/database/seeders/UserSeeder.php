<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \App\Models\User::create([
            'name' => '上位管理者',
            'email' => 'testadmin@sogiri.jp',
            'password' => Hash::make('testadmin'),
            'authority' => '1',
            'factory_id' => config('const.factory_key.ono'),
        ]);

        \App\Models\User::create([
            'name' => '下位管理者　三田',
            'email' => 'sandaadmin@sogiri.jp',
            'password' => Hash::make('sandaadmin'),
            'authority' => '2',
            'factory_id' => config('const.factory_key.sanda'),
        ]);

        \App\Models\User::create([
            'name' => '下位管理者　小野',
            'email' => 'onoadmin@sogiri.jp',
            'password' => Hash::make('onoadmin'),
            'authority' => '2',
            'factory_id' => config('const.factory_key.ono'),
        ]);

        \App\Models\User::create([
            'name' => '作業者　小野',
            'email' => 'onouser@sogiri.jp',
            'password' => Hash::make('onouser'),
            'authority' => '3',
            'factory_id' => config('const.factory_key.ono'),
        ]);
        
        \App\Models\User::create([
            'name' => '作業者　三田',
            'email' => 'sandauser@sogiri.jp',
            'password' => Hash::make('sandauser'),
            'authority' => '3',
            'factory_id' => config('const.factory_key.sanda'),
        ]);
    }
}
