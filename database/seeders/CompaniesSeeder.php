<?php

namespace Database\Seeders;

use App\Models\Company;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::flushEventListeners();

        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) { 
            Company::create([
                'name' => $faker->company,
            ]);
        }
    }
}
