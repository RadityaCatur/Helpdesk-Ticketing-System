<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Ticket;
use Carbon\Carbon;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Auth;
use DB;

class DashboardController extends Controller
{
    public function index() {
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('employee')) {
            $projects = Project::all();
            $tickets = Ticket::all();
            return response()->json([
                'projects' => ProjectResource::collection($projects),
                'tickets' => TicketResource::collection($tickets)
            ], 200);
        } else {
            $projects = Project::where('company_id', Auth::user()->company_id)->get();
            $tickets = Ticket::where('user_id', Auth::user()->id)->get();
            return response()->json([
                'projects' => ProjectResource::collection($projects),
                'tickets' => TicketResource::collection($tickets)
            ], 200);
        }
    }

    public function chart(Request $request) {
        $filter = request()->year;        
        $data = [];

        for ($i = 1; $i <= 12; $i++) { 
            $openTickets = Ticket::whereYear('created_at', $filter)
                ->whereMonth('created_at', $i)
                ->where('status', 'open')->get();

            $onProgressTickets = Ticket::whereYear('created_at', $filter)
                ->whereMonth('created_at', $i)
                ->where('status', 'onProgress')->get();

            $closedTickets = Ticket::whereYear('created_at', $filter)
                ->whereMonth('created_at', $i)
                ->where('status', 'closed')->get();
            
            $i < 10 ? $i = '0'.$i : $i;

            $data[] = [
                'date' => $filter . '-' . $i,
                'open' => $openTickets ? $openTickets->count() : 0,
                'onProgress' => $onProgressTickets ? $onProgressTickets->count() : 0,
                'closed' => $closedTickets ? $closedTickets->count() : 0
            ];
        }

        return $data;
    }
}
