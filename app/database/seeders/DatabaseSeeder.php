<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\DiametersSeeder;
use Database\Seeders\SpareSeeder;
use Database\Seeders\ClientSeeder;
use Database\Seeders\ComponentsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // exe 
        // $this->call(DiametersSeeder::class);    // diameters seeder
        // $this->call(SpareSeeder::class);        // spare seeder
        // $this->call(ClientSeeder::class);        // client seeder
        $this->call(ComponentsSeeder::class);         
    }
}
