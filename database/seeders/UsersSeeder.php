<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Faker\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::flushEventListeners();

        $faker = Factory::create();
        $companies = Company::select('id')->get();

        $admin = User::create([
            'fullname' => 'Admin Helpdesk',
            'username' => 'admin',
            'email' => 'radityacaturn@gmail.com',
            'handphone' => $faker->phoneNumber,
            'alamat' => $faker->address,
            'password' => Hash::make('password'),
            'active' => 1,
            'email_verified_at' => now(),
            'company_id' =>  $companies->random()->id,
        ]);

        $employee = User::create([
            'fullname' => 'Employee',
            'username' => 'employee',
            'email' => 'radityacatur09@gmail.com',
            'handphone' => $faker->phoneNumber,
            'alamat' => $faker->address,
            'password' => Hash::make('password'),
            'active' => 1,
            'email_verified_at' => now(),
            'company_id' => $companies->random()->id,
        ]);

        $client = User::create([
            'fullname' => 'PIC',
            'username' => 'pic',
            'email' => 'radityacatur01@gmail.com',
            'handphone' => $faker->phoneNumber,
            'alamat' => $faker->address,
            'password' => Hash::make('password'),
            'active' => 1,
            'email_verified_at' => now(),
            'company_id' => $companies->random()->id,
        ]);
    }
}
