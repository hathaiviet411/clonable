<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace Repository;

use App\Repositories\Contracts\CloudRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use App\Jobs\VehicleJob;
use App\Models\ConnectionLog;
use Symfony\Component\HttpFoundation\File\File;
class CloudRepository extends BaseRepository implements CloudRepositoryInterface
{

    // maintenance-lease-data.csv
    // vehicle-list.csv
    const zipFiles = [
        "vehicle" => "vehicle-list.csv",
        // "maintenance_cost" => "maintenance_cost.xlsx",
        "maintenance_lease" => "maintenance-lease-data.csv"
    ];
    private $path = "cloud";

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function model()
    {
        return Vehicle::class;
    }

    public function unZip($file, $log, $itemID, $now) {
        $result = [];
        $log->file_size = $file->getSize() / 1024;
        $log->save();
        $myZip = new ZipArchive();
        if ($myZip->open($file)) {
            foreach (self::zipFiles as $key => $fileName) {
                $fileContents = $myZip->getFromName($fileName);
                if ($fileContents) {
                    $path = $this->storageFile($fileName, $fileContents);
                    $result[$key] = $path;
                }
            }
        }
        if (count($result) == count(self::zipFiles)) {
            $vehicleJob = VehicleJob::dispatch($result, $log, $itemID, $now);
        }
        return $result;
    }

    private function storageFile($fileName, $fileContents) {
        if(!Storage::exists($this->path)) {
            Storage::makeDirectory($this->path);
        }
        $fileName = md5(Carbon::now()) . "_" . $fileName;
        $path = Storage::path($this->path . '/' . $fileName);
        // $status = Storage::disk($this->path)->put($fileName, $fileContents);
        $status = file_put_contents($path, $fileContents);
        if ($status != false) {
            return $fileName;
        }
        return false;
    }
}
