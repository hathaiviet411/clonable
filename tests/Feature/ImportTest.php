<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use App\Jobs\VehicleJob;
use App\Models\MaintenanceCost;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Storage;
class ImportTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    private $fileZip = "vehicle_data_csv";
    public function setUp(): void
    {
        $this->faker = Faker::create();
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testUploadZipWork() {
        Queue::fake();
        Storage::fake('cloud');
        $stub = __DIR__.'/stubs/' . $this->fileZip . ".zip";
        $file = new UploadedFile($stub, $this->fileZip, 'zip', null, true);
        $response = $this->post('/api/receive-vehicles-data', [
            'file' => $file,
            'item_id' => 1,
            'month' => '2021-01-01'
        ]);
        $response->assertStatus(200);
        Queue::assertPushed(VehicleJob::class);
    }

    public function testUploadFileIsNotZip() {
        Queue::fake();
        Storage::fake('cloud');
        $stub = __DIR__.'/stubs/' . $this->fileZip . ".zip";
        $file = new UploadedFile($stub, $this->fileZip, 'zip', null, true);
        $response = $this->post('/api/receive-vehicles-data', [
            'file' => UploadedFile::fake()->create('fake.png'),
            'item_id' => 1,
            'month' => '2021-01-01'
        ]);
        $response->assertStatus(422);
    }
}
