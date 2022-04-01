<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use App\Models\MaintenanceLease;
use App\Models\PlateHistory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Vehicle;
class LeaseImport implements ToModel, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    use RemembersRowNumber;

    public static $invalidateArr = [];
    public function __construct($month)
    {
        $this->month = $month;
    }

    public function collection(Collection $collection)
    {
        //
    }

    public function model(array $row)
    {
        $rowIndex = $this->getRowNumber();
        if ($rowIndex > 1) {
            $row[2] = preg_replace('/　/', '', $row[2]);
            $row[2] = preg_replace('/ /', '', $row[2]);
            $invalid = $this->validate($row, $rowIndex);
            if (count($invalid) > 0) {
                LeaseImport::$invalidateArr[] = $invalid;
                return;
            }
            if ($this->checkPlate($row[2])) {
                $lease = MaintenanceLease::updateOrCreate(
                    [
                        'no_number_plate' => $row[2],
                        'vehicle_id' => $this->checkPlate($row[2])
                    ],
                    [
                        'department_id' => Vehicle::checkDepartmentExist($row[1]), // need to update in the future, no sample data
                        'start_of_leasing' => $row[3],
                        'end_of_leasing' => $row[4],
                        'leasing_period' => $row[5],
                        'leasing_company' => $row[6],
                        'garage' => $row[7],
                        'tel' => $row[8] // need to update in the future, no sample data
                    ]
                );
            }
        }
    }

    private function validate($row, $rowIndex) {
        $invalidArray = [];
        if (!isset($row[2]) || $row[2] == '') {
            $invalidArray['other'][] = "Lease: Number of plate can not be null at given row: {$rowIndex}";
        }
        else if (isset($row[2])) {

            $row[3] = str_replace("/", "-", $row[3]);
            if (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $row[3])) {
                $invalidArray[$row[2]][] = "Lease: Required start of leasing Y-m-d format at given row: {$rowIndex}";
            }
            $row[4] = str_replace("/", "-", $row[4]);
            if (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $row[4])) {
                $invalidArray[$row[2]][] = "Lease: Required end of leasing Y-m-d format at given row: {$rowIndex}";
            }

            if ($this->validInteger($row[5], 'inspection_expiration_date')) {
                $invalidArray[$row[2]][] = "Lease: leasing period ('{$row[5]}') must be type of integer at given row: {$rowIndex}";
            }
        }
        return $invalidArray;
    }

    private function checkPlate($numberOfPlate) {
        if ($vehicle = PlateHistory::where('no_number_plate', $numberOfPlate)->first()) {
            return $vehicle->vehicle_id;
        }
        return null;
    }

    private function validInteger($data, $key) {
        if (gettype($data) != 'integer') {
            return true;
        }
        return false;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
