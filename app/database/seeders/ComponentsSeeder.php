<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Component;

class ComponentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $components = ['縦筋', '横筋', 'スラブ補強筋', '加工筋'];

        foreach( $components as $component ) {
            Component::create([
                'name' => $component,
                'port_id' => 1,
                'external_component_id' => 'test1',
                'factory_id' => 2
            ]);
        }
    }
}
