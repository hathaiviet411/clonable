<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Permission::first()) {
            $permissions = [
                PERMISSION_USER_LIST,
                PERMISSION_USER_CREATE,
                PERMISSION_USER_EDIT,
                PERMISSION_USER_DELETE,

                PERMISSION_ACCESSORIES_LIST,
                PERMISSION_ACCESSORIES_CREATE,
                PERMISSION_ACCESSORIES_EDIT,
                PERMISSION_ACCESSORIES_DELETE,

                PERMISSION_MAINTENANCE_SCHEDULE_AND_RESULTS_LIST,
                PERMISSION_MAINTENANCE_SCHEDULE_AND_RESULTS_EXPORT,

                PERMISSION_MAINTENANCE_SCHEDULE_LIST,
                PERMISSION_MAINTENANCE_SCHEDULE_CREATE,
                PERMISSION_MAINTENANCE_SCHEDULE_EDIT,
                PERMISSION_MAINTENANCE_SCHEDULE_DELETE,
                PERMISSION_MAINTENANCE_SCHEDULE_DETAIL,

                PERMISSION_VEHICLE_LIST,
                PERMISSION_VEHICLE_CREATE,
                PERMISSION_VEHICLE_EDIT,
                PERMISSION_VEHICLE_DELETE,
                PERMISSION_VEHICLE_DETAIL,

                PERMISSION_MAINTENANCE_LIST,
                PERMISSION_MAINTENANCE_CREATE,
                PERMISSION_MAINTENANCE_EDIT,
                PERMISSION_MAINTENANCE_DELETE,
                PERMISSION_MAINTENANCE_DETAIL,

                PERMISSION_MAINTENANCE_RESULT_LIST,
                PERMISSION_MAINTENANCE_RESULT_EDIT,
                PERMISSION_MAINTENANCE_RESULT_DELETE,
                PERMISSION_MAINTENANCE_RESULT_DETAIL,
            ];

            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission, 'guard_name' => 'api']);
            }
        }
    }
}
