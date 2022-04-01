<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\UserController;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Repositories\UserRepository;
use Mockery as m;
use Illuminate\Foundation\Testing\WithFaker;

class UserManagementTest extends TestCase
{
    protected $user;
    protected $userRepository;
    /**
     * @var UserController
     */
    protected $userController;
    protected $repository;
    protected $userRequest;
    // test create
    protected $user_not_create_role_null;
    protected $user_not_create_name_null;
    protected $user_not_create_id_null;
    protected $user_only_create_password;
    //test update
    protected $user_not_update_name_null;
    protected $user_not_update_role_null;
    protected $user_not_update_id_null;
    protected $user_only_update_password;


    use WithFaker;

    public function setUp(): void
    {
        $app = new Application();
        $userRepository = new UserRepository($app);
        $this->afterApplicationCreated(function () use ($userRepository) {
            $this->userRepository = m::mock($userRepository)->makePartial();
            $this->userController = new UserController(
                $this->app->instance(UserRepositoryInterface::class, $this->userRepository)
            );
        });
        $this->userRequest = new UserRequest();
        $this->faker = Faker::create();
        // chuẩn bị dữ liệu test
        // $this->user = [
        //     'role' => '1',
        //     "id" => rand(111, 99999),
        //     'name' => 'nguyen',
        //     "current_password" => "123456789",
        //     "password" => "123456789",
        //     "confirm_password" => "123456789"
        // ];

        // $this->user_not_update_name_null = [
        //     'role' => '1',
        //     "id" => rand(111, 99999),
        //     'name' => '',
        // ];
        // $this->user_not_update_role_null = [
        //     'role' => '',
        //     "id" => rand(111, 99999),
        //     'name' => $this->faker->name,
        // ];
        // $this->user_not_update_id_null = [
        //     'role' => '1',
        //     "id" => "",
        //     'name' => $this->faker->name,
        // ];
        // $this->user_only_update_password = [
        //     "current_password" => "",
        //     "password" => "12345678",
        //     "confirm_password" => "12345678"
        // ];

        // $this->user_not_create_role_null = [
        //     'role' => null,
        //     'name' => $this->faker->name,
        //     "id" => rand(111, 99999),
        //     "password" => "123456789",
        //     "confirm_password" => "123456789"

        // ];
        // $this->user_not_create_name_null = [
        //     'role' => '1',
        //     'name' => '',
        //     "id" => rand(111, 99999),
        //     "password" => "123456789",
        //     "confirm_password" => "123456789"
        // ];

        // $this->user_not_create_id_null = [
        //     'role' => '1',
        //     'name' => $this->faker->name,
        //     "id" => null,
        //     "password" => "123456789",
        //     "confirm_password" => "123456789"
        // ];

        // $this->user_only_create_password = [
        //     'role' => '1',
        //     'name' => $this->faker->name,
        //     "id" => rand(111, 99999),
        //     "password" => null,
        //     "confirm_password" => null,
        // ];
        parent::setUp();

    }

    public function tearDown(): void
    {
        parent::tearDown();
    }


    /**
     * test User.
     * @param UserRequest $this - >param
     * @param null $guard
     * @return void
     */

    //test OK
    // public function testUserCreateSuccess()
    // {
    //     $this->userRequest->merge($this->user);
    //     $response = $this->userController->store($this->userRequest);
    //     $this->assertEquals(200, $response->getStatusCode());
    // }

    // public function testUserUpdateSuccess()
    // {
    //     // Gọi hàm tạo
    //     $this->userRequest->merge($this->user);
    //     $user = User::factory()->create();
    //     $response = $this->userController->update($this->userRequest, $user->id);
    //     $this->assertEquals(200, $response->getStatusCode());
    // }

    // //  Test OK
    // public function testUserShowID()
    // {
    //     // Gọi hàm tạo
    //     $this->userRequest->merge($this->user);
    //     $user = User::factory()->create();
    //     $response = $this->userController->show($user->id);
    //     $this->assertEquals(200, $response->getStatusCode());
    // }

    ////    Test OK
    public function testUserShowAll()
    {
        if (User::first()) {
            $user = User::factory()->create();
            $role = Role::inRandomOrder()->first();
            $user->syncRoles($role);
        }
        $this->userRequest = new UserRequest();
        $response = $this->userController->index($this->userRequest);
        $this->assertEquals(200, $response->getStatusCode());
    }

    ////    Test OK
    public function testUserSearch()
    {

        $user = User::factory()->create();
        $this->userRequest->merge($user->toArray());
        $role = Role::inRandomOrder()->first();
        $user->syncRoles($role);

        $this->userRequest = new UserRequest();

        $response = $this->userController->index($this->userRequest);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateUser() {
        $user = [
            'user_code' => rand(3,10000),
            'department_id' => 1,
            'user_name' => $this->faker->name,
            'password' => '123@123a',
            'created_by' => User::first()->id,
            'updated_by' => User::first()->id,
            'roles' => $role = Role::first()->id
        ];

        $this->userRequest->merge($user);
        $response = $this->userController->store($this->userRequest);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateUser() {
        $userNewRole = ROLE_OPERATOR;
        $roleId = Role::findByName($userNewRole)->id;
        $user = $this->userRepository->first();
        $userNewData = [
            'user_code' => $user->user_code,
            'department_id' => $user->department_id,
            'user_name' => $this->faker->name,
            'password' => '123@123a',
            'created_by' => User::first()->id,
            'updated_by' => User::first()->id,
            'roles' => $roleId
        ];
        $this->userRequest->merge($userNewData);
        $response = $this->userController->update($this->userRequest, $user->id);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteUser() {
        $user = $this->userRepository->first();
        $deleteId = $user->id;
        $response = $this->userController->destroy($user->id);
        $checkIfExits = User::where('id',$deleteId)->first();
        $this->assertNotInstanceOf(User::class, $checkIfExits);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
