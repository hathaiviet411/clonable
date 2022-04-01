<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace Repository;

use App\Exports\MaintenanceCostExport;
use App\Jobs\RecalculateScheduleJob;
use App\Models\Accessory;
use App\Models\Department;
use App\Models\MaintenanceAccessory;
use App\Models\MaintenanceCost;
use App\Models\MaintenanceLease;
use App\Models\MaintenanceWage;
use App\Models\PlateHistory;
use App\Models\Vehicle;
use App\Repositories\Contracts\MaintenanceCostRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Application;
use App\Models\MileageHistory;
use App\Models\Schedule;
use Error;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
class MaintenanceCostRepository extends BaseRepository implements MaintenanceCostRepositoryInterface
{

    private $path = "export";
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * Instantiate model
     *
     * @param MaintenanceCost $model
     */

    public function model()
    {
        return MaintenanceCost::class;
    }

    public function maintenanceCodePaginate(
        $department_id, $yearMonth, $vehicleId = null, $garage = null, $type = null, $status = null,
        $scheduleDate = null, $maintainedDate = null, $sortBy = null, $sortType = null, $perPage = null)
    {

        $sortBy = $sortBy ? $sortBy : 'plate_history.no_number_plate';

//        if ($sortBy == 'garage') {
//            $sortBy = 'maintenance_leases.garage';
//        }
        if ($sortBy == 'type') {
            $sortBy = 'maintenance_costs.type';
        }
        if ($sortBy == 'schedule_date') {
            $sortBy = 'maintenance_costs.scheduled_date';
        }
        if ($sortBy == 'maintained_date') {
            $sortBy = 'maintenance_costs.maintained_date';
        }
        if ($sortBy == 'status') {
            $sortBy = 'maintenance_costs.status';
        }

        $sortType = $sortType && $sortType ? 'desc' : 'asc';


//        $user = Auth::user();
//        $userRoles = collect($user->getRoleNames());

//        $department = Department::find($department_id);
//        if ($user && $userRoles->search(ROLE_TEAM)) {
//            $departmentUser = $user->department_id;
//            if (!$department && $department->id !== $departmentUser) {
//                return [];
//            }
//        }

        $checkFtScheduleDate = null;
        if (Arr::get($scheduleDate, 'from') || Arr::get($scheduleDate, 'to')) {
            $checkFtScheduleDate = true;
        }

        $checkFtMaintainedDate = null;
        if (Arr::get($maintainedDate, 'from') || Arr::get($maintainedDate, 'to')) {
            $checkFtMaintainedDate = true;
        }

        $scheduleDateFrom = Carbon::create($yearMonth)->firstOfYear();
        if (Arr::get($scheduleDate, 'from')) {
            $scheduleDateFrom = Carbon::parse(Arr::get($scheduleDate, 'from'));
        }

        $scheduleDateTo = Carbon::create($yearMonth)->lastOfYear();
        if (Arr::get($scheduleDate, 'to')) {
            $scheduleDateTo = Carbon::parse(Arr::get($scheduleDate, 'to'));
        }

        $maintainedDateFrom = Carbon::create($yearMonth)->firstOfYear();
        if (Arr::get($maintainedDate, 'from')) {
            $maintainedDateFrom = Carbon::parse(Arr::get($maintainedDate, 'from'));
        }

        $maintainedDateTo = Carbon::create($yearMonth)->lastOfYear();
        if (Arr::get($maintainedDate, 'to')) {
            $maintainedDateTo= Carbon::parse(Arr::get($maintainedDate, 'to'));
        }

        $latestPlate = DB::table('vehicle_no_number_plate_history as latestPlate')
            ->select('latestPlate.vehicle_id', DB::raw('MAX(latestPlate.date) as last_date'))->groupBy('latestPlate.vehicle_id');
        $plateHistory = PlateHistory::select('vehicle_no_number_plate_history.id', 'vehicle_no_number_plate_history.no_number_plate', 'vehicle_no_number_plate_history.vehicle_id')
            ->joinSub($latestPlate, 'latestPlate', function ($join) {
                $join->on('latestPlate.vehicle_id', '=', 'vehicle_no_number_plate_history.vehicle_id')
                    ->on('latestPlate.last_date', '=', 'vehicle_no_number_plate_history.date');
            });
        $maintenanceLease = MaintenanceLease::select('id', 'garage', 'vehicle_id')->orderBy('id', 'DESC')->groupBy('vehicle_id');

        $query = MaintenanceCost::select('maintenance_costs.id', 'maintenance_costs.vehicle_id', 'maintenance_costs.status',
            'maintenance_costs.type', 'departments.name as department_name', 'maintenance_costs.scheduled_date',
            'maintenance_costs.maintained_date', 'plate_history.no_number_plate', 'maintenance_leases.garage')
            ->leftJoin('vehicles', 'vehicles.id', '=', 'maintenance_costs.vehicle_id')
            ->leftJoin('departments', 'departments.id', '=', 'vehicles.department_id')
            ->leftJoinSub($maintenanceLease, 'maintenance_leases', function ($join) {
                $join->on('vehicles.id', '=', 'maintenance_leases.vehicle_id');
            })
            ->leftJoinSub($plateHistory, 'plate_history', function ($join) {
                $join->on('plate_history.vehicle_id', '=', 'vehicles.id');
            })
            ->when($vehicleId, function ($query) use ($vehicleId) {
                return $query->where('maintenance_costs.vehicle_id', $vehicleId);
            })->when($department_id, function ($query) use ($department_id) {
                return $query->where('vehicles.department_id', $department_id);
            })->when($garage, function ($query) use ($garage) {
                return $query->where('maintenance_leases.garage', 'like', '%' . $garage . '%');
            })->when($type, function ($query) use ($type) {
                return $query->where('maintenance_costs.type', $type);
            })->when($status, function ($query) use ($status) {
                return $query->where('maintenance_costs.status', $status);
            })->whereBetween('maintenance_costs.scheduled_date', [$scheduleDateFrom, $scheduleDateTo])
            ->when($checkFtMaintainedDate, function ($query) use ($maintainedDateFrom, $maintainedDateTo) {
                return $query->whereBetween('maintenance_costs.maintained_date', [$maintainedDateFrom, $maintainedDateTo]);
            });

        $result = $query->groupBy('maintenance_costs.id')
            ->orderBy('plate_history.no_number_plate', $sortType)
            ->orderBy('maintenance_costs.scheduled_date', $sortType)
            ->orderBy($sortBy, $sortType)
            ->paginate($perPage);

        return $result;
    }

