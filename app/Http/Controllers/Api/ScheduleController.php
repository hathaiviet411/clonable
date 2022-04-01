<?php
/**
 * Created by VeHo.
 * Year: 2022-01-28
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleRequest;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\MaintenanceCostVehicleResource;
use App\Http\Resources\ScheduleResource;
use App\Repositories\Contracts\AccessoryScheduleRepositoryInterface;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;
    protected $acScheduleRepository;
    public function __construct(ScheduleRepositoryInterface $repository, AccessoryScheduleRepositoryInterface $acScheduleRepository)
    {
        $this->repository = $repository;
        $this->acScheduleRepository = $acScheduleRepository;
    }

    public function maintenanceCostVehicle($id) {
        // return $this->repository->vehicleCostDetail($id);
        return $this->responseJson(200, new MaintenanceCostVehicleResource($this->repository->vehicleCostDetail($id)));
    }

    public function scheduleAccessoriesVehicle($id, $year) {
        return $this->responseJson(200,
                $this->repository->vehicleCostScheduleAcessories($id, $year)
        );
    }

    public function scheduleAccessory(Request $request) {
        return $this->responseJson(200, [
            $this->acScheduleRepository->scheduleVehicleAccessories($request->year, $request->vehicle_id, $request->department_id)
        ]);
    }

    public function scheduleAccessoryEdit(Request $request, $id) {
        return $this->responseJson(200, [
            $this->repository->vechileScheduleCostEdit($id, $request)
        ]);
    }
}
