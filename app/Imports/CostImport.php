<?php

namespace App\Imports;

use App\Models\Maintenance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use App\Models\MaintenanceDepreciation;
use App\Models\Vehicle;

class CostImport implements ToModel, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    use RemembersRowNumber;
    private $month;
    public function __construct($month)
    {
        $this->month = $month;
    }
    public function model(array $row)
    {
        $rowIndex = $this->getRowNumber();
        if ($rowIndex > 2) {
            if ($vehicleId = $this->checkPlate($row[1]))
            {
                $cost = MaintenanceDepreciation::create(
                    [
                        'vehicle_id' => $vehicleId,
                        'newest_at' => $this->month,
                        'vehicle_depreciation' => (double)$row[2],
                        'leasing_depreciation' => (double)$row[3],
                        'disaster_insurance_liability' => (double)$row[4],
                        'disaster_insurance_mandatory' => (double)$row[5],
                        'maintenance_lease' => (double)$row[6],
                        'communication_fee_driver_record' => (double)$row[7],
                        'rent_fee_driver_record' => (double)$row[8],
                        'total' =>  (double)$row[2] + (double)$row[3] +  (double)$row[4] +  (double)$row[5] +  (double)$row[6] +  (double)$row[7] +  (double)$row[8]
                    ]
                );
            }
        }
    }

    private function checkPlate($numberOfPlate) {
        if ($vehicle = Vehicle::where('no_number_plate', $numberOfPlate)->first()) {
            return $vehicle->id;
        }
        return null;
    }

    public function collection(Collection $collection)
    {
        //
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