    public function updateMaintenanceCost($request, $id)
    {
        $attribute = $request->only(['charge_type', 'total_amount_excluding_tax', 'discount', 'total_amount_including_tax', 'note']);
        $result = $this->find($id);
        if ($attribute && count($attribute) > 0) {
            $result->update($attribute);
        }
        $accessories = $request->get('accessories');
        if ($accessories && count($accessories) > 0) {
            foreach ($accessories as $accessory) {
                $accessory['maintenance_cost_id'] = $result->id;
                if (Arr::get($accessory, 'id')) {
                    $maintenanceAccessory = MaintenanceAccessory::find(Arr::get($accessory, 'id'));
                    if ($maintenanceAccessory) {
                        $maintenanceAccessory->update($accessory);
                    }
                } else {
                    MaintenanceAccessory::create($accessory);
                }
            }
        }
        $accessories_delete = $request->get('accessories_delete');
        if ($accessories_delete && count($accessories_delete) > 0) {
            MaintenanceAccessory::whereIn(MaintenanceAccessory::ID, $accessories_delete)->delete();
        }
        $wages = $request->get('wages');
        if ($wages && count($wages) > 0) {
            foreach ($wages as $wage) {
                $wage['maintenance_cost_id'] = $result->id;
                if (Arr::get($wage, 'id')) {
                    $maintenanceWage = MaintenanceWage::find(Arr::get($wage, 'id'));
                    $maintenanceWage ? $maintenanceWage->update($wage) : null;
                } else {
                    MaintenanceWage::create($wage);
                }
            }
        }
        $wages_delete = $request->get('wages_delete');
        if ($wages_delete && count($wages_delete) > 0) {
            MaintenanceWage::whereIn(MaintenanceWage::ID, $wages_delete)->delete();
        }
        return $result;
    }

