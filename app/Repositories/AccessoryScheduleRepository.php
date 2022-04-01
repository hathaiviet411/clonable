<?php
/**
 * Created by VeHo.
 * Year: 2022-01-26
 */

namespace Repository;

use App\Models\Accessory;
use App\Models\AccessorySchedule;
use App\Models\MaintenanceAccessory;
use App\Models\MaintenanceCost;
use App\Models\PlateHistory;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Repositories\Contracts\AccessoryScheduleRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

class AccessoryScheduleRepository extends BaseRepository implements AccessoryScheduleRepositoryInterface
{

    public function __construct(Application $app)
    {
        parent::__construct($app);

    }

    /**
     * Instantiate model
     *
     * @param AccessorySchedule $model
     */

    public function model()
    {
        return Vehicle::class;
    }

    public function index($request)
    {
        $year = $request->get('year');
        $vehicle_id = $request->get('vehicle_id');
        $department_id = $request->get('department_id');
        $accessory = $request->get('accessory');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        return $this->scheduleAccessories($year, $vehicle_id, $department_id, true, $accessory);
    }

    public function getLstAccessory($firstOfMonth, $endOfMonth, $vehicleId)
    {
        $arrayContent = [];
        $checkLate = $endOfMonth->lt(Carbon::now());

        //0 là đỏ, 1 là xanh, 3 là trắng
        $listVhcs = $schedules = Schedule::query()
            ->where(Schedule::VEHICLE_ID, $vehicleId)
            ->whereBetween(Schedule::MONTH, [$firstOfMonth, $endOfMonth])
            ->get();
        $lstAccessArr = [];
        foreach ($listVhcs as $listVhc) {
            $lstAccessArr = array_merge($lstAccessArr, $listVhc->list_accessory);
        }
        if ($lstAccessArr) {
            $listAccessName = Accessory::whereIn('id', $lstAccessArr)->pluck('name', 'id')->toArray();
            foreach ($listAccessName as $lst) {
                $arrayContent[] = [
                    'color' => $checkLate ? 0 : 3,
                    'text' => $lst,
                ];
            }
        }

        $listVhcs = $schedules = MaintenanceAccessory::select('maintenance_costs.scheduled_date', 'maintenance_accessories.accessory_id')
            ->leftJoin('maintenance_costs', 'maintenance_costs.id', '=', 'maintenance_accessories.maintenance_cost_id')
            ->where('maintenance_accessories.accessory_id', '>', 0)
            ->where('maintenance_costs.vehicle_id', $vehicleId)
            ->whereBetween('maintenance_costs.maintained_date', [$firstOfMonth, $endOfMonth])
            ->pluck('maintenance_accessories.accessory_id', 'maintenance_accessories.accessory_id')->toArray();

        if ($listVhcs) {
            $listAccessName = Accessory::withTrashed()->whereIn('id', $listVhcs)->pluck('name', 'id')->toArray();
            foreach ($listAccessName as $lst) {
                $arrayContent[] = [
                    'color' => 1,
                    'text' => $lst,
                ];
            }
        }
        return $arrayContent;
    }

    public function scheduleVehicleAccessories(int $year, int $vehicle_id, int $department_id)
    {
        $result = [
            ($year - 1) => [],
            ($year) => [],
            ($year + 1) => [],
            ($year + 2) => []
        ];
        for ($i = $year - 1; $i <= $year + 2; $i++) {
            $data = $this->scheduleAccessories($i, $vehicle_id, $department_id, false);
            foreach ($data as $key => $sch) {
                $schYear = date('Y', strtotime($sch[0]['month']));
                $result[$schYear] = $sch;
                break;
            }
        }
        return array_values($result);
    }

