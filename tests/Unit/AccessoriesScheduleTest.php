<?php

namespace Tests\Unit;

use App\Http\Requests\AccessoryScheduleRequest;
use App\Models\Accessory;
use App\Models\MaintenanceCost;
use App\Models\Schedule;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Repository\AccessoryScheduleRepository;
use Repository\ScheduleRepository;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;

class AccessoriesScheduleTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $repository;
    protected $repositorySchedule;

    public function setUp(): void
    {
        $app = new Application();
        $this->repository = new AccessoryScheduleRepository($app);
        $this->repositorySchedule = new ScheduleRepository($app);
        parent::setUp();
        $this->faker = Faker::create();
    }


    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetAccessoriesSchedule()
    {
        $per_page = 10;
        $page = 1;
        Vehicle::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();
        MaintenanceCost::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'type' => TYPE_ACCESSORY_CHANGE,
                'vehicle_id' => 1,
            ]);
        })->create();
        Accessory::factory()->count(10)->create();
        Schedule::factory()->count(10)->create();


        $request = new AccessoryScheduleRequest();

        $request->merge(array(
            'year' => '2020',
            'department_id' => 1,
        ));

        $results = $this->repository->index($request);
        $yearCheck = 0;
        foreach ($results as $result) {
            $yearCheck = Carbon::parse($result[0]['month'])->year;
        }
        $this->assertEquals(2021, $yearCheck);
    }

    public function testFilterByVehicle()
    {
        Vehicle::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();
        MaintenanceCost::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'type' => TYPE_ACCESSORY_CHANGE,
                'vehicle_id' => 1,
            ]);
        })->create();
        Accessory::factory()->count(10)->create();
        Schedule::factory()->count(10)->create();


        $request = new AccessoryScheduleRequest();

        $request->merge(array(
            'year' => '2020',
            'department_id' => 1,
            'vehicle_id' => 1,
        ));


        $results = $this->repository->index($request);
        $yearCheck = 0;
        foreach ($results as $result) {
            $yearCheck = Carbon::parse($result[0]['month'])->year;
        }
        $this->assertEquals(2021, $yearCheck);
    }

    public function testMaintenanceCostDataDetail()
    {
        $vhc = Vehicle::factory()->count(1)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();
        MaintenanceCost::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'type' => TYPE_ACCESSORY_CHANGE,
                'vehicle_id' => 1,
            ]);
        })->create();
        Accessory::factory()->count(10)->create();
        Schedule::factory()->count(10)->create();

        $results = $this->repositorySchedule->vehicleCostDetail($vhc->first()->id);

        $this->assertEquals($vhc->first()->id, $results->id);
    }


    public function testMaintenanceCostDataEdit()
    {
        $vhc = Vehicle::factory()->count(1)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();
        MaintenanceCost::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'type' => TYPE_ACCESSORY_CHANGE,
                'vehicle_id' => 1,
            ]);
        })->create();
        Accessory::factory()->count(10)->create();
        Schedule::factory()->count(10)->create();

        $request = new Request();

        $request->merge(array(
            "glass" => "A",
            "glass_date" => "2021-01-01",
            "body_id" => "Body 222222",
            "body_id_date" => "2021-01-01",
            "camera_monitor" => null,
            "camera_monitor_date" => null,
            "gate" => "01",
            "gate_date" => "2021-01-01",
            "other" => "Other accessories",
            "other_date" => "2021-01-01",
            "remark_01" => "remark 01",
            "remark_02" => "remark 02"
        ));

        $results = $this->repositorySchedule->vechileScheduleCostEdit($vhc->first()->id, $request);
        $this->assertEquals($vhc->first()->id, $results[0]->vehicle_id);
    }
}
