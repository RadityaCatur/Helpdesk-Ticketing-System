<?php

namespace Database\Seeders;

use App\Models\Position;
use Faker\Factory;
use Illuminate\Database\Seeder;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Position::flushEventListeners();

        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) { 
            Position::create([
                'name' => $faker->jobTitle,
            ]);
        }
    }
}
