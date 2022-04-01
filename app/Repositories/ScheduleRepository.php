<?php
/**
 * Created by VeHo.
 * Year: 2022-01-28
 */

namespace Repository;

use App\Models\Accessory;
use App\Models\MaintenanceCost;
use App\Models\Schedule;
use App\Models\MaintenanceLease;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use Illuminate\Foundation\Application;

class ScheduleRepository extends BaseRepository implements ScheduleRepositoryInterface
{

    private $vehicleRepository;
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->vehicleRepository = new VehicleRepository($app);
    }

    /**
     * Instantiate model
     *
     * @param Schedule $model
     */

    public function model()
    {
        return Schedule::class;
    }

    public function vehicleCostDetail(int $vehicle_id) {
        $vehicle = $this->vehicleRepository->find($vehicle_id);
        $vehicleLease = MaintenanceLease::where('vehicle_id', $vehicle_id)->orderBy('id', 'DESC')->first();
        $vehicle->vehicle_lease = $vehicleLease;
        if (isset($vehicle->vehicle_data[0])) {
            $vehicle->vehicle_data = $vehicle->vehicle_data[0];
        }
        $vehicle->vehicle_data->battery = $vehicle->battery_size;
        // $vehicle->vehicle_data->battery = null;
        // $vehicle->vehicle_data->battery_date = null;

        // $vehicle->vehicle_data->starter_motor = null;
        // $vehicle->vehicle_data->starter_motor_date = null;

        // $vehicle->vehicle_data->alternator = null;
        // $vehicle->vehicle_data->alternator_date = null;
        $truck_classification = $vehicle->truck_classification_number;
        $costList = MaintenanceCost::whereHas('maintenance_accessories', function($query) use($vehicle_id){
            $query->where('vehicle_id', $vehicle_id)->whereIn('accessory_id', LIST_ID_ACCESSORY);
        })->with([
            'maintenance_accessories' => function ($query) {
                $query->whereIn('accessory_id', LIST_ID_ACCESSORY)->select(['*']);
            }
        ])->orderBy('maintained_date', 'DESC')->get(); //get list maintenance_accessories by cost
        // order by newest date.
        if ($costList) {
            foreach ($costList as $key => $cost) {
                foreach ($cost->maintenance_accessories as $key => $value) {
                    if (in_array($value->accessory_id, LIST_ID_ACCESSORY)) { // check is batter, starter_motor, alternator
                        $accessory = Accessory::find($value->accessory_id);
                        if ($accessory) {
                            if ($accessory->tonnage == $truck_classification) {
                                // check type(truck_classification_number of vehicle) is same as tonage of accessory
                                switch (LIST_KEY_ACCESSORY[$value->accessory_id]) { // check key of each accessories in object.
                                    case 'battery':
                                        // $vehicle->vehicle_data->battery = $vehicle->battery_size;
                                        $vehicle->vehicle_data->battery_date = $cost->maintained_date;
                                        break;
                                    case 'starter_motor':
                                        $vehicle->vehicle_data->starter_motor = (isset($vehicle->vehicle_data->starter_motor)) ?  $vehicle->vehicle_data->starter_motor : null;
                                        $vehicle->vehicle_data->starter_motor_date = (isset($vehicle->vehicle_data->starter_motor_date)) ? $vehicle->vehicle_data->starter_motor_date : $cost->maintained_date;
                                        break;
                                    case 'alternator':
                                        $vehicle->vehicle_data->alternator = (isset($vehicle->vehicle_data->alternator)) ? $vehicle->vehicle_data->alternator : null;
                                        $vehicle->vehicle_data->alternator_date = (isset($vehicle->vehicle_data->alternator_date)) ? $vehicle->vehicle_data->alternator_date : $cost->maintained_date;
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                    }
            }
        }
        }
        return $vehicle;
    }

    public function vehicleCostScheduleAcessories(int $vehicle_id, int $year) {
        $schedule = $this->model->where('vehicle_id', $vehicle_id)
        ->whereYear('month', '>=', $year - 1)
        ->whereYear('month', '<=', $year + 2)
        ->orderBy('month','ASC')
        ->get(['*']);
        return $schedule;
    }

    public function vechileScheduleCostEdit(int $vehicle_id, $request) {
        $vehicle = $this->vehicleRepository->find($vehicle_id);
        if (count($vehicle->vehicle_data) > 0) {
            $vehicle->vehicle_data()->update([
                'tire_replacement_date' => $request->tire_replacement_date,
                'starter_motor' => $request->starter_motor,
                'starter_motor_date' => $request->starter_date,
                'alternator' => $request->alternator,
                'alternator_date' => $request->alternator_date,
                'glass' => $request->glass,
                'glass_date' => $request->glass_date,
                'body_id' => $request->body_id,
                'body_id_date' => $request->body_id_date,
                'camera_monitor' => $request->camera_monitor,
                'camera_monitor_date' => $request->camera_monitor_date,
                'gate' => $request->gate,
                'gate_date' => $request->gate_date,
                'other' => $request->other,
                'other_date' => $request->other_date,
                'remark_01' => $request->remark_01,
                'remark_02' => $request->remark_02,
                'created_by' => 1,
                'updated_by' => 1
            ]);
        } else {
            $vehicle->vehicle_data()->create([
                'vehicle_id' => $vehicle_id,
                'tire_replacement_date' => $request->tire_replacement_date,
                'starter_motor' => $request->starter_motor,
                'starter_motor_date' => $request->starter_date,
                'alternator' => $request->alternator,
                'alternator_date' => $request->alternator_date,
                'glass' => $request->glass,
                'glass_date' => $request->glass_date,
                'body_id' => $request->body_id,
                'body_id_date' => $request->body_id_date,
                'camera_monitor' => $request->camera_monitor,
                'camera_monitor_date' => $request->camera_monitor_date,
                'gate' => $request->gate,
                'gate_date' => $request->gate_date,
                'other' => $request->other,
                'other_date' => $request->other_date,
                'remark_01' => $request->remark_01,
                'remark_02' => $request->remark_02,
                'created_by' => 1,
                'updated_by' => 1
            ]);
        }
        $vehicle = $this->vehicleRepository->find($vehicle_id);
        return $vehicle->vehicle_data;
    }
}
