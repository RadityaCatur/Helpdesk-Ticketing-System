<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Services\Acl;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Acl::roles() as $role) {
            Role::findOrCreate($role);
        }

        $adminRole = Role::findByName(Acl::ROLE_ADMIN);
        $employeeRole = Role::findByName(Acl::ROLE_EMPLOYEE);
        $clientRole = Role::findByName(Acl::ROLE_CLIENT);

        foreach (Acl::permissions() as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        // Setup basic permission
        $adminRole->givePermissionTo(Acl::permissions());
        $employeeRole->givePermissionTo(Acl::permissions([
            Acl::PERMISSION_PERMISSION_MANAGE, 
            Acl::PERMISSION_PROJECT_MANAGE
        ]));
        $clientRole->givePermissionTo(Acl::permissions([
            Acl::PERMISSION_ACTIVITIES_MANAGE, 
            Acl::PERMISSION_PERMISSION_MANAGE, 
            Acl::PERMISSION_CATEGORY_MANAGE
        ]));        

        $adminUser = User::where('username', 'admin')->first();        
        $adminUser->syncRoles($adminRole);

        $employeeUser = User::where('username', 'employee')->first();
        $employeeUser->syncRoles($employeeRole);

        $clientUser = User::where('username', 'pic')->first();
        $clientUser->syncRoles($clientRole);
    }
}
