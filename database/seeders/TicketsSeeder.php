<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\Priority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Faker\Factory;
use Carbon\Carbon;

class TicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ticket::flushEventListeners();

        $faker = Factory::create();
        $projects = Project::select('id')->get();
        $priorities = Priority::select('id')->get();

        $type = [
            'service_request',
            'incident',
            'change_request',
            'bug'
        ];

        $status = [
            'open',
            'closed',
            'onProgress'
        ];

        for ($i = 0; $i < 10; $i++) {
            Ticket::create([
                'user_id' => 3,
                'project_id' => $projects->random()->id,
                'priority_id' => $priorities->random()->id,
                'subject' => $faker->sentence,
                'type' => Arr::random($type),
                'description' => $faker->paragraph,
                'started_at' => Carbon::now(),
                'ended_at' => Carbon::now()->addWeeks(4),
                'status' => Arr::random($status),
            ]);
        }
    }
}
