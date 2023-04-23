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
        $spares = [
            'd10' => [
                'name' => [
                    '180', '280', '320', '370', '380', '410', '550', '580', '700', '765', '800', '840', '860', '870', '900', '910', '915', '240',
                    '1000', '1100', '1200', '1250', '1300', '1325', '1360', '1310', '1500', '1760', '1780', '1800', '1960',
                    '2235', '2660', '2690', '2730', '3560', '3600'
                ],
                'priority_flg' => 0,
                'diameters_id' => 1,
            ],
            'd13' => [
                'name' => [
                    '180', '250', '280', '330', '550', '860', '870', '910', '940', 
                    '1000', '1050', '1100', '1200', '1300', '1310', '1325', '1400', '1450',  '1500', '1530', '1550', '1600', '1650', '1720', '1760', '1780', '1800', '1960',
                    '2000', '2100', '2235', '2660', '2690', '3600'
                ],
                'priority_flg' => 0,
                'diameters_id' => 2,
            ],
            'd16' => [
                'name' => [
                    '420', '825', '860', '870',
                    '1310', '1380', '1376', '1400', '1500', '1760', '1780', '1960',
                    '2000', '2235', '2300', '2660', '2690', '3560', '3600'
                ],
                'priority_flg' => 0,
                'diameters_id' => 3,
            ],
            'd19' => [
                'name' => [
                    '930', '1580', '1680', '1710', '1800', '1830', '2200', '2230', '2280', '2580', '2730', '3630'
                ],
                'priority_flg' => 0,
                'diameters_id' => 4,
            ],
            'd22' => [
                'name' => [
                    '2000', '2400'
                ],
                'priority_flg' => 0,
                'diameters_id' => 5,
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