    public function maintenanceCost($request, $reCaculate = true) {
        if (isset($request->id)) {
            if ($check = $this->findMaintenanceCost($request->id, $request->vehicle_id)) {
                $cost = $check['cost'];
                $cost->charge_type = $request->charge_type;
                if (isset($request->maintained_date)) {
                    $current_mt_date = $cost->maintained_date;
                    if ($cost->type == 3)
                    $cost->maintained_date = $request->maintained_date . "-01";
                    else $cost->maintained_date = $request->maintained_date;
                }

                $cost->note = $request->note;
            }
        } else if ($request->type == TYPE_OTHER) {
            $current_mt_date = $request->maintained_date;
            $cost = $this->maintenanceCostRegister($request);
        }
        if ($cost) {
            $accessoriesNeedToSync = [];
            $accessoriesPriceTotal = 0;
            // $replacedAccessories = [];

            $maintainedAccessoriesIds = [];
            foreach ($request->maintenance_accessories as $key => $accessory) {
                $maintainedAccessoriesIds[] = $accessory['id'];
            }
            $listMaintainedAccessories = Accessory::whereIn('id', $maintainedAccessoriesIds)->withTrashed()->get(['*'])->keyBy('id');
            foreach ($request->maintenance_accessories as $key => $accessory) {
                $accessoriesNeedToSync[] = new MaintenanceAccessory([
                    'maintenance_cost_id' => $cost->id,
                    'accessory_id' => ($accessory['id'] != -1) ? $listMaintainedAccessories[$accessory['id']]->id  : $accessory['id'],
                    'name' => ($accessory['id'] != -1) ? $listMaintainedAccessories[$accessory['id']]->name : $accessory['name'],
                    'quantity' => $accessory['quantity'],
                    'price' => $accessory['price']
                ]);
                $accessoriesPriceTotal += $accessory['price'];
            }
            $wageNeedToSync = [];
            $wageTotal = 0;
            foreach ($request->wage as $key => $value) {
                $wageNeedToSync[] = new MaintenanceWage([
                    "work_content" => $value['work_content'],
                    "wages" => $value['wage'],
                    "maintenance_cost_id" => $cost->id
                ]);
                $wageTotal += $value['wage'];
            }

            if (isset($check) && $check['valid_edit_mileage'] || $cost->status == STATUS_NOT_INPUTTED) { //only first update can be edit the mileage
                $vehicle = Vehicle::find($request->vehicle_id);
                if ($cost->type == TYPE_THREE_MONTH || $cost->type == TYPE_TWELVE_MONTH) {
                    MileageHistory::updateOrCreate(
                        [
                            'date' => (isset($current_mt_date)) ? $current_mt_date : $request->maintained_date ." 00:00:00",
                            'vehicle_id' => $vehicle->id,
                        ],
                        [
                            'vehicle_id' => $vehicle->id,
                            'mileage' =>  $request->mileage_current,
                            'date' => (isset($request->maintained_date)) ? $request->maintained_date : $cost->maintained_date . " 00:00:00",
                        ]
                    );
                }
                $milage = $vehicle->mileage_history()->where('date', '<', $cost->maintained_date)->orderBy('date', 'DESC')->get();
                $cost->mileage_last_time = isset($milage[0]) ? $milage[0]->mileage : 0;
                $cost->mileage_current = $request->mileage_current;
            } else {
                if ($cost->maintained_date != null && $cost->type != TYPE_OTHER) {
                    $cost->maintained_date = $current_mt_date;
                }
            }

            $cost->total_amount_excluding_tax = $accessoriesPriceTotal + $wageTotal;
            $cost->total_amount_including_tax = ($cost->total_amount_excluding_tax - $request->discount) * 1.1;
            $cost->discount = $request->discount;
            $accessoriesStatus = $this->replacedAccessories($cost->vehicle_id, $cost->schedule_year, $cost->schedule_month, $maintainedAccessoriesIds, $cost->status);
            $cost->status = $accessoriesStatus['status'];
            if ((count($accessoriesNeedToSync) > 0 && $request->type == TYPE_OTHER) || ($cost->type == 1 || $cost->type == 2)) {
                $cost->status = STATUS_INPUTTED;
            }
            $cost->maintenance_accessories()->delete();
            $cost->maintenance_accessories()->saveMany($accessoriesNeedToSync);
            $cost->wage()->delete();
            $cost->wage()->saveMany($wageNeedToSync);
            $cost->created_by = isset($request->user()->id) ? $request->user()->id : null;
            $cost->updated_by = isset($request->user()->id) ? $request->user()->id : null;
            $cost->save();

            //add job calculate schedule
            if ($reCaculate == true) {
                RecalculateScheduleJob::dispatch($cost->vehicle_id);
            }
            return $cost;
        }
    }

