<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class ProjectObserver
{
    public static $observes = [
        Project::class
    ];

    /**
     * Handle the project "created" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function created(Project $project)
    {
        activity()
            ->performedOn($project)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah membuat project dengan nama ' . $project->name);
    }

    /**
     * Handle the project "updated" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function updated(Project $project)
    {
        activity()
            ->performedOn($project)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah mengubah projek dengan nama ' . $project->nama);
    }

    /**
     * Handle the project "deleted" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function deleted(Project $project)
    {
        $tickets = $project->tickets;
        if($tickets) {
            foreach ($tickets as $ticket) {
                $ticket->delete();
            }
        }

        activity()
            ->performedOn($project)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah menghapus projek dengan nama ' . $project->nama);
    }

    /**
     * Handle the project "restored" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function restored(Project $project)
    {
        //
    }

    /**
     * Handle the project "force deleted" event.
     *
     * @param  \App\Models\Project  $project
     * @return void
     */
    public function forceDeleted(Project $project)
    {
        //
    }
}
