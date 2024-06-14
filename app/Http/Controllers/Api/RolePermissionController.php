<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class RolePermissionController extends Controller
{
    public function index() {
        $permissions = Permission::all()->map(function ($permission) {
            $permission->slugCamel = Str::camel($permission->slug);
            return $permission;
        })->pluck('slug', 'slugCamel');

        $roles = Role::all()->map(function ($role) {
            $role->slugCamel = Str::camel($role->slug);
            return $role;
        })->pluck('slug', 'slugCamel');

        return response()->json([
            "permissions" => $permissions,
            "roles" => $roles
        ]);
    }
}
