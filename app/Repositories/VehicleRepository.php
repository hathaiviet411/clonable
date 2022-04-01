<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace Repository;

use App\Models\PlateHistory;
use App\Models\Vehicle;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class VehicleRepository extends BaseRepository implements VehicleRepositoryInterface
{

    public function __construct(Application $app)
    {
        parent::__construct($app);

    }

    /**
     * Instantiate model
     *
     * @param Vehicle $model
     */

    public function model()
    {
        return Vehicle::class;
    }

    public function vehiclesDatas(int $year, int $department, int $vehicle_id = null): Collection
    {
        $datas = $this->model->where('department_id', $department)->
        with([
            'plate_history' => function ($query) use ($year) {
                $query->select(['*'])->orderBy('date', 'DESC');
            },
            'mileage_history' => function ($query) use ($year) {
                $query->select(['*'])->orderBy('mileage', 'DESC');
            }
        ]);

        if ($vehicle_id != null) {
            return $datas->where('id', $vehicle_id)->get(['*']);
        }
        return $datas->orderBy('id', 'asc')->get(['*']);
    }

    public function findByNumberOfPlate(string $numberOfPlate = null)
    {
        if (!$numberOfPlate) return null;
        $vehicle = $this->model->whereHas('plate_history', function ($query) use ($numberOfPlate) {
            $query->where('no_number_plate', $numberOfPlate);
        })->first();
        return $vehicle;
    }

    public function vehiclePlates(int $department): Collection
    {
        $datas = $this->model->where('department_id', $department)->
        with([
            'plate_history' => function ($query) {
                $query->select(['*'])->orderBy('date', 'DESC');
            }
        ]);
        return $datas->orderBy('id', 'asc')->get(['id']);
    }

    public function listVehiclePlates($department)
    {
        $latestPlate = DB::table('vehicle_no_number_plate_history as latestPlate')
            ->select('latestPlate.vehicle_id', DB::raw('MAX(latestPlate.date) as last_date'))->groupBy('latestPlate.vehicle_id');
        $plateHistory = PlateHistory::select('vehicle_no_number_plate_history.id', 'vehicle_no_number_plate_history.no_number_plate', 'vehicle_no_number_plate_history.vehicle_id')
            ->joinSub($latestPlate, 'latestPlate', function ($join) {
                $join->on('latestPlate.vehicle_id', '=', 'vehicle_no_number_plate_history.vehicle_id')
                    ->on('latestPlate.last_date', '=', 'vehicle_no_number_plate_history.date');
            });
        $datas = Vehicle::select('vehicles.id', 'plate_history.no_number_plate')->where('department_id', $department)
            ->leftJoinSub($plateHistory, 'plate_history', function ($join) {
                $join->on('plate_history.vehicle_id', '=', 'vehicles.id');
            });
        return $datas->pluck('plate_history.no_number_plate', 'vehicles.id');
    }
}
