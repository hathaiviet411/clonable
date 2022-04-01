<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace Repository;

use App\Models\Accessory;
use App\Models\Vehicle;
use App\Repositories\Contracts\AccessoriesRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Psy\CodeCleaner\FunctionContextPass;
use Repository\VehicleRepository;
class AccessoriesRepository extends BaseRepository implements AccessoriesRepositoryInterface
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
       * @param Accessory $model
       */

    public function model()
    {
        return Accessory::class;
    }

    public function accessoriesPullDown($vehicle_id = null) {
        $tonnage = null;
        if ($vehicle_id) {
            if ($vehicle = $this->vehicleRepository->find($vehicle_id))
            {
                $tonnage = $vehicle->truck_classification_number;
            }
        }
        if ($tonnage != null) {
            return $this->model->where('tonnage', $tonnage)->get(['*']);
        }
        return $this->model->get(['*']);
    }

    public function getPaginate($accessoryName = null, $sortBy = null, $sortType = 0)
    {
        $type = [
            "DESC",
            "ASC"
        ];

        $query = $this->model;
        if ($accessoryName != null) {
            $query = $query->where('name', 'like', '%'. $accessoryName . '%');
        }

        if ($sortBy != null) {
            $query = $query->orderBy($sortBy, $type[$sortType]);
        }

        return $query->get(['*']);
    }
}
