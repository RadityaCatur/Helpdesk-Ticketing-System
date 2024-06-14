<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Faker\Factory;
use Carbon\Carbon;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::flushEventListeners();

        $faker = Factory::create();
        $categories = Category::select('id')->get();
        $priorities = Priority::select('id')->get();
        $employess = User::role('employee')->select('id')->get();

        $projects = [
            'ads_xpress',
            'bisniso',
            'panen_panen'
        ];

        $status = [
            'maintaince',
            'warranty'
        ];

        $service_level_agreement = [
            'service_request',
            'incident',
            'change_request',
            'bug'
        ];

        $jumlah_hari = [3, 5, 7, 10, 12];

        foreach ($projects as $project) {
            Project::create([
                'user_id' => 3,
                'category_id' => $categories->random()->id,
                'priority_id' => $priorities->random()->id,
                'leader_id' => $employess->random()->id,
                'name' => $project,
                'contract' => $faker->randomNumber,
                'contract_started_at' => Carbon::now(),
                'contract_ended_at' => Carbon::now()->addWeeks(2),
                'service_level_agreement' => Arr::random($service_level_agreement),
                'jumlah_hari' => Arr::random($jumlah_hari),
                'status' => Arr::random($status),
                'started_at' => Carbon::now(),
                'ended_at' => Carbon::now()->addWeeks(4)
            ]);
        }
    }
}
