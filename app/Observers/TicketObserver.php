<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Room;
use App\Events\RoomCreated;
use Illuminate\Support\Str;

class TicketObserver
{
    public static $observes = [
        Ticket::class
    ];
    
    /**
     * Handle the ticket "created" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        activity()
            ->performedOn($ticket)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah membuat tiket dengan subject ' . $ticket->subject);

        $adminUsers = User::whereHas('roles', function($q){ 
            $q->where('name', 'admin'); 
        })->get();

        $user = Auth::user();
        $room = new Room();        
        $room->name = Str::slug($ticket->user->fullname, '-') . '-ticket-' . $ticket->id;
        $room->ticket_id = $ticket->id;

        $user->rooms()->save($room);

        // also make the owner a member (other members can see the list of members)
        $room->users()->attach($user->id);

        // add other members to this chat room
        $room->users()->attach($adminUsers);

        // create a new broadcasted for this event
        // broadcast(new RoomCreated($room, $user));
        
        return $room;
    }    

    /**
     * Handle the ticket "updated" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function updated(Ticket $ticket)
    {
        activity()
            ->performedOn($ticket)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah mengubah tiket dengan subject ' . $ticket->subject);
    }

    /**
     * Handle the ticket "deleted" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        activity()
            ->performedOn($ticket)
            ->causedBy(Auth::user())
            ->log(Auth::user()->fullname . ' Telah menghapus tiket dengan subject ' . $ticket->subject);
    }

    /**
     * Handle the ticket "restored" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function restored(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the ticket "force deleted" event.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return void
     */
    public function forceDeleted(Ticket $ticket)
    {
        //
    }
}
