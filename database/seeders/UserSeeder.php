<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::first()) {
            $user = User::create([
                'user_code' => 111111,
                'password' => '123456789',
                'user_name' => 'Super Admin'
            ]);

            $role = Role::findByName(ROLE_HEADQUARTER, 'api');
            $user->syncRoles($role);

            $user = User::create([
                'user_code' => 666666,
                'password' => '123456789',
                'user_name' => 'User Team',
            ]);

            $role = Role::findByName(ROLE_TEAM, 'api');
            $user->syncRoles($role);
        }
    }
}
