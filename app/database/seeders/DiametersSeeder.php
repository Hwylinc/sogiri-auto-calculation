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
            'size' => 10,
        ]);

        Diameter::create([
            'size' => 13,
        ]);

        Diameter::create([
            'size' => 16,
        ]);

        Diameter::create([
            'size' => 19,
        ]);

        Diameter::create([
            'size' => 22,
        ]);
    }
}