    private function findMaintenanceCost(int $cost_id, int $vehicle_id) {
        $valid_edit_mileage = false;
        if ($cost = $this->model->where('id', $cost_id)->where('vehicle_id', $vehicle_id)->first()) {
            if ($checkValidEditMileage = $this->model->whereIn('type', [1, 2])
            ->where('maintained_date', '!=', null)->where('vehicle_id', $vehicle_id)
            ->orderBy('maintained_date', 'DESC')->first()) {
                $costScheduledDate =  Carbon::createFromFormat('Y-m-d', $cost->scheduled_date);
                $newestCostAt =  Carbon::createFromFormat('Y-m-d', $checkValidEditMileage->scheduled_date);
                if ($cost->id == $checkValidEditMileage->id) {
                    $valid_edit_mileage = true;
                } else if ($costScheduledDate->gt($newestCostAt)) $valid_edit_mileage = true;
            } else {
                $valid_edit_mileage = true;
            }
            return [
                "cost" => $cost,
                "valid_edit_mileage" => $valid_edit_mileage
            ];
        }
        return null;
    }

    public function MaintenanceCostDetail($id) {
        $cost = MaintenanceCost::where('id', $id)->first();
        $replacedAccessories = [];
        if ($cost) {
            $cost->maintenance_accessories;
            $cost->wage;
            if ($vehicle = Vehicle::find($cost->vehicle_id)) {
                if ($cost->maintained_date == null) {
                    $scheduled_date = $cost->scheduled_date;
                    if ($cost->type == 3) {
                        $scheduled_date = $cost->scheduled_date;
                    }
                    $milage = $vehicle->mileage_history()->where('date', '<=', $scheduled_date)->orderBy('date', 'DESC')->get();
                    $cost->mileage_last_time = (isset($milage[0]->mileage)) ? $milage[0]->mileage : 0;
                }
                if ($cost->maintained_date != null) {
                    $maintained_date = $cost->maintained_date;
                    if ($cost->type == 3) {
                        $maintained_date = $cost->maintained_date;
                    }
                    if ($milage_history = $vehicle->mileage_history()->where('date', '<', $maintained_date)->orderBy('date', 'DESC')->get())
                    $cost->mileage_last_time = (isset($milage_history[0]->mileage)) ? $milage_history[0]->mileage : 0;
                }
            }

            foreach ($cost->maintenance_accessories as $key => $value) {
                if ($value->accessory_id != null)
                $replacedAccessories[] = $value->accessory_id;
            }

            if ($cost->type == TYPE_ACCESSORY_CHANGE) {
                if ($cost->status == null) {
                    $cost->status = 1;
                    $cost->save();
                }
                $cost->replacement_accessories = $this->replacedAccessoriesAll($cost->vehicle_id, $cost->schedule_year, $cost->schedule_month);
            } else if (in_array($cost->type, [TYPE_THREE_MONTH, TYPE_TWELVE_MONTH])) {
                $chk = $this->findMaintenanceCost($cost->id, $cost->vehicle_id);
                if ($cost->status != STATUS_NOT_INPUTTED) {
                    $cost->valid_edit = $chk['valid_edit_mileage'];
                } else if ($cost->status == STATUS_NOT_INPUTTED) {
                    $cost->valid_edit = true;
                }
            }

            $accessoriesDataDeleted = Accessory::whereIn('id', $replacedAccessories)->withTrashed()->get(['*'])->keyBy('id');
            foreach ($cost->maintenance_accessories as $key => &$value) { // sao ngu the nay????????????
                if (isset($accessoriesDataDeleted[$value->accessory_id])) {
                    if ($accessoriesDataDeleted[$value->accessory_id] != null) {
                        $value['deleted_at'] = $accessoriesDataDeleted[$value->accessory_id]->deleted_at;
                    }
                }
            }

            $vehicle = $cost->vehicle->plate_history()->orderBy('date', 'DESC')->first();
            $cost->no_number_plate = $vehicle->no_number_plate;
            $cost->department_id =  $cost->vehicle->department_id;
            $cost->garage = null;
            if ($lease =  MaintenanceLease::orderBy('id', 'DESC')->first()) {
                $cost->garage = $lease->garage;
            }
            return $cost;
        }
        return [];
    }

