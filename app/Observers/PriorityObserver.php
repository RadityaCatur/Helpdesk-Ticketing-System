<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Priority;

class PriorityObserver
{

    public static $observes = [
        Priority::class
    ];
    
    /**
     * Handle the priority "created" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function created(Priority $priority)
    {
        activity()
            ->performedOn($priority)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah menambahkan daftar prioritas baru dengan nama ' . $priority->name);
    }

    /**
     * Handle the priority "updated" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function updated(Priority $priority)
    {
        activity()
            ->performedOn($priority)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah mengubah prioritas dengan nama ' . $priority->name);
    }

    /**
     * Handle the priority "deleting" event.
     * 
     * @param   \App\Models\Priority $priority
     * @return  void
     */
    public function deleting(Priority $priority) {
        $priority->projects()->each(function($project) {
            $project->priority()->dissociate();
            $project->save();
        });
    }

    /**
     * Handle the priority "deleted" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function deleted(Priority $priority)
    {
        activity()
            ->performedOn($priority)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah menghapus prioritas dengan nama ' . $priority->name);        
    }

    /**
     * Handle the priority "restored" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function restored(Priority $priority)
    {
        //
    }

    /**
     * Handle the priority "force deleted" event.
     *
     * @param  \App\Models\Priority  $priority
     * @return void
     */
    public function forceDeleted(Priority $priority)
    {
        //
    }
}
