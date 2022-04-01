<?php

namespace App\Imports;

use App\Jobs\CalculateScheduleJob;
use App\Models\Department;
use App\Models\PlateHistory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use App\Models\Vehicle;
use App\Models\MileageHistory;
use Carbon\Carbon;

class VehicleImport implements ToModel, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    use RemembersRowNumber;

    public static $arrayInvalid = [];

    public function __construct($month)
    {
        $this->month = date('Y-m-d', strtotime($month));
    }

    public function model(array $row)
    {
        $rowIndex = $this->getRowNumber();
        $dataFaild = [];
        if ($rowIndex > 3) {
            if ($row[4] != null) {
                if (count($this->validate($row, $rowIndex)) > 0) {
                    VehicleImport::$arrayInvalid[] =  $this->validate($row, $rowIndex);
                    return;
                }
                $vehicle = Vehicle::updateOrCreate(
                    [
                        'vehicle_identification_number' => $row[15]
                    ],
                    [
                        'department_id' => Vehicle::checkDepartmentExist($row[2]),
                        'driving_classification' => $row[4],
                        'tonnage' => $row[5],
                        'truck_classification' => $row[6],
                        'truck_classification_number' => (int)$row[6],
                        'truck_classification_2' => $row[7],
                        'manufactor' => $row[8],
                        'first_registration' => $row[9] . "-" .$row[10],
                        'box_distinction' => $row[11],
                        'inspection_expiration_date' => $row[12] . "-" . $row[13] . "-" . $row[14],
                        'vehicle_identification_number' => $row[15],
                        'owner' => $row[16],
                        'etc_certification_number' => $row[17],
                        'etc_number' => $row[18],
                        'fuel_card_number_1' => $row[19],
                        'fuel_card_number_2' => $row[20],
                        'driving_recorder' => $row[21],
                        'box_shape' => $row[25],
                        'mount' => $row[26],
                        'refrigerator' => $row[27],
                        'eva_type' => $row[28],
                        'gate' => ($row[29]) ? true : false,
                        'humidifier' => ($row[30]) ? true : false,
                        'type' => $row[31],
                        'motor' => $row[32],
                        'displacement' => (double)$row[33],
                        'length' => $row[34],
                        'width' => $row[35],
                        'height' => $row[36],
                        'maximum_loading_capacity' => (double)$row[37],
                        'vehicle_total_weight' => (double)$row[38],
                        'in_box_length' => $row[39],
                        'in_box_width' => $row[40],
                        'in_box_height' => $row[41],
                        'voluntary_insurance' => (int)$row[42],
                        'liability_insurance_period' => $row[43] . "-" . $row[44] . "-" . $row[45],
                        'insurance_company' => $row[46],
                        'agent' => $row[47],
                        'tire_size' => $row[49],
                        'battery_size' => $row[50],
                        'monthly_mileage' => (int)str_replace("," , "",$row[51]),
                        'remark_old_car_1' => $row[53],
                        'remark_old_car_2' => $row[54],
                        'remark_old_car_3' => $row[55],
                        'remark_old_car_4' => $row[56]
                    ]
                );
                if ($vehicle) {
                    $lastPlate = PlateHistory::where('vehicle_id', $vehicle->id)->orderBy('date', 'DESC')->first();
                    if (!isset($lastPlate) || $lastPlate->no_number_plate != $row[3]) {
                        PlateHistory::create([
                            'vehicle_id' => $vehicle->id,
                            'no_number_plate' =>  $row[3],
                            'date' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);
                    }

                    $mileageHistoryChk = MileageHistory::where('vehicle_id', $vehicle->id)->first();
                    if (!$mileageHistoryChk) {
                        MileageHistory::create(
                            [
                                'vehicle_id' => $vehicle->id,
                                'mileage' => (int)str_replace("," , "",$row[48]),
                                'date' => Carbon::now()->firstOfMonth()
                            ]
                        );
                    }
                    if (!$vehicle->wasChanged()) {
                        CalculateScheduleJob::dispatch($vehicle->id);
                    }
                }
            } else return;
        }
    }

    public function collection(Collection $collection)
    {

    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function validate($row, $rowIndex) {
        $invalidArray = [];
        $validDepartment = Vehicle::checkDepartmentExist($row[2]);
        if (!isset($row[3])) {
            $invalidArray['other'][] = "Number of plate can not be null at given row: {$rowIndex}";
        }

        if (isset($row[3])) {
            if (!isset($row[15])) {
                $invalidArray['other'][] = "vehicle identification number can not be null at given row: {$rowIndex}";
            }

            if ($validDepartment == 0) {
                $invalidArray[$row[3]][] = "Department {'$row[2]'} missmatch in list department at given row: {$rowIndex}";
            }

            if ($this->validInteger($row[9], 'first registration')) {
                $invalidArray[$row[3]][] = "first registration(YEAR) ('{$row[9]}') must be type of integer at given row: {$rowIndex}";
                // $invalidArray['first_registration'][] = $this->validInteger($row[9], 'first registration(year)');
            }

            if ($this->validInteger($row[10], 'first registration')) {
                $invalidArray[$row[3]][] = "first registration(MONTH) ('{$row[10]}') must be type of integer at given row: {$rowIndex}";
            }

            if ($this->validInteger($row[12], 'inspection_expiration_date')) {
                $invalidArray[$row[3]][] = "inspection expiration date(YEAR) ('{$row[12]}') must be type of integer at given row: {$rowIndex}";
            }

            if ($this->validInteger($row[13], 'inspection_expiration_date')) {
                $invalidArray[$row[3]][] = "inspection expiration date(MONTH) ('{$row[13]}') must be type of integer at given row: {$rowIndex}";
            }

            if ($this->validInteger($row[14], 'inspection_expiration_date')) {
                $invalidArray[$row[3]][] = "inspection expiration date(DATE) ('{$row[14]}') must be type of integer at given row: {$rowIndex}";
            }

            $mileage = str_replace("," , "",$row[48]);
            if (!preg_match("/^[0-9]+$/", $mileage)) {
                $invalidArray[$row[3]][] = "Mileage ('{$mileage}') must be type of integer at given row: {$rowIndex}";
            }
        }

        return $invalidArray;
    } //'first_registration' => $row[9] . "-" .$row[10],

    private function validInteger($data, $key) {
        if (gettype($data) != 'integer') {
            return true;
        }
        return false;
    }

    public function __destruct()
    {
        // VehicleImport::$arrayInvalid = null;
    }
}
