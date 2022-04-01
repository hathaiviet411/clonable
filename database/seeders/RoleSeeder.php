<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Role::first()) {
            Role::insert([
                ['name' => ROLE_HEADQUARTER, 'guard_name' => 'api'],
                ['name' => ROLE_OPERATOR, 'guard_name' => 'api'],
                ['name' => ROLE_TEAM, 'guard_name' => 'api'],
            ]);
        }
    }
}
