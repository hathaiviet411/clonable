<?php

namespace Tests\Unit;

use App\Models\Accessory;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use Repository\AccessoriesRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
class AccessoriesTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $accessoryRepository;
    public function setUp(): void
    {
        $app = new Application();
        $this->accessoryRepository = new AccessoriesRepository($app);
        parent::setUp();
        $this->faker = Faker::create();
    }

    public function testGetPaginate() {
        $per_page = 10;
        $page = 1;
        Accessory::factory()->count(10)->create();
        $result = $this->accessoryRepository->getPaginate($per_page);
        $result = $result->toArray();
        $this->assertEquals($per_page, count($result['data']));
        $this->assertEquals($page, $result['current_page']);
    }

    public function testGetByName() {
        $per_page = 10;
        Accessory::factory()->count(10)->create();
        $data = $this->accessoryRepository->find(1);
        $name = $data->name;
        $result = $this->accessoryRepository->getPaginate($per_page, $name);
        $result = $result->toArray();
        $this->assertEquals($name, $result['data'][0]['name']);
    }

    public function testCreateAccessory() {
        $accessory = [
            'name' => "Accessory Create Test",
            'tonnage' => rand(1, 10),
            'passed_year' => rand(0, 10),
            'mileage' => rand(1, 10),
            'created_by' => 1,
            'updated_by' => 1
        ];
        $accessory = $this->accessoryRepository->create($accessory);
        $this->assertInstanceOf(Accessory::class, $accessory);
    }

    public function testUpdateAccessory() {
        $id = 1;
        Accessory::factory()->count(10)->create();
        $currentAccessory = Accessory::find($id);
        $accessoryUpdate = [
            'name' => "Accessory Updated Name",
            'tonnage' => $currentAccessory->tonnage,
            'passed_year' => $currentAccessory->passed_year,
            'mileage' => $currentAccessory->mileage,
            'created_by' => $currentAccessory->created_by,
            'updated_by' => $currentAccessory->updated_by
        ];
        $accessory = $this->accessoryRepository->update($accessoryUpdate, $id);
        $this->assertInstanceOf(Accessory::class, $accessory);
        $this->assertNotEquals($currentAccessory->name, $accessory->name);
        $this->assertEquals($accessoryUpdate['name'], $accessory->name);
    }

    public function testDeleteAccessory() {
        Accessory::factory()->count(10)->create();
        $id = 1;
        $delete = $this->accessoryRepository->delete($id);
        $this->assertTrue($delete);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
