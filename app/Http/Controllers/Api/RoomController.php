<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\Room;
use App\Events\RoomTyping;
use App\Events\RoomCreated;
use App\Events\RoomUpdated;
use App\Events\RoomDeleted;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    
    /**
     * Get a list of all chatrooms a user is member of
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        // get current user
        $user = Auth::user();

        // get list of all chat rooms this user is member of and
        // make sure that most recently updated room
        $rooms = $user->memberships;

        foreach ($rooms as $key => $value) {
            $room = Room::find($value->id);
            $value->users = $room->users;
            $value->messages = $room->messages;
        }
        
        return $rooms;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Room $room Model
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room) {
        $chatRoom = Room::find($room->id);
        $chatRoom->messages = $room->messages;
        return $chatRoom;
    }

    /**
     * Set reading progress of a user in a room
     * 
     * @param   \App\Models\Room    $room   Model
     * 
     * @return  \Illuminate\Http\Response
     */
    public function setReading(Room $room) {
        // Get current user
        $user = Auth::user();

        // set the updated_at date in the pivot table
        // to indicated the reading progress of the user in this room
        $memberships = $room->users()->where('user_id', $user->id)->first();
        $memberships->pivot->touch();

        // inform all subscribers of this change
        // broadcast(new RoomUpdated($room, $user));

        return response($room);
    }

    /**
     * Change email notification setting of a user for new messages in a room
     *
     * @param \App\Room                $room    Room Model
     * @param \Illuminate\Http\Request $request HTTP data
     *
     * @return \Illuminate\Http\Response
     */
    public function setEmailNotification(Room $room, Request $request) {
        // get current user
        $user = Auth::user();

        $user->memberships()
            ->updateExisitingPivot(
                $room->id,
                ['email_notification' => $request->emailNotification]
            );

        return $room;
    }

    /**
     * Set Typing indicator in chat room
     * 
     * @param   \App\Models\Room    $room   Model
     * 
     * @return  \Illuminate\Http\Response
     */
    public function typing(Room $room) {
        // get current user and send 'typing' broadcast
        $user = Auth::user();
        // broadcast(new RoomTyping($room, $user));

        return response([
            'status' => 'OK'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Room $room Model
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room) {
        // Check if room exists and user owns this room
        if(!$room || $room->owner->id !== Auth::user()->id) {
            return;
        }

        // Broadcast the delete event
        \Log::info('RoomDeleted event prepared! id: ' . $room->id);
        // broadcast(new RoomDeleted($room->id));

        // remove all attached users (chatroom members)
        $room->users()->detach();

        // delete all attached files to thoose messages
        $room->messages->map(
            function($message, $key) {
                if($message->filename) {
                    $path = public_path() . '/images/';
                    unlink($path.$message->filename);
                }
            }
        );

        // delete all related messages
        $room->messages()->delete();

        // now delete the room
        $room->delete();

        return 'deleted';
    }

}
