<?php

namespace Tests\Unit;

use App\Models\PlateHistory;
use App\Models\Vehicle;
use Error;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use Repository\AccessoriesRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Repository\MaintenanceScheduleRepository;
use Repository\VehicleRepository;

class MaintanencScheduleTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    // use DatabaseMigrations;
    // use RefreshDatabase;

    private $scheduleRepository;
    private $vehicleRepository;
    public function setUp(): void
    {
        $app = new Application();
        $this->scheduleRepository = new MaintenanceScheduleRepository($app);
        $this->vehicleRepository = new VehicleRepository($app);
        $this->faker = Faker::create();
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testScheduleWorks() {
        Vehicle::factory()->has(PlateHistory::factory(), 'plate_history')->count(10)->create();
        $data = $this->scheduleRepository->finalSchedule(2022, 1);
        $this->assertIsArray($data);
    }

    public function testScheduleFindOneVehicle() {
        $vehicles = Vehicle::factory()->has(PlateHistory::factory(), 'plate_history')->create();
        $data = $this->scheduleRepository->finalSchedule(2022, 1, $vehicles->plate_history[0]->no_number_plate);
        $countScheduleShouldHave = 0;
        $numberOfSheduleShouleHave = 4;
        foreach ($data as $key => $month) {
            foreach ($month as $key => $vehicleSchedule) {
                if ($vehicleSchedule['no_number_plate'] == $vehicles->plate_history[0]->no_number_plate) {
                    $countScheduleShouldHave += 1; // each vehicle should have 4 maintanence times in year.
                    // 3 months for 1
                }
            }
        }
        $this->assertEquals($numberOfSheduleShouleHave, $countScheduleShouldHave);
    }

    public function testLogicOffScheduleIsCorrect() {
        $vehicles = $this->vehicleRepository->vehiclesDatas(2022, 1);
        $data = $this->scheduleRepository->finalSchedule(2022, 1);
        foreach ($vehicles as $key => $vehicle) {
            $countScheduleShouldHave = 0;
            $numberOfSheduleShouleHave = 4;
            foreach ($data as $key => $month) {
                foreach ($month as $key => $vehicleSchedule) {
                    if ($vehicleSchedule['no_number_plate'] == $vehicle->plate_history[0]->no_number_plate) {
                        if ($vehicleSchedule['vehicle_id'] == $vehicle->id)
                        $countScheduleShouldHave += 1; // each vehicle should have 4 maintanence times in year.
                        // 3 months for 1
                    }
                }
            }
            $this->assertEquals($numberOfSheduleShouleHave, $countScheduleShouldHave);
        }
    }
}
