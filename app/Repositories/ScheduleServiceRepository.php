<?php
/**
 * Created by VeHo.
 * Year: 2022-01-28
 */

namespace Repository;

use App\Models\Accessory;
use App\Models\MaintenanceAccessory;
use App\Models\MaintenanceCost;
use App\Models\MileageHistory;
use App\Models\Schedule;
use App\Models\SystemConfig;
use App\Models\Vehicle;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;

class ScheduleServiceRepository
{

//    public function __construct(Application $app)
//    {
//        parent::__construct($app);
//    }

    public function InitScheduleCalculation($vehicleId, $startDate = null, $endDate = null)
    {
        // =================== start periodic schedule calculation ====================//
        $yearConfig = SystemConfig::where('sys_param', 'next_year')->first()->sys_value;

        $vehicle = Vehicle::select('id', 'inspection_expiration_date', 'first_registration', 'truck_classification_number', 'department_id')
            ->where('id', $vehicleId)->where('truck_classification_number', '>=', 3)->first();
        if (!$vehicle || !$vehicle->inspection_expiration_date) {
            return;
        }

        $firstOfMonthAccountingYear = Carbon::create(Carbon::now())->firstOfMonth();

        if ($startDate) {
            $firstOfMonthAccountingYear = Carbon::parse($startDate)->firstOfMonth();
        }

        $endOfOfMonthAccountingCfYear = Carbon::create($yearConfig, 3)->addYear()->endOfMonth();
        if ($endDate) {
            $endOfOfMonthAccountingCfYear = Carbon::parse($endDate)->endOfMonth();
        }

        // danh sách bảo trì 3 và 12 tháng theo yêu cầu của nhà nước
        $vhc_month = Carbon::parse($vehicle->inspection_expiration_date)->month;
        $expirationDay = Carbon::parse($vehicle->inspection_expiration_date)->day;
        $inspection_expiration_date = Carbon::create(Carbon::now()->subYear()->year, $vhc_month, $expirationDay);
        $diffCompare = Carbon::parse($inspection_expiration_date)->firstOfMonth()->diffInMonths($endOfOfMonthAccountingCfYear) / 3;
        $vhcNextMonth = Carbon::parse($inspection_expiration_date->format('Y-m-d'));
        for ($i = 0; $i <= $diffCompare; $i++) {
            $vhcNextMonth->addMonths(3);
            if ($vhcNextMonth->gt($endOfOfMonthAccountingCfYear)) {
                break;
            }
            if (!$vhcNextMonth->between($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear)) {
                continue;
            }

            $type = TYPE_THREE_MONTH;
            if ($vhcNextMonth->month == $vhc_month) {
                $type = TYPE_TWELVE_MONTH;
            }

            $mcCheck = MaintenanceCost::select(MaintenanceCost::ID)->where(MaintenanceCost::SCHEDULE_MONTH, $vhcNextMonth->month)
                ->where(MaintenanceCost::SCHEDULE_YEAR, $vhcNextMonth->year)
                ->where(MaintenanceCost::VEHICLE_ID, $vehicle->id)
                ->whereIn(MaintenanceCost::TYPE, [TYPE_THREE_MONTH, TYPE_TWELVE_MONTH])->first();

            $dateExit = MaintenanceCost::select('maintenance_costs.scheduled_date')
                ->leftJoin('vehicles', 'vehicles.id', '=', 'maintenance_costs.vehicle_id')
                ->where('maintenance_costs.schedule_month', $vhcNextMonth->month)
                ->where('maintenance_costs.schedule_year', $vhcNextMonth->year)
                ->whereIn('maintenance_costs.type', [TYPE_THREE_MONTH, TYPE_TWELVE_MONTH])
                ->where('vehicles.department_id', $vehicle->department_id)
                ->orderBy('maintenance_costs.id', 'DESC')
                ->first();

            if ($dateExit && $dateExit->scheduled_date) {
                $vhcNextDateOfMonth = Carbon::parse($dateExit->scheduled_date)->addDay();
                if ($vhcNextDateOfMonth->gt(Carbon::parse($dateExit->scheduled_date)->lastOfMonth())) {
                    $vhcNextDateOfMonth = Carbon::create($vhcNextMonth->year, $vhcNextMonth->month)->firstOfMonth();
                }
            } else {
                $vhcNextDateOfMonth = Carbon::create($vhcNextMonth->year, $vhcNextMonth->month)->firstOfMonth();
            }

            if (!$mcCheck) {
                MaintenanceCost::create([
                    MaintenanceCost::TYPE => $type,
                    MaintenanceCost::VEHICLE_ID => $vehicle->id,
                    MaintenanceCost::SCHEDULED_DATE => $vhcNextDateOfMonth,
                    MaintenanceCost::SCHEDULE_MONTH => $vhcNextMonth->month,
                    MaintenanceCost::SCHEDULE_YEAR => $vhcNextMonth->year,
                    MaintenanceCost::STATUS => STATUS_NOT_INPUTTED
                ]);
            }
        }

        // =========== end periodic schedule calculation ==================//


        // =========== start accessory schedule calculation ====================//

        if (!$vehicle->truck_classification_number || (int)$vehicle->truck_classification_number < 3) {
            return;
        }

        $vhcTonnage = ($vehicle->truck_classification_number <= 4) ? $vehicle->truck_classification_number : 4;
        $passedYearChk = Accessory::select('passed_year')->where('tonnage', $vhcTonnage)->groupBy('passed_year')->orderBy('passed_year', 'ASC')->get();

        foreach ($passedYearChk as $key => $accessory) {
            $listAccessory = null;
            if ($accessory->passed_year > 0) {
                $resultYears = CarbonPeriod::create(Carbon::parse($vehicle->first_registration)->firstOfMonth(), $accessory->passed_year . ' year', $endOfOfMonthAccountingCfYear);
                foreach ($resultYears as $resultYear) {
                    if (Carbon::parse($resultYear->format('Y-m-d'))->between($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear)) {
                        $lstAccessory = Accessory::where('tonnage', $vhcTonnage)->where('passed_year', $accessory->passed_year)
                            ->whereRaw(' DATE_SUB(`created_at`,INTERVAL DAYOFMONTH(`created_at`)-1 DAY) <= ? ', $resultYear->format('Y-m-d'))
                            ->pluck('id', 'id')->toArray();
                        $schedule = Schedule::where('vehicle_id', $vehicle->id)->where('type', $accessory->passed_year)
                            ->whereDate('month', $resultYear->format('Y-m-d'))->first();
                        if (!$schedule && count($lstAccessory) > 0) {
                            Schedule::create([
                                'vehicle_id' => $vehicle->id,
                                'month' => $resultYear->format('Y-m-d'),
                                'type' => $accessory->passed_year,
                                'list_accessory' => array_values($lstAccessory),
                            ]);
                        }
                    }
                }
            }
        }

        $schedules = Schedule::select('vehicle_id', 'month')->where('vehicle_id', $vehicleId)->groupBy('vehicle_id', 'month')->get();
        foreach ($schedules as $schedule) {
            $mcCheck = MaintenanceCost::where(MaintenanceCost::SCHEDULE_MONTH, Carbon::parse($schedule->month)->month)
                ->where(MaintenanceCost::SCHEDULE_YEAR, Carbon::parse($schedule->month)->year)
                ->where(MaintenanceCost::VEHICLE_ID, $schedule->vehicle_id)
                ->where(MaintenanceCost::TYPE, TYPE_ACCESSORY_CHANGE)->first();

            if (!$mcCheck) {
                MaintenanceCost::create([
                    MaintenanceCost::TYPE => TYPE_ACCESSORY_CHANGE,
                    MaintenanceCost::VEHICLE_ID => $schedule->vehicle_id,
                    MaintenanceCost::SCHEDULED_DATE => $schedule->month,
                    MaintenanceCost::SCHEDULE_MONTH => Carbon::parse($schedule->month)->month,
                    MaintenanceCost::SCHEDULE_YEAR => Carbon::parse($schedule->month)->year,
                    MaintenanceCost::STATUS => STATUS_NOT_INPUTTED
                ]);
            }
        }
        // =========== end accessory schedule calculation ====================//
    }


