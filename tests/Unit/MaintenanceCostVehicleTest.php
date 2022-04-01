<?php

namespace Tests\Unit;

use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use App\Models\Vehicle;
use App\Models\PlateHistory;
use Repository\MaintenanceCostRepository;
use Repository\ScheduleRepository;
use Repository\AccessoryScheduleRepository;
use App\Models\MaintenanceCost;
class MaintenanceCostVehicleTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
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

    public function testMaintenanceCostDetail() { // Maintenance cost (Detail)
        $id = 1;
        $detail = $this->maintenanceCost->MaintenanceCostDetail($id);
        $this->assertInstanceOf(MaintenanceCost::class, $detail);
    }

    public function testMaintenanceCostRegister() { // Maintenance cost  Registration
        $vehicle = Vehicle::factory()->has(PlateHistory::factory(), 'plate_history')->create();
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->request->add([
            "type"=> 4,
            "charge_type"=> 1,
            "maintained_date"=> "2022-08",
            "vehicle_id"=> 1,
            "maintenance_accessories"=> [
            ],
            "no_number_plate"=> $vehicle->plate_history[0]->no_number_plate,
            "mileage_current"=> 354400,
            "wage"=> [
                [
                "work_content"=> "Wage 1",
                "wage"=> 1
                ]
            ],
            "total_amount_excluding_tax"=> 106,
            "discount"=> 1,
            "total_amount_including_tax"=> 115.50000000000001,
            "note"=> "Note 1111"
        ]);
        $cost = $this->maintenanceCost->maintenanceCost($request, false);
        $this->assertEquals($request->maintained_date . "-01", $cost['maintained_date']); // create a cost has maintained_date
    }

    public function testMaintenanceCostEdit() { // Maintenance cost  EDIT
        $vehicle = Vehicle::find(1);
        $mtn = MaintenanceCost::factory()->state(function (array $attributes) use ($vehicle) {
            return array_merge($attributes, [
                'vehicle_id' => $vehicle->id,
                'status' => 2,
                'type' => 3
            ]);
        })->create();
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->request->add([
            "id" => $mtn->id,
            "type"=> $mtn->type,
            "charge_type"=> 1,
            "maintained_date"=> "2022-08-27",
            "vehicle_id"=> 1,
            "maintenance_accessories"=> [
            ],
            "no_number_plate"=> $vehicle->plate_history[0]->no_number_plate,
            "mileage_current"=> 354400,
            "wage"=> [
                [
                "work_content"=> "Wage 1",
                "wage"=> 1
                ]
            ],
            "total_amount_excluding_tax"=> 106,
            "discount"=> 1,
            "total_amount_including_tax"=> 115.50000000000001,
            "note"=> "Note 1111"
        ]);
        $cost = $this->maintenanceCost->maintenanceCost($request, false);
        $this->assertEquals($mtn->id, $cost['id']); // true id
    }

    public function testMaintenanceCostVehicleDetail() { // Maintenance cost_Vehicle (Detail)
        $vehicle_id = 1;
        $year = 2021;
        $vehicle = Vehicle::find($vehicle_id);
        $vehicle_data = $this->scheduleRepository->vehicleCostDetail($vehicle->id);
        $schduleAccessoriesData = $this->scheduleAccessoriesRepository->scheduleVehicleAccessories($year, $vehicle->id, $vehicle->department_id);
        $this->assertEquals($vehicle->id, $vehicle_data->id);
        $yearResponse = date('Y', strtotime($schduleAccessoriesData[0][0]['month']));
        $this->assertEquals($year, $yearResponse);
    }

    public function testMaintenanceCostVehicleEdit() { // Maintenance cost_Vehicle (Edit)
        $vehicle_id = 1;
        $vehicle = Vehicle::find($vehicle_id);
        $request = new \Illuminate\Http\Request();
        $request->setMethod('PUT');
        $request->request->add([
            'vehicle_id' => $vehicle->id,
            'glass' => "glass",
            'glass_date' => "2021-01-01",
            'body_id' => "body",
            'body_id_date' => "body",
            'camera_monitor' => "camera",
            'camera_monitor_date' => "2021-01-01",
            'gate' => "gate",
            'gate_date' => "2021-01-01",
            'other' => "other",
            'other_date' => "2021-01-01",
            'remark_01' => "remark",
            'remark_02' => "remark",
            'created_by' => 1,
            'updated_by' => 1
        ]);
        $response = $this->scheduleRepository->vechileScheduleCostEdit($vehicle->id, $request);
        $this->assertEquals($vehicle->id, $response[0]->vehicle_id);
    }
}