    private function maintenanceCostRegister($request) {
        $cost = $this->model->create([
            'type' => TYPE_OTHER,
            'type_text' => LIST_TYPE[TYPE_OTHER],
            'charge_type' => $request->charge_type,
            'scheduled_date' => $request->maintained_date . "-01",
            'schedule_month' => date('m', strtotime($request->maintained_date)),
            'schedule_year' => date('Y', strtotime($request->maintained_date)),
            'maintained_date' => $request->maintained_date . "-01",
            'vehicle_id' => $request->vehicle_id,
            'no_number_plate' => $request->no_number_plate,
            'mileage_last_time' => $request->mileage_current,
            'mileage_current' => $request->mileage_current,
            'total_amount_excluding_tax' => $request->total_amount_excluding_tax,
            'discount' => $request->discount,
            'total_amount_including_tax' => $request->total_amount_including_tax,
            'note' => $request->note,
            'status' => 1,
            'created_by' => (isset($request->user()->id)) ? $request->user()->id : null
        ]);
        return $cost;
    }

    private function replacedAccessories(int $vehicle_id, int $year, int $month, array $replacedAccessories, int $status) {
        $monthSchedule = $year . "-" . $month . "-01";
        $schedule = Schedule::where('vehicle_id', $vehicle_id)
            ->whereBetween('month', [Carbon::parse($monthSchedule)->firstOfMonth(), Carbon::parse($monthSchedule)->endOfMonth()])
            ->get(['*']);

        $result = [];
        $scheduled_accessories = [];
        if ($schedule) {
            foreach ($schedule as $key => $value) {
                $scheduled_accessories = array_merge($scheduled_accessories, $value->list_accessory);
            }
            $scheduled_accessories = array_unique($scheduled_accessories);
            $accessoriesIds = [];
            foreach ($scheduled_accessories as $key => $value) {
                $accessoriesIds[] = $value;
            }
            $status = STATUS_IN_INPUTTING;
            $accessories = Accessory::whereIn('id', $accessoriesIds)->get(['*']);
            $count = 0;
            foreach ($accessories as $key => $accessory) {
                if (in_array($accessory->id, $replacedAccessories)) {
                    $result[] = [
                        "id" => $accessory->id,
                        "name" => $accessory->name,
                        "replaced" => true,
                    ];
                    $count += 1;
                    error_log("replaced:" . $accessory->id);
                } else {
                    // $status = STATUS_IN_INPUTTING;
                    $result[] = [
                        "id" => $accessory->id,
                        "name" => $accessory->name,
                        "replaced" => false,
                    ];
                }
            }
        }
        if ($count == count($accessories))  $status = STATUS_INPUTTED;
        return [
            "status" => $status,
            "result" => $result
        ];
    }

    private function replacedAccessoriesAll(int $vehicle_id, int $year, int $month)
    {
        $monthSchedule = $year . "-" . $month . "-01";
        $schedule = Schedule::where('vehicle_id', $vehicle_id)
            ->whereBetween('month', [Carbon::parse($monthSchedule)->firstOfMonth(), Carbon::parse($monthSchedule)->endOfMonth()])
            ->get(['*']);
        $result = [];
        $scheduled_accessories = [];
        if ($schedule) {
            foreach ($schedule as $key => $value) {
                $scheduled_accessories = array_merge($scheduled_accessories, $value->list_accessory);
            }
            $accessories = Accessory::whereIn('id', $scheduled_accessories)->get(['*']);
            foreach ($accessories as $key => $accessory) {
                $result[] = [
                    "id" => $accessory->id,
                    "name" => $accessory->name,
                    "replaced" => false,
                ];
            }
        }

        $maintenanceAsc = MaintenanceAccessory::leftJoin('maintenance_costs', 'maintenance_costs.id', '=', 'maintenance_accessories.maintenance_cost_id')
            ->whereBetween('maintenance_costs.maintained_date', [Carbon::parse($monthSchedule)->firstOfMonth(), Carbon::parse($monthSchedule)->endOfMonth()])
            ->where('maintenance_accessories.accessory_id', '>', 0)
            ->where('maintenance_costs.vehicle_id', $vehicle_id)
            ->pluck('maintenance_accessories.accessory_id', 'maintenance_accessories.accessory_id')->toArray();
        $accessories = Accessory::whereIn('id', array_values($maintenanceAsc))->withTrashed()->get();

        if ($accessories->count() > 0) {
            foreach ($accessories as $accessory) {
                $result[] = [
                    "id" => $accessory->id,
                    "name" => $accessory->name,
                    "replaced" => true,
                    "deleted_at" => $accessory->deleted_at,
                ];
            }
        }

        return $result;
    }