    public function RecalculateScheduledAccessory($vehicleId, $startDate = null, $endDate = null)
    {
        $vehicle = Vehicle::select('id', 'first_registration', 'truck_classification_number', 'created_at')
            ->where('truck_classification_number', '>=', 3)->where('id', $vehicleId)->first();

        $maintenanceAccessories = MaintenanceAccessory::select('maintenance_costs.type', 'maintenance_costs.maintained_date',
            'maintenance_costs.scheduled_date', 'maintenance_accessories.accessory_id', 'accessories.passed_year', 'accessories.mileage',
            'maintenance_costs.mileage_current', 'maintenance_costs.mileage_last_time')
            ->leftJoin('maintenance_costs', 'maintenance_costs.id', '=', 'maintenance_accessories.maintenance_cost_id')
            ->leftJoin('accessories', 'accessories.id', '=', 'maintenance_accessories.accessory_id')
            ->where('maintenance_accessories.accessory_id', '>', 0)
            ->where('accessories.passed_year', '>', 0)
            ->where('maintenance_costs.vehicle_id', $vehicleId)
            ->orderBy('maintenance_costs.maintained_date', 'DESC')->get();

        $listAccessoryId = [];
        if ($maintenanceAccessories->count() > 0) {
            foreach ($maintenanceAccessories as $mtnAccessory) {
                if (in_array($mtnAccessory->accessory_id, $listAccessoryId)) {
                    continue;
                }
                $listAccessoryId[] = $mtnAccessory->accessory_id;
            }
        }

        $yearConfig = SystemConfig::where('sys_param', 'next_year')->first()->sys_value;

        $firstOfMonthAccountingYear = Carbon::parse($vehicle->created_at)->firstOfMonth();

        if ($startDate) {
            $firstOfMonthAccountingYear = Carbon::parse($startDate)->firstOfMonth();
        }

        $endOfOfMonthAccountingCfYear = Carbon::create($yearConfig, 3)->addYear()->endOfMonth();
        if ($endDate) {
            $endOfOfMonthAccountingCfYear = Carbon::parse($endDate)->endOfMonth();
        }


        $vhcTonnage = ($vehicle->truck_classification_number <= 4) ? $vehicle->truck_classification_number : 4;
        $passedYearChk = Accessory::select('passed_year')->where('tonnage', $vhcTonnage)->groupBy('passed_year')
            ->orderBy('passed_year', 'ASC')->get();

        Schedule::where(Schedule::VEHICLE_ID, $vehicle->id)
            ->whereBetween(Schedule::MONTH, [$firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear])
            ->where(Schedule::TYPE, '>', 0)
            ->delete();

        // Recalculate Scheduled Accessory
        foreach ($passedYearChk as $key => $accessory) {
            $startChk = Carbon::parse($vehicle->first_registration);
            $listAccessory = null;
            if ($accessory->passed_year > 0) {
                $resultYears = CarbonPeriod::create(Carbon::parse($vehicle->first_registration)->firstOfMonth(), $accessory->passed_year . ' year', $endOfOfMonthAccountingCfYear);
                foreach ($resultYears as $resultYear) {
                    if (Carbon::parse($resultYear->format('Y-m-d'))->between($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear)) {
                        $lstAccessory = Accessory::where('tonnage', $vhcTonnage)->where('passed_year', $accessory->passed_year)
                            ->whereNotIn('id', $listAccessoryId)
                            ->whereRaw(' DATE_SUB(`created_at`,INTERVAL DAYOFMONTH(`created_at`)-1 DAY) <= ? ', $resultYear->format('Y-m-d'))
                            ->pluck('id', 'id')->toArray();
                        $schedule = Schedule::where('vehicle_id', $vehicle->id)->where('type', $accessory->passed_year)
                            ->whereDate('month', $resultYear->format('Y-m-d'))->first();
                        if (!$schedule && $lstAccessory && count($lstAccessory) > 0) {
                            $schedule = Schedule::create([
                                'vehicle_id' => $vehicle->id,
                                'month' => $resultYear->format('Y-m-d'),
                                'type' => $accessory->passed_year,
                            ]);
                        }
                        if ($lstAccessory && count($lstAccessory) > 0) {
                            $schedule->list_accessory = array_values($lstAccessory);
                            $schedule->save();
                        }
                    }
                }
            }
        }
        if (count($listAccessoryId) > 0) {
            // Recalculate Scheduled Accessory with maintained_date
            $listAccessoryId2 = [];
            foreach ($maintenanceAccessories as $mtnAccessory) {
                if (in_array($mtnAccessory->accessory_id, $listAccessoryId2)) {
                    continue;
                }
                $listAccessoryId2[] = $mtnAccessory->accessory_id;
                if (in_array($mtnAccessory->type, [TYPE_THREE_MONTH, TYPE_TWELVE_MONTH])) {
                    $denominator = 3;
                    $checkIsMileageInit = MileageHistory::where('vehicle_id', $vehicle->id)->orderBy('created_at','ASC')->get();
                    if ($checkIsMileageInit->count() == 2) {
                        $denominator = ceil(Carbon::parse($checkIsMileageInit->first()->date)->floatDiffInMonths(Carbon::parse($mtnAccessory->maintained_date)));
                    }
                    if (!$denominator || $denominator == 0) {
                        continue;
                    }
                    $avgMileageMonth = round(($mtnAccessory->mileage_current - $mtnAccessory->mileage_last_time) / $denominator);
                    $numberMonthOfCost = $mtnAccessory->mileage / $avgMileageMonth;
                    if (($mtnAccessory->mileage % $avgMileageMonth) !== 0) {
                        $numberMonthOfCost = ceil($numberMonthOfCost);
                    }

                    $nextMonthAvgMileage = Carbon::parse($mtnAccessory->maintained_date)->addMonths($numberMonthOfCost);
                    $firstNext = $nextMonthPassedYear = Carbon::parse($mtnAccessory->maintained_date)->addYears($mtnAccessory->passed_year);

                    if ($nextMonthAvgMileage->lt($nextMonthPassedYear)) {
                        $firstNext = $nextMonthAvgMileage;
                    }

                    $resultYears = CarbonPeriod::create(Carbon::parse($firstNext)->firstOfMonth(), $mtnAccessory->passed_year . ' year', $endOfOfMonthAccountingCfYear);

                    foreach ($resultYears as $resultYear) {
                        if (Carbon::parse($resultYear->format('Y-m-d'))->between($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear)) {
                            $schedule = Schedule::where('vehicle_id', $vehicle->id)->where('type', $mtnAccessory->passed_year)
                                ->whereDate('month', $resultYear->format('Y-m-d'))->first();
                            if (!$schedule) {
                                $schedule = Schedule::create([
                                    'vehicle_id' => $vehicle->id,
                                    'month' => $resultYear->format('Y-m-d'),
                                    'type' => $mtnAccessory->passed_year,
                                    'list_accessory' => array_values([$mtnAccessory->accessory_id])
                                ]);
                            } else {
                                $schedule->list_accessory = $this->MergeAccessory($schedule->list_accessory, [$mtnAccessory->accessory_id]);
                                $schedule->save();
                            }
                        }
                    }

                } else if ($mtnAccessory->type == TYPE_OTHER) {

                    $resultYears = CarbonPeriod::create(Carbon::parse($mtnAccessory->maintained_date)->addYears($mtnAccessory->passed_year)->firstOfMonth(), $mtnAccessory->passed_year . ' year', $endOfOfMonthAccountingCfYear);

                    $mtCostPeriodic = MaintenanceCost::where('vehicle_id', $vehicle->id)
                        ->whereIn(MaintenanceCost::TYPE, [TYPE_THREE_MONTH, TYPE_TWELVE_MONTH])
                        ->where(MaintenanceCost::STATUS, STATUS_INPUTTED)
                        ->whereNotNull(MaintenanceCost::MAINTAINED_DATE)
                        ->orderBy(MaintenanceCost::MAINTAINED_DATE, 'DESC')->first();

                    if ($mtCostPeriodic) {
                        $denominator = 3;
                        $checkIsMileageInit = MileageHistory::where('vehicle_id', $vehicle->id)->orderBy('created_at','ASC')->get();
                        if ($checkIsMileageInit->count() == 2) {
                            $denominator = ceil(Carbon::parse($checkIsMileageInit->first()->date)->floatDiffInMonths(Carbon::parse($mtCostPeriodic->maintained_date)));
                        }
                        if ($denominator && $denominator > 0) {
                            $avgMileageMonth = round(($mtCostPeriodic->mileage_current - $mtCostPeriodic->mileage_last_time) / $denominator);
                            $numberMonthOfCost = $mtnAccessory->mileage / $avgMileageMonth;
                            if (($mtnAccessory->mileage % $avgMileageMonth) !== 0) {
                                $numberMonthOfCost = ceil($numberMonthOfCost);
                            }

                            $nextMonthAvgMileage = Carbon::parse($mtnAccessory->maintained_date)->addMonths($numberMonthOfCost);
                            $firstNext = $nextMonthPassedYear = Carbon::parse($mtnAccessory->maintained_date)->addYears($mtnAccessory->passed_year);

                            if ($nextMonthAvgMileage->lt($nextMonthPassedYear)) {
                                $firstNext = $nextMonthAvgMileage;
                            }

                            $resultYears = CarbonPeriod::create(Carbon::parse($firstNext)->firstOfMonth(), $mtnAccessory->passed_year . ' year', $endOfOfMonthAccountingCfYear);
                        }
                    }

                    foreach ($resultYears as $resultYear) {
                        if (Carbon::parse($resultYear->format('Y-m-d'))->between($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear)) {
                            $schedule = Schedule::where('vehicle_id', $vehicle->id)->where('type', $mtnAccessory->passed_year)
                                ->whereDate('month', $resultYear->format('Y-m-d'))->first();
                            if (!$schedule) {
                                $schedule = Schedule::create([
                                    'vehicle_id' => $vehicle->id,
                                    'month' => $resultYear->format('Y-m-d'),
                                    'type' => $mtnAccessory->passed_year,
                                    'list_accessory' => array_values([$mtnAccessory->accessory_id])
                                ]);
                            } else {
                                $schedule->list_accessory = $this->MergeAccessory($schedule->list_accessory, [$mtnAccessory->accessory_id]);
                                $schedule->save();
                            }
                        }
                    }
                } else {
                    $resultYears = CarbonPeriod::create(Carbon::parse($mtnAccessory->maintained_date)->addYears($mtnAccessory->passed_year)->firstOfMonth(), $mtnAccessory->passed_year . ' year', $endOfOfMonthAccountingCfYear);

                    $mtCostPeriodic = MaintenanceCost::where('vehicle_id', $vehicle->id)
                        ->whereIn(MaintenanceCost::TYPE, [TYPE_THREE_MONTH, TYPE_TWELVE_MONTH])
                        ->where(MaintenanceCost::STATUS, STATUS_INPUTTED)
                        ->whereNotNull(MaintenanceCost::MAINTAINED_DATE)
                        ->orderBy(MaintenanceCost::MAINTAINED_DATE, 'DESC')->first();

                    if ($mtCostPeriodic) {
                        $denominator = 3;
                        $checkIsMileageInit = MileageHistory::where('vehicle_id', $vehicle->id)->orderBy('created_at','ASC')->get();
                        if ($checkIsMileageInit->count() == 2) {
                            $denominator = ceil(Carbon::parse($checkIsMileageInit->first()->date)->floatDiffInMonths(Carbon::parse($mtCostPeriodic->maintained_date)));
                        }
                        if ($denominator && $denominator > 0) {
                            $avgMileageMonth = round(($mtCostPeriodic->mileage_current - $mtCostPeriodic->mileage_last_time) / $denominator);
                            $numberMonthOfCost = $mtnAccessory->mileage / $avgMileageMonth;
                            if (($mtnAccessory->mileage % $avgMileageMonth) !== 0) {
                                $numberMonthOfCost = ceil($numberMonthOfCost);
                            }

                            $nextMonthAvgMileage = Carbon::parse($mtnAccessory->maintained_date)->addMonths($numberMonthOfCost);
                            $firstNext = $nextMonthPassedYear = Carbon::parse($mtnAccessory->maintained_date)->addYears($mtnAccessory->passed_year);

                            if ($nextMonthAvgMileage->lt($nextMonthPassedYear)) {
                                $firstNext = $nextMonthAvgMileage;
                            }

                            $resultYears = CarbonPeriod::create(Carbon::parse($firstNext)->firstOfMonth(), $mtnAccessory->passed_year . ' year', $endOfOfMonthAccountingCfYear);
                        }
                    }

                    foreach ($resultYears as $resultYear) {
                        if (Carbon::parse($resultYear->format('Y-m-d'))->between($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear)) {
                            $schedule = Schedule::where('vehicle_id', $vehicle->id)->where('type', $mtnAccessory->passed_year)
                                ->whereDate('month', $resultYear->format('Y-m-d'))->first();
                            if (!$schedule) {
                                $schedule = Schedule::create([
                                    'vehicle_id' => $vehicle->id,
                                    'month' => $resultYear->format('Y-m-d'),
                                    'type' => $mtnAccessory->passed_year,
                                    'list_accessory' => array_values([$mtnAccessory->accessory_id])
                                ]);
                            } else {
                                $schedule->list_accessory = $this->MergeAccessory($schedule->list_accessory, [$mtnAccessory->accessory_id]);
                                $schedule->save();
                            }
                        }
                    }
                }
            }
        }

        // Recalculate Scheduled passed_year = 0
        Schedule::where(Schedule::VEHICLE_ID, $vehicle->id)
            ->whereBetween(Schedule::MONTH, [$firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear])
            ->where(Schedule::TYPE, 0)
            ->delete();
        // Recalculate Scheduled oil
        $oil = Accessory::where('name', 'エンジンオイル')->where('tonnage', $vhcTonnage)->where('passed_year', 0)->first();
        $oilElement = Accessory::where('name', 'オイルエレメント')->where('tonnage', $vhcTonnage)->where('passed_year', 0)->first();

        if ($oil && $oilElement) {
            $mtnAccessoryOil = MaintenanceAccessory::select('maintenance_costs.type', 'maintenance_costs.maintained_date',
                'maintenance_costs.scheduled_date', 'maintenance_accessories.accessory_id')
                ->leftJoin('maintenance_costs', 'maintenance_costs.id', '=', 'maintenance_accessories.maintenance_cost_id')
                ->where('maintenance_costs.vehicle_id', $vehicleId)
                ->where('maintenance_accessories.accessory_id', $oil->id)
                ->orderBy('maintenance_costs.maintained_date', 'DESC')->first();

            //check 2 change Oil
            $check2Oil = false;
            if ($mtnAccessoryOil) {
                $mtnAccessoryOilElm = MaintenanceAccessory::select('maintenance_costs.type', 'maintenance_costs.maintained_date',
                    'maintenance_costs.scheduled_date', 'maintenance_accessories.accessory_id')
                    ->leftJoin('maintenance_costs', 'maintenance_costs.id', '=', 'maintenance_accessories.maintenance_cost_id')
                    ->where('maintenance_costs.vehicle_id', $vehicleId)
                    ->where('maintenance_accessories.accessory_id', $oilElement->id)
                    ->whereBetween('maintenance_costs.maintained_date', [Carbon::parse($mtnAccessoryOil->maintained_date)->firstOfMonth(), Carbon::parse($mtnAccessoryOil->maintained_date)->endOfMonth()])
                    ->orderBy('maintenance_costs.maintained_date', 'DESC')->first();
                if ($mtnAccessoryOilElm) {
                    $check2Oil = true;
                }
            }

            $mtnAccessoriesMonth = MaintenanceCost::where('vehicle_id', $vehicle->id)
                ->whereIn(MaintenanceCost::TYPE, [TYPE_THREE_MONTH, TYPE_TWELVE_MONTH])
                ->where(MaintenanceCost::STATUS, STATUS_INPUTTED)
                ->whereNotNull(MaintenanceCost::MAINTAINED_DATE)
                ->orderBy(MaintenanceCost::MAINTAINED_DATE, 'DESC')->first();
            if ($mtnAccessoriesMonth && $mtnAccessoryOil) {
                $denominator = 3;
                $checkIsMileageInit = MileageHistory::where('vehicle_id', $vehicle->id)->orderBy('created_at','ASC')->get();
                if ($checkIsMileageInit->count() == 2) {
                    $denominator = ceil(Carbon::parse($checkIsMileageInit->first()->date)->floatDiffInMonths(Carbon::parse($mtnAccessoriesMonth->maintained_date)));
                }
                if ($denominator && $denominator > 0) {
                    $avgMileageMonth = round(($mtnAccessoriesMonth->mileage_current - $mtnAccessoriesMonth->mileage_last_time) / $denominator);
                    $numberMonthOfCost = $oil->mileage / $avgMileageMonth;
                    if (($oil->mileage % $avgMileageMonth) !== 0) {
                        $numberMonthOfCost = ceil($numberMonthOfCost);
                    }
                    $scheduledDate = $mtnAccessoriesMonth->maintained_date;
                    if ($mtnAccessoryOil && Carbon::parse($mtnAccessoryOil->maintained_date)->gt(Carbon::parse($scheduledDate))) {
                        $scheduledDate = $mtnAccessoryOil->maintained_date;
                    }

                    $resultYears = CarbonPeriod::create(Carbon::parse($scheduledDate)->addMonths($numberMonthOfCost)->firstOfMonth(), $numberMonthOfCost . ' month', $endOfOfMonthAccountingCfYear);
                    $listOil = Accessory::where('name', 'エンジンオイル')->where('tonnage', $vhcTonnage)->where('passed_year', 0)->pluck('id', 'id')->toArray();
                    $listOilElement = Accessory::whereIn('name', ['エンジンオイル', 'オイルエレメント'])->where('tonnage', $vhcTonnage)->where('passed_year', 0)->pluck('id', 'id')->toArray();
                    if ($check2Oil) {
                        $listOilAll = $listOil;
                    } else {
                        $listOilAll = $listOilElement;
                    }
                    foreach ($resultYears as $key => $resultYear) {
                        if (count($listOilAll) <= 0 || $key > 0) {
                            continue;
                        }
                        if (Carbon::parse($resultYear->format('Y-m-d'))->between($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear)) {
                            $schedule = Schedule::where('vehicle_id', $vehicle->id)->where('type', 0)
                                ->whereDate('month', $resultYear->format('Y-m-d'))->first();
                            if (!$schedule) {
                                $schedule = Schedule::create([
                                    'vehicle_id' => $vehicle->id,
                                    'month' => $resultYear->format('Y-m-d'),
                                    'type' => 0,
                                    'list_accessory' => array_values($listOilAll)
                                ]);
                            } else {
                                $schedule->list_accessory = $this->MergeAccessory($schedule->list_accessory, $listOilAll);
                                $schedule->save();
                            }

                            if (count($listOilAll) == 1) {
                                $listOilAll = $listOilElement;
                            } else {
                                $listOilAll = $listOil;
                            }
                        }
                    }
                }
            }
        }

        //Recalculate maintenance cost;
        $schedules = Schedule::select('vehicle_id', 'month')->where('vehicle_id', $vehicle->id)->groupBy('month')->get();

        MaintenanceCost::where(MaintenanceCost::VEHICLE_ID, $vehicle->id)
            ->where(MaintenanceCost::TYPE, TYPE_ACCESSORY_CHANGE)
            ->where(function ($query) {
                $query->where(MaintenanceCost::STATUS, STATUS_NOT_INPUTTED)
                    ->orWhereNull(MaintenanceCost::MAINTAINED_DATE);
            })->delete();

        foreach ($schedules as $schedule) {
            $mcCheck = MaintenanceCost::where(MaintenanceCost::SCHEDULE_MONTH, Carbon::parse($schedule->month)->month)
                ->where(MaintenanceCost::SCHEDULE_YEAR, Carbon::parse($schedule->month)->year)
                ->where(MaintenanceCost::VEHICLE_ID, $schedule->vehicle_id)
                ->where(MaintenanceCost::TYPE, TYPE_ACCESSORY_CHANGE)->first();

            if (!$mcCheck) {
                MaintenanceCost::create([
                    MaintenanceCost::TYPE => TYPE_ACCESSORY_CHANGE,
                    MaintenanceCost::VEHICLE_ID => $schedule->vehicle_id,
                    MaintenanceCost::SCHEDULED_DATE => $schedule->month,
                    MaintenanceCost::SCHEDULE_MONTH => Carbon::parse($schedule->month)->month,
                    MaintenanceCost::SCHEDULE_YEAR => Carbon::parse($schedule->month)->year,
                    MaintenanceCost::STATUS => STATUS_NOT_INPUTTED
                ]);
            } else {
                // update status to inputting
                if ($mcCheck->status == STATUS_INPUTTED) {
                    $mcCheck->status = STATUS_IN_INPUTTING;
                    $mcCheck->save();
                }
            }
        }

        // update status to inputted
        $mcCheckInputtings = MaintenanceCost::where(MaintenanceCost::VEHICLE_ID, $vehicle->id)
            ->where(MaintenanceCost::TYPE, TYPE_ACCESSORY_CHANGE)
            ->where(MaintenanceCost::STATUS, STATUS_IN_INPUTTING)
            ->whereNotNull(MaintenanceCost::MAINTAINED_DATE)
            ->get();

        if ($mcCheckInputtings->count() > 0) {
            foreach ($mcCheckInputtings as $mcCheckInputting) {
                $schedule = Schedule::select('vehicle_id', 'month')
                    ->where('vehicle_id', $vehicle->id)
                    ->whereBetween('month', [Carbon::parse($mcCheckInputting->maintained_date)->firstOfMonth(), Carbon::parse($mcCheckInputting->maintained_date)->endOfMonth()])
                    ->first();
                if (!$schedule) {
                    $mcCheckInputting->status = STATUS_INPUTTED;
                    $mcCheckInputting->save();
                }
            }
        }
    }

    private function MergeAccessory($array1, $array2)
    {
        return array_unique(array_merge(array_values($array1), array_values($array2)));
    }
}
