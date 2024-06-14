<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserObserver
{
    public static $observes = [
        User::class
    ];

    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        if(Auth::check()) {
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log(Auth::user()->fullname . ' Telah membuat user dengan username ' . $user->username);
        }
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        if(Auth::check()) {
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log(Auth::user()->fullname . ' Telah mengubah user dengan username ' . $user->username);
        }
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $tickets = $user->tickets;
        if($tickets) {
            foreach ($tickets as $ticket) {
                $ticket->delete();
            }
        }

        $projects = $user->projects;
        if($projects) {
            foreach ($projects as $project) {
                $project->delete();
            }
        }

        activity()
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah menghapus user dengan username ' . $user->username);
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
