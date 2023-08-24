<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Spare;

class SpareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Spare::truncate();

        $spares = [
            'd10' => [
                'name' => [
                    '600', '630', '640', '650', '740', '750', '760', '770', '780', '800', '820', '830', '840', '880', '890', '900', '960', '980', '1000', '1010', '1060', '1090', '1110', '1140', '1160', '1250', '1280', '1350', '1470', '1480'
                ],
                'priority_flg' => 0,
                'diameters_id' => 1,
                'order_number' => 999,
            ],
            'd13' => [
                'name' => [
                    '1200', '1180', '2000', '1100', '1080', '1250', '1230', '1600', '1500', '1580'
                ],
                'priority_flg' => 0,
                'diameters_id' => 2,
                'order_number' => 999,
            ],
            'd16' => [
                'name' => [
                    '850', '1050', '1400', '1500', '1900', '2300'
                ],
                'priority_flg' => 0,
                'diameters_id' => 3,
                'order_number' => 999,
            ],
            'd19' => [
                'name' => [
                    '930', '1580', '1680', '1710', '1800', '1830', '2200', '2230', '2280', '2580', '2730', '3630'
                ],
                'priority_flg' => 0,
                'diameters_id' => 4,
                'order_number' => 999,
            ],
            'd22' => [
                'name' => [
                    '2000', '2400'
                ],
                'priority_flg' => 0,
                'diameters_id' => 5,
                'order_number' => 999,
            ],
        ];
        
        foreach($spares as $spare_name => $array) {
            foreach($array['name'] as $value) {
                Spare::create([
                    'name' => $value,
                    'priority_flg' => $array['priority_flg'],
                    'diameters_id' => $array['diameters_id'],
                ]);
            }
        }

       

        

        

        

        
    }
}
