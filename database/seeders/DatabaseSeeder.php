<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {            
        $faker = Factory::create();
                
        $this->call(CategoriesSeeder::class);
        $this->call(PrioritiesSeeder::class);
        $this->call(CompaniesSeeder::class);                
        $this->call(PositionsSeeder::class);      
        $this->call(UsersSeeder::class);         
        $this->call(RolesPermissionsSeeder::class);
        $this->call(ProjectsSeeder::class);         
        $this->call(TicketsSeeder::class);                                            
    }
}
