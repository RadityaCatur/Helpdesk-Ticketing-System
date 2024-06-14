<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Pusher\Pusher;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{        
    
    public function login(LoginRequest $request) {        
        $creadentials = request(['username', 'password']);
        $creadentials['active'] = 1;
        $creadentials['deleted_at'] = null;

        if(!Auth::attempt($creadentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if($request->remember_me) {
            $token->expires_at = Carbon::now()->addMonths(2);
        }

        $token->save();        

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ], 200);            
    }

    public function checkAuth() {
        if(Auth::check()) {
            return response()->json([
                'message' => 'Authenticated',
                'status' => true,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unathenticated',
                'status' => false,
            ], 403);
        }
    }

    public function authBroadcast(Request $request) {
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
        
        // return response([
        //     'auth' => $pusher->socket_auth($request->channel_name, $request->socket_id),
        //     'channel_data' => Auth::user()
        // ]);
        return $pusher->presence_auth(
            $request->channel_name, 
            $request->socket_id, 
            Auth::user()->id, 
            Auth::user()
        );

        // return $pusher->socket_auth($request->channel_name, $request->socket_id);
    }

    public function register(RegisterRequest $request) {     
        return response()->json([
            'message' => 'Registration successed'
        ]);
    }

    public function logout() {
        $user = Auth::user()->token();
        $user->revoke();

        return response()->json([
            'message' => 'Logged Successfully'
        ]);
    }       

}
