<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignPermissionsToRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::findByName(ROLE_HEADQUARTER, 'api');
        if ($role) {
            $permissions = Permission::pluck('id', 'id')->all();
            $role->syncPermissions($permissions);
        }

        $role = Role::findByName(ROLE_OPERATOR, 'api');
        if ($role) {
            $permNotIn = [
                PERMISSION_ACCESSORIES_LIST,
                PERMISSION_ACCESSORIES_CREATE,
                PERMISSION_ACCESSORIES_EDIT,
                PERMISSION_ACCESSORIES_DELETE,
                PERMISSION_USER_LIST,
                PERMISSION_USER_CREATE,
                PERMISSION_USER_EDIT,
                PERMISSION_USER_DELETE,
            ];
            $permissions = Permission::whereNotIn('name', $permNotIn)->pluck('id', 'id')->all();
            $role->syncPermissions($permissions);
        }

        $role = Role::findByName(ROLE_TEAM, 'api');
        if ($role) {
            $permNotIn = [
                PERMISSION_ACCESSORIES_LIST,
                PERMISSION_ACCESSORIES_CREATE,
                PERMISSION_ACCESSORIES_EDIT,
                PERMISSION_ACCESSORIES_DELETE,
                PERMISSION_USER_LIST,
                PERMISSION_USER_CREATE,
                PERMISSION_USER_EDIT,
                PERMISSION_USER_DELETE,
                PERMISSION_MAINTENANCE_RESULT_EDIT,
                PERMISSION_MAINTENANCE_RESULT_DELETE,
                PERMISSION_MAINTENANCE_DELETE,
                PERMISSION_MAINTENANCE_SCHEDULE_LIST,
            ];
            $permissions = Permission::whereNotIn('name', $permNotIn)->pluck('id', 'id')->all();
            $role->syncPermissions($permissions);
        }
    }
}
