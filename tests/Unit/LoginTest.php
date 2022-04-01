<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Faker\Factory as Faker;
use Repository\AuthRepository;
use App\Http\Requests\LoginRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
class LoginTest extends TestCase
{
    protected $request;
    protected $authRepository;
    protected $loginRequest;
    protected $user;
    protected $param;

    public function setUp(): void
    {
        $this->faker = Faker::create();
        parent::setUp();
        $this->artisan('db:seed');
        $this->request = new LoginRequest();
        $this->authRepository = new AuthRepository();
        $this->loginRequest = new LoginRequest();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testLogin()
    {
        $param = [
            'user_code' => '111111',
            'password' => '123456789',
        ];
        $this->request->merge($param);
        $response = $this->authRepository->doLogin($this->request, $guard = null);
        $this->assertArrayHasKey('attempt', $response);
        $this->assertEquals($param['user_code'], $response['user']->user_code);
    }

    public function testLoginWrongIdOrPass()
    {
        $param = [
            'id' => '122112',
            'password' => '1234567899',
        ];
        $this->request->merge($param);
        $response = $this->authRepository->doLogin($this->request, $guard = null);
        $this->assertEquals(false, $response['attempt'],
            'server.emp_code_or_password_incorrect');
    }

    public function testLoginNotHavePassword()
    {
        $param = [
            'user_code' => '123456',
            'password' => '',
        ];
        $this->request->merge($param);
        $response = $this->authRepository->doLogin($this->request, $guard = null);
        $this->assertEquals(false, $response['attempt'],
            'The パスワード field is required.');
    }

    public function testLoginNotHaveParams()
    {
        $param = [
            'user_code' => '',
            'password' => '',
        ];
        $this->request->merge($param);
        $response = $this->authRepository->doLogin($this->request, $guard = null);
        $this->assertEquals(false, $response['attempt'],
            'The emp code field is required.');
    }

    public function testLoginWrongTypeId()
    {
        $param = [
            'user_code' => '1234@12',
            'password' => '',
        ];
        $this->request->merge($param);
        $response = $this->authRepository->doLogin($this->request, $guard = null);
        $this->assertEquals(false, $response['attempt'],
            'The emp code may only contain letters, numbers, dashes and underscores.');
    }

    public function testLoginNotHaveId()
    {
        $param = [
            'user_code' => '',
            'password' => '123456789',
        ];
        $this->request->merge($param);
        $response = $this->authRepository->doLogin($this->request, $guard = null);
        $this->assertEquals(false, $response['attempt'],
            'The emp code field is required.');
    }


}
