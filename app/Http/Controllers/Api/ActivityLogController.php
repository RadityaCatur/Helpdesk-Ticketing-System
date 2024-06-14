<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use DB;

class ActivityLogController extends Controller
{
    public function index(Request $request) {
        $filter = request()->year . '-' . request()->month . '-' . request()->day;
        $parse = Carbon::parse($filter);
        $array_date = range($parse->startOfMonth()->format('d'), $parse->endOfMonth()->format('d'));
        $activities = Activity::orderBy('created_at', 'DESC')->where('created_at', 'LIKE', '%' . $filter . '%')->get();
        
        return response()->json($activities);            
    }
}
