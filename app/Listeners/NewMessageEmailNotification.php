<?php

namespace App\Listeners;

use Log;
use Mail;
use Pusher;
use App\User;
use App\Models\Room;
use App\Events\MessagePosted;
use App\Mail\ChatMessageReceived;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMessageEmailNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MessagePosted $event)
    {
        $author = $event->user;
        $message = $event->message;
        $room = Room::find($message->room->id);
        $members = $room->users;

        $pusher = new Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            array('cluster' => env('PUSHER_APP_CLUSTER'))
        );

        $users = [];
        $response = $pusher->get('/channels/presence-room/users');

        if($response['status'] === 200) {
            $users = $response['result']['users'];
        }

        $onlineUsers = [];
        foreach ($users as $user) {
            array_push($onlineUsers, $user['id']);
        }

        foreach ($members as $member) {
            if($author->id !== $member->id &&
                $member->pivot->email_notification
            ) {
                if(!array_search($member->id, $onlineUsers)) {
                    Log::info('sending email notification to ' . $member->username);
                    Mail::to($member->email)
                        ->send(new ChatMessageReceived($member, $room, $message, $author));
                }
            }
        }
    }
}