    private function scheduleAccessories($year, $vehicle_id, $department_id, $isAccountingYear = true, $accessory = null)
    {
        if (!$year) {
            $year = Carbon::now()->year;
            //check accounting year
        }

        if ($isAccountingYear) {
            $firstOfMonthAccountingYear = Carbon::create($year, 4)->firstOfMonth();
            $endOfOfMonthAccountingCfYear = Carbon::create($year, 3)->addYear()->endOfMonth();
        } else {
            $firstOfMonthAccountingYear = Carbon::create($year, 1)->firstOfMonth();
            $endOfOfMonthAccountingCfYear = Carbon::create($year, 12)->endOfMonth();
        }

        $listVhcs = $schedules = Schedule::query()
            ->when($vehicle_id, function ($query) use ($vehicle_id) {
                return $query->where(Schedule::VEHICLE_ID, $vehicle_id);
            })->when($year, function ($query) use ($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear) {
                return $query->whereBetween(Schedule::MONTH, [$firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear]);
            })->get();

        $accessories = [];
        if ($accessory) {
            $listVhcs = null;
            $accessories = Accessory::where('name', 'like', '%' . $accessory . '%')->pluck('id')->toArray();
            if ($accessories) {
                foreach ($schedules as $schedule) {
                    $arrayDiff = array_diff($accessories, $schedule->list_accessory);
                    if (count($arrayDiff) !== count($accessories)) {
                        $listVhcs[] = $schedule;
                    }
                }
            }
        }

        $maintenanceAsc = MaintenanceAccessory::select('maintenance_costs.type', 'maintenance_costs.vehicle_id',
            'maintenance_costs.scheduled_date', 'maintenance_accessories.accessory_id')
            ->leftJoin('maintenance_costs', 'maintenance_costs.id', '=', 'maintenance_accessories.maintenance_cost_id')
            ->whereBetween('maintenance_costs.maintained_date', [$firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear])
            ->where('maintenance_accessories.accessory_id', '>', 0)
            ->when($accessory, function ($query) use ($accessories) {
                return $query->whereIn('maintenance_accessories.accessory_id', $accessories);
            })->when($vehicle_id, function ($query) use ($vehicle_id) {
                return $query->where('maintenance_costs.vehicle_id', $vehicle_id);
            })->get();

        $listVhcArr = [];
        if ($listVhcs) {
            foreach ($listVhcs as $listVhc) {
                if (!in_array($listVhc->vehicle_id, $listVhcArr)) {
                    $listVhcArr[] = $listVhc->vehicle_id;
                }
            }
        }
        if ($maintenanceAsc) {
            foreach ($maintenanceAsc as $maintenanceAscVhc) {
                if (!in_array($maintenanceAscVhc->vehicle_id, $listVhcArr)) {
                    $listVhcArr[] = $maintenanceAscVhc->vehicle_id;
                }
            }
        }

        $latestPlate = DB::table('vehicle_no_number_plate_history as latestPlate')
            ->select('latestPlate.vehicle_id', DB::raw('MAX(latestPlate.date) as last_date'))->groupBy('latestPlate.vehicle_id');
        $plateHistory = PlateHistory::select('vehicle_no_number_plate_history.id', 'vehicle_no_number_plate_history.no_number_plate', 'vehicle_no_number_plate_history.vehicle_id')
            ->joinSub($latestPlate, 'latestPlate', function ($join) {
                $join->on('latestPlate.vehicle_id', '=', 'vehicle_no_number_plate_history.vehicle_id')
                    ->on('latestPlate.last_date', '=', 'vehicle_no_number_plate_history.date');
            });
        $resultQuerys = MaintenanceCost::select('maintenance_costs.id', 'maintenance_costs.vehicle_id', 'maintenance_costs.status',
            'maintenance_costs.type', 'departments.name as department_name', 'maintenance_costs.scheduled_date',
            'maintenance_costs.maintained_date', 'plate_history.no_number_plate')
            ->leftJoin('vehicles', 'vehicles.id', '=', 'maintenance_costs.vehicle_id')
            ->leftJoin('departments', 'departments.id', '=', 'vehicles.department_id')
            ->leftJoinSub($plateHistory, 'plate_history', function ($join) {
                $join->on('plate_history.vehicle_id', '=', 'vehicles.id');
            })->where(function ($query) use ($firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear) {
                $query->whereBetween('maintenance_costs.scheduled_date', [$firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear])
                    ->orWhereBetween('maintenance_costs.maintained_date', [$firstOfMonthAccountingYear, $endOfOfMonthAccountingCfYear]);
            })->whereIn('maintenance_costs.vehicle_id', $listVhcArr)
            ->when($department_id, function ($query) use ($department_id) {
                return $query->where('vehicles.department_id', $department_id);
            })->groupBy('maintenance_costs.vehicle_id')->get();

        $listObj = [];
        if ($resultQuerys->count() > 0) {
            foreach ($resultQuerys as $key => $resultQuery) {
                $keyArray = $resultQuery->no_number_plate . '-' . $resultQuery->vehicle_id;

                $resultYears = CarbonPeriod::create($firstOfMonthAccountingYear, '1 month', $endOfOfMonthAccountingCfYear);
                foreach ($resultYears as $resultYear) {
                    $firstOfMonth = Carbon::parse($resultYear)->firstOfMonth();
                    $endOfMonth = Carbon::parse($resultYear)->endOfMonth();
                    if (!$this->checkVhcExist($firstOfMonth, $endOfMonth, $resultQuery->vehicle_id)) {
                        continue;
                    }
                    $content = $this->getLstAccessory($firstOfMonth, $endOfMonth, $resultQuery->vehicle_id);
                    if (count($content) > 0) {
                        $listObj[$keyArray][] = [
                            'month' => $endOfMonth,
                            'content' => $content,
                        ];
                    }
                }
            }
        }
        //return collect($listObj)->forPage($page, $perPage);
        return $listObj;
    }

    public function checkVhcExist($firstOfMonth, $endOfMonth, $vehicleId)
    {
        $resultChk = MaintenanceCost::whereBetween(MaintenanceCost::SCHEDULED_DATE, [$firstOfMonth, $endOfMonth])
            ->orWhereBetween(MaintenanceCost::MAINTAINED_DATE, [$firstOfMonth, $endOfMonth])
            ->where(MaintenanceCost::VEHICLE_ID, $vehicleId)->first();
        if ($resultChk) {
            return true;
        } else {
            return false;
        }
    }
}
