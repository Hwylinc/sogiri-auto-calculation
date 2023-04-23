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
            'size' => 'D10',
        ]);

        Diameter::create([
            'size' => 'D13',
        ]);

        Diameter::create([
            'size' => 'D16',
        ]);

        Diameter::create([
            'size' => 'D19',
        ]);

        Diameter::create([
            'size' => 'D22',
        ]);
    }
}
