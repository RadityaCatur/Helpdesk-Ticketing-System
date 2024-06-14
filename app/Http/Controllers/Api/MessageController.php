<?php

namespace App\Http\Controllers\Api;

use Log;
use Auth;
use File;

use App\Models\Room;
use App\Models\Message;

use App\Events\RoomUpdated;
use App\Events\MessagePosted;
use App\Events\MessageUpdated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    
    /**
     * Display a listing of the resource
     * 
     * @return  \Illuminate\Http\Response
     */
    public function index() {
        return Message::with('user')->get();
    }
    
    public function store(Request $request) {        
        $user = Auth::user();
        $message = $request->get('message');
        $room_id = $request->get('room_id');

        // Check if we have a message and if the user is member of this room
        if(strlen($message) && $user->isMemberOf($room_id)) {
            $message = new Message([
                'message' => $message
            ]);
            $message->user_id = $user->id;
            $room = Room::find($room_id);
            $room->messages()->save($message);
            
            $membership = $user->memberships()->where('room_id', $room_id)->first();
            $membership->pivot->touch();
            
            // broadcast(new RoomUpdated($room, $user));            
            // broadcast(new MessagePosted($message, $user));

            return response([
                'status' => 'OK'
            ]);
        }

        return 'failed';
    }
    
    public function upload(Request $request, Room $room) {
        $user = Auth::user();

        if($user->isMemberOf($room->id) && $request->file('file')) {            
            $image = $request->file('file');
            $filename = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $name = time().$filename;
            
            switch ($extension) {
                case 'mp3': 
                    $type = 'audio';
                    break;
                case 'aac': 
                    $type = 'audio';
                    break;
                case 'amr': 
                    $type = 'audio';
                    break;
                case 'mp4': 
                    $type = 'video';
                    break;
                case 'png': 
                    $type = 'image';
                    break;
                case 'gif': 
                    $type = 'image';
                    break;
                case 'jpg': 
                    $type = 'image';
                    break;
                case 'jpeg': 
                    $type = 'image';
                    break;
                default: $type = explode('/', $image->getMimeType())[0];
            }
            
            if(!$type)
                $type = 'audio';

            $image->move(public_path('/images/'), $name);

            $message = new Message([
                'message' => $filename,
                'filename' => $name,
                'filetype' => $type
            ]);

            $message->user_id = $user->id;
            $room->messages()->save($message);
            
            $membership = $user->memberships()->where('room_id', $room->id)->first();
            $membership->pivot->touch();
            
            // broadcast(new RoomUpdated($room, $user));            
            // broadcast(new MessagePosted($message, $user));

            return $name;
        }

        return 'failed!';
    }
    
    public function destory($id) {
        $message = Message::where('id', $id)->first();
        $user = Auth::user();

        if($user->id === $message->user_id) {
            if($message->filename) {
                $path = public_path('/images/');
                unlink($path.$message->filename);

                if(file_exists($path.$message->filename))
                    Log::info($path." file $message->filename was NOT deleted");
                else
                    Log::info($path." file $message->filename was deleted");
            }
            
            $message->update([
                'message' => $message->updated_at,
                'filename' => null,
                'filetype' => null
            ]);

            // broadcast(new MessageUpdated($message, $user));

            return 'deleted';
        }
    }

}
