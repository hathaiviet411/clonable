<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory as Faker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
class AccessoryFeatureTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->faker = Faker::create();
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