    public function export(string $date) {
        $lastDayYmd = date('Y-m-d', strtotime($date . "-1 day"));
        $lastDay = $lastDayYmd . " 00:00:00";
        $endDay = $lastDayYmd . " 23:59:59";
        $csvHeader = [[
            "match_by" => "", //edit, new,
            "cost_id",
            "vehicle_id",
            "plate",
            "department_id",
            "department_name",
            "scheduled_date",
            "maintained_date",
            "total_amount_excluding_tax",
            "discount",
            "total_amount_including_tax",
            "note",
            "type",
            "type_text",
            "status",
            "status_text",
            "created_at",
            "updated_at"
        ]];
        $maintenanceCost = $this->model
            ->where(function ($query) use ($lastDay, $endDay) {
                $query->whereBetween('created_at', [$lastDay, $endDay])
                    ->orWhereBetween('updated_at', [$lastDay, $endDay]);
            })->where('maintained_date', '!=', null)->get(['*'])->toArray();
        $fileName = "Maintenancecost.csv";
        $csvContent = [];
        foreach ($maintenanceCost as $key => $cost) {
            if ($cost['created_at'] == $cost['updated_at']) {
                $this->addContents($cost, $csvContent, "Create new");
            } else {
                $this->addContents($cost, $csvContent, "Update new");
            }
        }
        $result = array_merge($csvHeader, $csvContent);
        $fileName = "MaintenanceCost.csv";
        if(!Storage::exists($this->path)) {
            Storage::makeDirectory($this->path);
        }
        if (Excel::store(new MaintenanceCostExport($result),  $this->path . '/' . $fileName)) {
            $path = Storage::path($this->path . '/' . $fileName);
            return $this->zipFile($path);
        } //download(new MaintenanceCostExport($result), $fileName);
    }
    private function addContents($cost, &$contents, $data_type) {
        $vehicle = Vehicle::find($cost['vehicle_id']);
        $department = Department::find($vehicle->department_id);
        $contents[] = [
            $data_type,
            $cost['id'],
            $cost['vehicle_id'],
            $vehicle->plate_history()->orderBy('date', 'DESC')->first()->no_number_plate,
            $department->id,
            $department->name,
            $cost['scheduled_date'],
            $cost['maintained_date'],
            $cost['total_amount_excluding_tax'],
            $cost['discount'],
            $cost['total_amount_including_tax'],
            $cost['note'],
            $cost['type'],
            $cost['type_text'],
            $cost['status'],
            $cost['status_text'],
            $cost['created_at'],
            $cost['updated_at']
        ];
    }

    private function zipFile($file) {
        $zip_file = 'cost.zip';
        $zip = new ZipArchive();
        $zip->open(Storage::path($this->path . '/' . $zip_file), ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($file, "Maintenancecost.csv");
        $zip->close();
        return response()->download(Storage::path($this->path . '/' . $zip_file));
    }
}

     // if (($cost['type'] == 1 || $cost['type'] == 2) && $cost['maintained_date'] != null) {
            //     if ($cost['maintained_date'] == $lastDayYmd) {
            //         $this->addContents($cost, $csvContent, "maintained_date");
            //     } else {
            //         $updatedNotime = date('Y-m-d', strtotime($cost['updated_at']));
            //         if ($lastDayYmd == $updatedNotime) {
            //             $mt_date = Carbon::createFromFormat('Y-m-d', date("Y-m-d", strtotime($cost['maintained_date'])));
            //             $d = Carbon::createFromFormat('Y-m-d', $lastDayYmd);
            //             if ($mt_date->lt($d) || $mt_date->eq($d)) {
            //                 $this->addContents($cost, $csvContent, "updated_at");
            //             }
            //         }
            //     }
            // }
            // if ($cost['type'] == 3 && $cost['maintained_date'] != null) {
            //     $updatedNotime = date('Y-m-d', strtotime($cost['updated_at']));
            //     if ($lastDayYmd == $updatedNotime) {
            //         $this->addContents($cost, $csvContent, "updated_at");
            //     }
            // }
            // if ($cost['type'] == 4 && $cost['maintained_date'] != null) { // check type of cost = 4, it is register a new one.
            //     if ($cost['created_at'] == $cost['updated_at']) { // cause it is create => created_at == updated_at
            //         $updatedNotime = date('Y-m-d', strtotime($cost['created_at']));
            //         if ($lastDayYmd == $updatedNotime) {
            //             $this->addContents($cost, $csvContent, "created_at = updated_at"); // add a new row in to csv  => send at created day
            //         }
            //     } else {
            //         $updatedNotime = date('Y-m-d', strtotime($cost['updated_at']));
            //         if ($lastDayYmd == $updatedNotime) {
            //             $this->addContents($cost, $csvContent, "updated_at");
            //         }
            //     }
            // }
