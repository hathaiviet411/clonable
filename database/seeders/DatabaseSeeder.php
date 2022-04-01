<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DepartmentSeeder::class);
        $this->call(SystemConfigSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(AssignPermissionsToRolesSeeder::class);
        $this->call(UserSeeder::class);
    }
}
