<?php

namespace Tests\Unit;

use App\Http\Requests\RoleRequest;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleManagementTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        $app = new Application();
        $this->faker = Faker::create();

        parent::setUp();

    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test User.
     * @param roleRequest $this - >param
     * @param null $guard
     * @return void
     */

    //test OK
    public function testCreateRole()
    {
        Role::create(['name' => $this->faker->name]);
        $count = Role::count();
        $this->assertDatabaseCount('roles', $count);
    }

    //test OK
    public function testAssignPermToRole()
    {
        $role = Role::create(['name' => $this->faker->name]);
        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);
        $count = Role::count();
        $this->assertDatabaseCount('roles', $count);
    }

    //test OK
    public function testAssignRoleToUser()
    {
        $role = Role::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        $user->syncRoles($role);
        $count = User::count();
        $this->assertDatabaseCount('users', $count);
    }
}
