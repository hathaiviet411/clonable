<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tests\TestCase;
use Faker\Factory as Faker;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserManagementFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    private $crew;
    private $manager;
    private $url = 'api/system';

    public function setUp(): void
    {
        $this->faker = Faker::create();
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function actingAs($user, $driver = "user")
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', "Bearer {$token}");
        parent::actingAs($user, $driver);
        return $this;
    }


    // public function testUserCreateValidationFullFieldBlank()
    // {
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->postJson('api/user', []);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testUserCreateValidationMoreField()
    // {
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->postJson('api/user', ['role' => "", 'name' => 12, 'password' => 1, 'password_confirmation' => ""]);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testUserCreateValidationOneField()
    // {
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->postJson('api/user', ['role' => 1, "name" => "name test", "id" => 1, "password" => "", "password_confirmation" => ""]);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testUpdateCreateValidationFullFieldBlank()
    // {
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->putJson('api/user/' . $user->id, []);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testUpdateCreateValidationMoreField()
    // {
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->putJson('api/user/' . $user->id, ['role' => "", 'name' => 12, 'password' => 1, 'password_confirmation' => ""]);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testUpdateCreateValidationOneField()
    // {
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->putJson('api/user/' . $user->id, ['role' =>"", "name" => "name test"]);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testValidationNotInListRole()
    // {
    //     $role = Str::random(6);
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->postJson('api/user', ['role' => $role, "name" => "name test", "id" => 1, "password" => "123456789", "password_confirmation" => ""]);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testValidationMaxName()
    // {
    //     $name = Str::random(256);
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->postJson('api/user', ['role' => 1, "name" => $name, "id" => 1, "password" => "123456789", "password_confirmation" => ""]);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testValidationUserIdDuplicate()
    // {
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->postJson('api/user', ['role' => 1, "name" => "name", "id" => $user->id, "password" => "123456789", "password_confirmation" => ""]);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }

    // public function testValidationMinPassword()
    // {
    //     $pass = Str::random(6);
    //     $user = User::factory()->create();
    //     $response = $this->actingAs($user)->postJson('api/user', ['role' => 1, "name" => "name", "id" => $user->id, "password" => $pass, "password_confirmation" => $pass]);
    //     $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getData()->code);
    // }
}
