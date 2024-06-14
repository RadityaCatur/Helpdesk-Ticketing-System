<?php

namespace Database\Seeders;

use App\Models\Broadcast;
use App\Models\Project;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Broadcast::flushEventListeners();

        $faker = Factory::create();       
        $project = Project::select('*')->get();        
        $employess = User::select('*')->get();

        $status = [
            'semua',
            'project',
            'user'
        ];
        
        $statusBc = Arr::random($status);
        $idProject = $project->random()->id;
        
        if ($statusBc == 'semua') {
            for($i=0; $i < count($employess); $i++) {
                Broadcast::create([
                    'user_id' => $employess[$i]->id,   
                    'tipe' => $statusBc, 
                    'subjek' => $faker->sentence($nbWords = 6, $variableNbWords = true),
                    'isi' => $faker->paragraph(5),
                ]);
            }
        } elseif ($statusBc == 'project') {
            Broadcast::create([
                'project_id' => $idProject,   
                'tipe' => $statusBc, 
                'subjek' => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'isi' => $faker->paragraph(5),
            ]);
        } else {
            Broadcast::create([
                'user_id' => $employess->random()->id,   
                'tipe' => $statusBc, 
                'subjek' => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'isi' => $faker->paragraph(5),
            ]);
        }
    }
}
