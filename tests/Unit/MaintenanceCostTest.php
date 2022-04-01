<?php

namespace Tests\Unit;

use App\Http\Requests\MaintenanceCostRequest;
use App\Models\MaintenanceCost;
use App\Models\Vehicle;
use Repository\MaintenanceCostRepository;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use Repository\AccessoryScheduleRepository;
use Repository\ScheduleRepository;
use App\Models\PlateHistory;
class MaintenanceCostTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $maintenanceCost;
    protected $scheduleRepository;
    protected $scheduleAccessoriesRepository;
    public function setUp(): void
    {
        $app = new Application();
        $this->maintenanceCost = new MaintenanceCostRepository($app);
        $this->scheduleRepository = new ScheduleRepository($app);
        $this->scheduleAccessoriesRepository = new AccessoryScheduleRepository($app);
        parent::setUp();
        $this->faker = Faker::create();
    }


    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetPaginate()
    {
        $per_page = 10;
        $page = 1;
        Vehicle::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();
        MaintenanceCost::factory()->count(10)->create();
        $result = $this->maintenanceCost->maintenanceCodePaginate(1, '2021-02', null, null, null, null, null, null, null, null, $per_page);
        $result = $result->toArray();
        $this->assertEquals($per_page, count($result['data']));
        $this->assertEquals($page, $result['current_page']);
    }

    public function testFilterByStatusAndType()
    {
        Vehicle::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();
        MaintenanceCost::factory()->count(10)->state(function (array $attributes) {
            return array_merge($attributes, [
                'status' => STATUS_INPUTTED,
                'type' => TYPE_THREE_MONTH,
            ]);
        })->create();
        $results = $this->maintenanceCost->maintenanceCodePaginate(1, '2021-02', null, null, TYPE_THREE_MONTH, STATUS_INPUTTED, null, null, null, null, null);

        foreach ($results as $result) {
            $this->assertEquals(STATUS_INPUTTED, $result->status);
            $this->assertEquals(TYPE_THREE_MONTH, $result->type);
        }
    }

    public function testFilterByPlate()
    {
        $ver = Vehicle::factory()->count(1)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();

        MaintenanceCost::factory()->count(2)->state(function (array $attributes) use ($ver) {
            return array_merge($attributes, [
                'vehicle_id' => $ver->first()->id
            ]);
        })->create();

        $vehicle_id = $ver->first()->id;
        $results = $this->maintenanceCost->maintenanceCodePaginate(1, '2021-02', $vehicle_id, null, null, null, null, null, null, null, null);
        foreach ($results as $result) {
            $this->assertEquals($vehicle_id, $result->vehicle_id);
        }
    }

    public function testEditMaintenanceCost() //  Maintenance cost (Edit)
    {
        $ver = Vehicle::factory()->count(1)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();

        $mtn = MaintenanceCost::factory()->count(2)->state(function (array $attributes) use ($ver) {
            return array_merge($attributes, [
                'vehicle_id' => $ver->first()->id
            ]);
        })->create();

        $request = new MaintenanceCostRequest();

        $request->merge(array(
            'charge_type' => '1',
            'total_amount_excluding_tax' => 50000,
            'discount' => 5000,
            'total_amount_including_tax' => 5000,
            'note' => 'Note content',
        ));
        $results = $this->maintenanceCost->updateMaintenanceCost($request, $mtn->first()->id);
        $this->assertEquals($results->id, $mtn->first()->id);
    }


    public function testEditMaintenanceCostWithAccessories() // Maintenance cost (Edit)
    {
        $ver = Vehicle::factory()->count(1)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();

        $mtn = MaintenanceCost::factory()->count(2)->state(function (array $attributes) use ($ver) {
            return array_merge($attributes, [
                'vehicle_id' => $ver->first()->id
            ]);
        })->create();

        $request = new MaintenanceCostRequest();

        $request->merge(array(
            'charge_type' => '1',
            'total_amount_excluding_tax' => 50000,
            'discount' => 5000,
            'total_amount_including_tax' => 5000,
            'note' => 'Note content',
            'accessories' =>
                array(
                    0 => array('id' => 1, 'accessory_id' => 1, 'quantity' => 1, 'price' => 10000,),
                    1 => array('id' => NULL, 'accessory_id' => 2, 'quantity' => 2, 'price' => 20000,)),
        ));
        $results = $this->maintenanceCost->updateMaintenanceCost($request, $mtn->first()->id);
        $this->assertEquals($results->id, $mtn->first()->id);
    }

    public function testEditMaintenanceCostWithWages() // Maintenance cost (Edit)
    {
        $ver = Vehicle::factory()->count(1)->state(function (array $attributes) {
            return array_merge($attributes, [
                'department_id' => 1,
                'first_registration' => "2012-02",
            ]);
        })->create();

        $mtn = MaintenanceCost::factory()->count(2)->state(function (array $attributes) use ($ver) {
            return array_merge($attributes, [
                'vehicle_id' => $ver->first()->id
            ]);
        })->create();

        $request = new MaintenanceCostRequest();

        $request->merge(array(
            'charge_type' => '1',
            'total_amount_excluding_tax' => 50000,
            'discount' => 5000,
            'total_amount_including_tax' => 5000,
            'note' => 'Note content',
            'wages' =>
                array (
                    0 => array ('id' => 1, 'work_content' => 'work content 1',),
                    1 => array ('id' => NULL, 'work_content' => 'work content 2',),),
        ));
        $results = $this->maintenanceCost->updateMaintenanceCost($request, $mtn->first()->id);
        $this->assertEquals($results->id, $mtn->first()->id);
    }
}
