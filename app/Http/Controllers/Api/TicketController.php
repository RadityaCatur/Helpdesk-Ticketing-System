<?php

namespace App\Http\Controllers\Api;

use DateTime;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketFile;
use App\Http\Resources\TicketResource;
use App\Notifications\NewTicketNotification;
use App\Notifications\UpdateTicketNotification;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = request()->status;
        if (auth()->user()->hasRole('pic')) {
            $tickets = Ticket::where('status', $status)
                ->where('user_id', auth()->user()->id)
                ->orderBy('id', 'DESC')
                ->when($request->q, function ($tickets) use ($status) {
                    $tickets = $tickets
                        ->where([
                            ['subject', 'ILIKE', '%' . request()->q . '%'],
                            ['status', $status]
                        ])
                        ->orWhere([
                            ['id', 'ILIKE', '%' . request()->q . '%'],
                            ['status', $status]
                        ]);
                })->get();
            foreach ($tickets as $ticket) {
                $ticket->project;
                if ($ticket->room) {
                    $ticket->room->users;
                    $ticket->room->messages;
                }
            }

            return response()->json([
                'data' => $tickets
            ], 200);
        } else if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('employee')) {
            if ($status) {
                $tickets = Ticket::where('status', $status)
                    ->orderBy('id', 'DESC')
                    ->when($request->q, function ($tickets) use ($status) {
                        $tickets = $tickets
                            ->where([
                                ['subject', 'ILIKE', '%' . request()->q . '%'],
                                ['status', $status]
                            ])
                            ->orWhere([
                                ['id', 'ILIKE', '%' . request()->q . '%'],
                                ['status', $status]
                            ]);
                    })->get();

                foreach ($tickets as $ticket) {
                    $ticket->project;
                    if ($ticket->room) {
                        $ticket->room->users;
                        $ticket->room->messages;
                    }
                }

                return response()->json([
                    'data' => $tickets
                ]);
            } else {
                $tickets = Ticket::orderByRaw("case status when 'open' then 1 when 'onProgress' then 2 when 'closed' then 3 end")
                    ->orderBy('id', 'DESC')
                    ->when($request->q, function ($tickets) {
                        $tickets = $tickets->where('subject', 'ILIKE', '%' . request()->q . '%')
                            ->orWhere('status', 'ILIKE', '%' . request()->q . '%')
                            ->orWhere('id', 'ILIKE', '%' . request()->q . '%');
                    })->get();
                foreach ($tickets as $ticket) {
                    $ticket->project;
                    if ($ticket->room) {
                        $ticket->room;
                    }
                }

                return response()->json([
                    'data' => $tickets
                ]);
            }
        }
    }

    public function openChat($id)
    {
        $ticket = Ticket::where('id', $id)->first();
        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }
        $ticket->room->users;
        $ticket->room->messages;

        return response()->json($ticket);
    }

    public function export(Request $request)
    {
        $tickets = Ticket::orderByRaw("case status when 'open' then 1 when 'onProgress' then 2 when 'closed' then 3 end")
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json([
            'data' => $tickets
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'subject' => 'required|string',
            'type' => 'required',
            'description' => 'required',
        ]);
        $ticket = Ticket::create([
            'user_id' => auth()->user()->id,
            'project_id' => $request->project_id,
            'subject' => $request->subject,
            'type' => $request->type,
            'description' => $request->description
        ]);


        // Check if request has files
        if ($request->file()) {
            $allowedfileExtension = [
                'jpeg', 'jpg', 'png',
                'gif', 'pdf', 'docx',
                'csv', 'txt', 'xlsx',
            ];

            $files = $request->file('files');

            // Store each file
            foreach ($files as $file) {
                $filename = time() . '-' . preg_replace('/\s+/', '', $file->getClientOriginalName());
                $extension = $file->getClientOriginalExtension();

                $fileAllowed = in_array($extension, $allowedfileExtension);

                if ($fileAllowed) {
                    $file->storeAs('uploads', $filename, 'public');
                    TicketFile::create([
                        'ticket_id' => $ticket->id,
                        'filename' => $filename,
                        'extension' => $extension
                    ]);
                } else {
                    return response()->json([
                        'message' => 'File not allowed'
                    ], 403);
                }
            }
        }

        // Get All Admin Users
        $admin = User::role('admin')->get();
        foreach ($admin as $item) {
            if ($item->hasVerifiedEmail() && $item->active) {
                // Send Email Notification to each admin
                $item->notify(new NewTicketNotification($ticket));
            }
        }

        return response()->json([
            'message' => 'Ticket successfully created',
            'data' => $ticket
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = Ticket::where('id', $id)->first();
        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->files;
        $ticket->user;
        $ticket->room;
        $ticket->user->company;
        $ticket->project;

        return response()->json([
            'data' => (new TicketResource($ticket))
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ticket = Ticket::where('id', $id)->first();
        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }

        $date1 = new DateTime($request->started_at);
        $date2 = new DateTime($request->ended_at);

        if ($date2 < $date1) {
            return response()->json([
                'message' => 'Waktu berakhir tidak dapat kurang dari waktu mulai.'
            ], 422);
        } else {
            $ticket->update([
                'status' => $request->status,
                'response' => $request->response,
                'started_at' => $request->started_at,
                'ended_at' => $request->ended_at
            ]);
            $ticket->save();

            if ($ticket->user->hasVerifiedEmail() && $ticket->user->active) {
                $ticket->user->notify(new UpdateTicketNotification($ticket));
            }

            return response()->json([
                'data' => $ticket,
                'message' => 'Update Successfully'
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ticket = Ticket::where('id', $id)->first();
        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->delete();

        return response()->json([
            'message' => 'Ticket successfully deleted'
        ], 200);
    }
}
