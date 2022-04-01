<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VehicleImport;
use Illuminate\Support\Facades\Storage;
use App\Imports\LeaseImport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Throwable;
use App\Models\ConnectionLog;
use App\Models\Vehicle;

class VehicleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $pathFiles;
    private $log;
    private $itemId;
    private $now;
    public function __construct($pathFiles, $log, $itemId, $now)
    {
        $this->pathFiles = $pathFiles;
        $this->log = $log;
        $this->itemId = $itemId;
        $this->now = $now;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->importVehicle();
        $this->importLease();
        $this->log->contents = "success";
        $this->log->status = "success";
        $this->log->file_path = json_encode($this->pathFiles);
        $this->log->save();

        $invalidate = [
            "status" => false,
            "vehicle" => [],
            "lease" => []
        ];

        if (count(LeaseImport::$invalidateArr) > 0) {
            $invalidate['lease'] = LeaseImport::$invalidateArr;
            $invalidate['status'] = 0;
        }

        if (count(VehicleImport::$arrayInvalid) > 0)
        {
            $invalidate['vehicle'] = VehicleImport::$arrayInvalid;
            $invalidate['status'] = 0;
        }

        $this->postStatus($invalidate, $invalidate['status']);

        VehicleImport::$arrayInvalid = [];
        LeaseImport::$invalidateArr = [];
    }

    public function failed(Throwable $exception) {
        $this->log->contents = $exception;
        $this->log->status = "failed";
        $this->log->file_path = json_encode($this->pathFiles);
        $this->log->save();
        $this->postStatus($exception);
    }

    private function importVehicle() {
        $this->setInputEncoding(Storage::path('cloud/'. $this->pathFiles['vehicle']));
        $import =  new VehicleImport($this->now);
        Excel::import($import, Storage::path('cloud/'. $this->pathFiles['vehicle']));
        Storage::delete('cloud/'. $this->pathFiles['vehicle']);
    }

    // private function importCost() {
    //     Excel::import(new CostImport($this->now), Storage::path('cloud/'. $this->pathFiles['maintenance_cost']));
    //     Storage::delete('cloud/'. $this->pathFiles['maintenance_cost']);
    // } comment by task remove file cost at 2022-01-24.(2h implement)

    private function importLease() {
        $this->setInputEncoding(Storage::path('cloud/'. $this->pathFiles['maintenance_lease']));
        Excel::import(new LeaseImport($this->now), Storage::path('cloud/'. $this->pathFiles['maintenance_lease']));
        Storage::delete('cloud/'. $this->pathFiles['maintenance_lease']);
    }

    private function postStatus($content = null, $status = true, $api = "api/data_connection/update-status-connection") {
        $payLoad = [
            "item_id" => $this->itemId,
            "status" => $status,
            "content" => $content
        ];
        $connectToCloudLog = ConnectionLog::create([
            'from' => "Maintenance",
            'call_to_api' => null,
            'status' => "excluding",
            'file_size' => null,
            'file_path' => null,
            'contents' => json_encode([
                "payload" =>  $payLoad
            ])
        ]);
        $environment = App::environment();
        if ($environment) {
            $url = CLOUD_URL[$environment] . $api;
            $connectToCloudLog->call_to_api = $url;
            $response = Http::withoutVerifying()->post($url, $payLoad);
            $connectToCloudLog->contents = json_encode([
                "payload" => $payLoad,
                "response" => $response
            ]);
            $connectToCloudLog->status = "success";
        } else {
            $connectToCloudLog->status = "fail";
        }
        $connectToCloudLog->save();
    }

    private function setInputEncoding($file) {
        $fileContent = file_get_contents($file);
        $enc = mb_detect_encoding($fileContent, mb_list_encodings(), true);
        Config::set('excel.imports.csv.input_encoding', $enc);
    }
}
