<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Diameter;

class DiametersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Diameter::create([
            'size'      => 10,
            'length'    => 8000,
            'max_limit' => 25,
        ]);
        
        Diameter::create([
            'size'      => 13,
            'length'    => 8000,
            'max_limit' => 19,
        ]);

        Diameter::create([
            'size'      => 16,
            'length'    => 8000,
            'max_limit' => 15,
        ]);

        Diameter::create([
            'size'      => 19,
            'length'    => 8000,
            'max_limit' => 12,
        ]);

        Diameter::create([
            'size'      => 22,
            'length'    => 8000,
            'max_limit' => 10,
        ]);
    }
}
