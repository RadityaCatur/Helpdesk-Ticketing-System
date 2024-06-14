<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    public function admin(Request $request) {    
        $users = User::role('admin')
            ->with('roles')
            ->orderBy('id', 'DESC')
            ->when($request->q, function($users) {
               $users = $users->where('fullname', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%admin%');
                    })
                    ->orWhere('email', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%admin%');
                    })
                    ->orWhere('username', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%admin%');
                    })
                    ->orWhere('handphone', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%admin%');
                    })->get();
            })->get();

        return response()->json([
            'data' => $users
        ]);            
    }

    public function employee(Request $request) {    
        $users = User::role('employee')
            ->with('roles')
            ->orderBy('id', 'DESC')
            ->when($request->q, function($users) {
               $users = $users->where('fullname', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%employee%');
                    })
                    ->orWhere('email', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%employee%');
                    })
                    ->orWhere('username', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%employee%');
                    })
                    ->orWhere('handphone', 'ILIKE', '%' . request()->q . '%')
                    ->whereHas('roles', function(Builder $query) {
                        return $query->where('name', 'LIKE', '%employee%');
                    })->get();
            })->get();

        return response()->json([
            'data' => $users
        ]);            
    }
}
