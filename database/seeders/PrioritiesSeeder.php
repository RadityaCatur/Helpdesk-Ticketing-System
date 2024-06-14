<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Priority::flushEventListeners();

        $priorities = [
            'Urgent',
            'Major',
            'Minor',
        ];

        foreach ($priorities as $priority) {
            Priority::create([
                'name' => $priority
            ]);
        }
    }
}
