<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class BroadcastMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $web = Auth::guard('web')->user();
        if($web) {
            return response()->json(
                \Illuminate\Support\Facades\Broadcast::auth($request)
            );
        }

        $api = Auth::guard('api')->user();
        if ($api) {
            return response()->json($api);
        }

        return response()->json($data, 200, $headers);
    }
}
