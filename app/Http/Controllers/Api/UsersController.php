<?php

namespace App\Http\Controllers\Api;

use Validator;
use Carbon\Carbon;

use App\Services\JsonResponse;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangeDetailsRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Notifications\VerifyNotification;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{    
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {        
        return response()->json(User::all());
    }          

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $companies = Company::where('name', 'Birutekno Inc.')->first();

        $request->validate([
            'email' => 'required|string|email',
            'fullname' => 'required|string|min:4',
            'username' => 'required|string|min:4|max:20|unique:users,username',
            'handphone' => 'required|max:20',
            'alamat' => 'required|string',
            'password' => 'required|min:8'
        ]);

        $user = User::create([
            'email' => $request->email,
            'fullname' => $request->fullname,
            'username' => $request->username,
            'handphone' => $request->handphone,
            'alamat' => $request->alamat,
            'company_id' => $companies->id,
            'position_id' => $request->position_id,
            'password' => Hash::make($request->password),
        ]);

        $role = Role::findByName($request->roles);
        $user->syncRoles($role);

        $user->notify(new VerifyNotification($request->username, $request->password));

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $user->company;
        $user->position;
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => 'required|string|email',
            'fullname' => 'required|string|min:4',            
            'handphone' => 'required|max:20',
            'alamat' => 'required|string',
        ]);

        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }
        // if ($user->hasRole('admin')) {
        //     return response()->json(['error' => 'Admin can not be modified'], 403);
        // }

        $currentUser = Auth::user();
        if (!$currentUser->hasRole('admin')
            && $currentUser->id !== $user->id
            && !$currentUser->hasPermission(\App\Laravue\Acl::PERMISSION_USER_MANAGE)
        ) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $email = $request->get('email');
        $found = User::where('email', $email)->first();
        if ($found && $found->id !== $user->id) {
            return response()->json(['error' => 'Email has been taken'], 403);
        }

        $user->email = $email;
        $user->fullname = $request->fullname;
        $user->handphone = $request->handphone;
        $user->alamat = $request->alamat;
        $user->company_id = $request->company_id;
        $user->position_id = $request->position_id;
        
        $role = Role::findByName($request->roles);
        $user->syncRoles($role);
        $user->save();

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User    $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function updatePermissions(Request $request, User $user)
    {
        if ($user === null) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // if ($user->hasRole('admin')) {
        //     return response()->json(['error' => 'Admin can not be modified'], 403);
        // }

        $permissionIds = $request->get('permissions', []);
        $rolePermissionIds = array_map(
            function($permission) {
                return $permission['id'];
            },

            $user->getPermissionsViaRoles()->toArray()
        );        

        $newPermissionIds = array_diff($permissionIds, $rolePermissionIds);
        $permissions = Permission::allowed()->whereIn('id', $newPermissionIds)->get();
        $user->syncPermissions($permissions);

        $role = Role::findByName($request->roles);
        $user->syncRoles($role);

        return new UserResource($user);
    }    

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('admin')) {
            return response()->json(['error' => 'Ehhh! Can not delete admin user'], 403);
        }

        try {
            $user->delete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json([
            'message' => 'Delete Success'
        ], 204);
    }

    /**
     * Get permissions from role
     *
     * @param User $user
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function permissions(User $user)
    {
        try {
            return new JsonResponse([
                'user' => PermissionResource::collection($user->getDirectPermissions()),
                'role' => PermissionResource::collection($user->getPermissionsViaRoles()),
            ]);
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }
    }

    public function me(Request $request) {
        $user = $request->user();
        $user->company;
        $user->leadProjects;
        return response()->json(new UserResource($user));
    }

    public function changePassword(ChangePasswordRequest $request) {
        $user = User::findOrFail(Auth::user()->id);

        if(!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'message' => 'New password cannot be the same with old password'
            ]);
        }

        $user->password = Hash::make($request->newPassword);

        if(!$user->save()) {
            return response()->json([
                'message' => 'Opps something went wrong'
            ], 500);
        }

        return response()->json([
            'message' => 'Change password successfully'
        ]);
    }

    public function changeDetails(ChangeDetailsRequest $request) {
        $user = User::findOrFail(Auth::user()->id);
        $user->fullname = $request->fullname;
        $user->handphone = $request->handphone;
        $user->save();
        
        return response()->json([
            'message' => 'Update profile succesfully'
        ]);
    }   

    /**
     * @param bool $isNew
     * @return array
     */
    private function getValidationRules($isNew = true)
    {
        return [
            'fullname' => 'required',
            'email' => $isNew ? 'required|email|unique:users' : 'required|email',         
            'username' => 'required|unique:users,username',
        ];
    }
}
