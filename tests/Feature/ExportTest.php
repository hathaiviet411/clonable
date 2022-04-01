<?php

namespace Tests\Feature;

use App\Models\MaintenanceCost;
use App\Models\Vehicle;
use Carbon\Carbon;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class ExportTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    private $path = "export";

    public function setUp(): void
    {
        $this->faker = Faker::create();
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testExportMaintenanceCostExportNoFolderToStorage(){
        if(Storage::exists($this->path)) {
            Storage::deleteDirectory($this->path);
        }
        $response = $this->get('/api/maintenance-cost/export/' . $this->faker->date('Y-m-d'));
        $response->assertStatus(200);
    }

    public function testExportMaintenanceCostRandomDate() {
        $response = $this->get('/api/maintenance-cost/export/' . $this->faker->date('Y-m-d'));
        $response->assertStatus(200);
    }

    public function testExportMaintenanceCostExportTypePriodicInspection() {
        $changed = MaintenanceCost::where('type', TYPE_THREE_MONTH)->first();
        $changed->maintained_date = Carbon::now()->format('Y-m-d');
        $next = date('Y-m-d', strtotime($changed->maintained_date . "+1 day"));
        $changed->save();
        $response = $this->get('/api/maintenance-cost/export/' . $next);
        $response->assertStatus(200);
    }

    public function testExportMaintenanceCostExportTypeAccessory() {
        $changed = MaintenanceCost::where('type', TYPE_ACCESSORY_CHANGE)->first();
        $changed->maintained_date = Carbon::now()->format('Y-m-d');
        $next = date('Y-m-d', strtotime(Carbon::now()->format('Y-m-d') . "+1 day"));
        $changed->save();
        $response = $this->get('/api/maintenance-cost/export/' . $next);
        $response->assertStatus(200);
    }

    public function testExportMaintenanceCostExportTypeOther() {
        $changed = MaintenanceCost::where('type', TYPE_OTHER)->first();
        $changed->maintained_date = Carbon::now()->format('Y-m-d');
        $next = date('Y-m-d', strtotime(Carbon::now()->format('Y-m-d') . "+1 day"));
        $changed->save();
        $response = $this->get('/api/maintenance-cost/export/' . $next);
        $response->assertStatus(200);
    }
}
