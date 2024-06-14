<?php
/**
 * File RoleController.php
 *
 * @author Tuan Duong <bacduong@gmail.com>
 * @package Laravue
 * @version 1.0
 */
namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Controllers\Controller;

/**
 * Class RoleController
 *
 * @package App\Http\Controllers\Api
 */
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RoleResource::collection(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api'
        ]);

        return response()->json([
            'message' => 'Role has been created successfully',
            'data' => new RoleResource($role)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        if ($role === null || $role->isAdmin()) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        if($request->name) {
            $role->name = $request->name;
            $role->save();
            return new RoleResource($role);
        } else {
            $permissionIds = $request->get('permissions', []);
            $permissions = Permission::allowed()->whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);
            $role->save();
            return new RoleResource($role);
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
        $role = Role::where('id', $id)->first();
        if(!$role) {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }

        $role->delete();
        return response()->json([
            'message' => 'Role has been deleted successfully'
        ]);
    }

    /**
     * Get permissions from role
     *
     * @param  Role $role
     * @return \Illuminate\Http\Response
     */
    public function permissions(Role $role)
    {
        return PermissionResource::collection($role->permissions);
    }
}
