<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Builder;
use Hash;
use App\Notifications\UpdateAccessNotification;
use App\Notifications\NewClientNotification;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{

    public function index(Request $request)  {
        $users = User::role('pic')
            ->with('roles', 'company')
            ->orderBy('id', 'DESC')
            ->when($request->q, function($users) {
                $users = $users->where('fullname', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%pic%');
                    })
                    ->orWhere('email', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%pic%');
                    })
                    ->orWhere('username', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%pic%');
                    })
                    ->orWhere('handphone', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%pic%');
                    });
            })->get();

        return response()->json([
            'data' => $users
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
        $validate =  Validator::make($request->all(), [
            'fullname' => 'required|string|min:4',
            'username' => 'required|string|min:4|max:20|unique:users,username',
            'email' => 'required|string|email|unique:users,email',
            'handphone' => 'required|max:20',
            'alamat' => 'required|string',
            'password' => 'required|min:8'
        ])->setAttributeNames([
            'email' => 'Email',
            'fullname' => 'Nama Lengkap',
            'username' => 'Username',
            'handphone' => 'Handphone',
            'alamat' => 'Alamat',
            'password' => 'Password'
        ]);

        if ($validate->fails()) {
            $after_save = [
                'icon' => 'check',
                'alert' => 'danger',
                'message' => strip_tags($validate->errors()->first()),
            ];
            return response()->json($after_save, 422);
        }

        $user = User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'handphone' => $request->handphone,
            'alamat' => $request->alamat,
            'company_id' => $request->company_id,            
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $role = Role::findByName($request->roles);
        $user->syncRoles($role);

        if($request->sendEmail) {
            $user->notify(new NewClientNotification($request->username, $request->password));
        }

        $data = new UserResource($user);
        return response(['data' => $data, 'message' => 'data berhasil disimpan', 'status' => true], Response::HTTP_CREATED);
        // return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Integer $id
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {   
        $request->validate([            
            'username'  => 'required|string|min:4|max:20',            
            'password'  => 'sometimes|min:8',            
            'status'    => 'required'
        ]);

        $user = User::find($id);
        if(!$user) {
            return response()->json([
                'message' => 'User Not Found'
            ], 404);
        }
        
        $user->username = $request->username;  
        
        if ($request->status == "1") {
            $user->active   = true;      
        } else {
            $user->active   = false;
        }
        
        if($request->password) {
            $user->password = Hash::make($request->password);
            $user->notify(new UpdateAccessNotification($request->username, $request->password));
        }

        $user->update();        

        return new UserResource($user);
    }
}
