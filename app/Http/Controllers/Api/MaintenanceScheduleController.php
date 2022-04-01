<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceScheduleRequest;
use App\Repositories\Contracts\MaintenanceScheduleRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\MaintenanceScheduleResource;
use App\Http\Resources\VehiclePlateResource;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class MaintenanceScheduleController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;
    protected $vehicleRepository;
    public function __construct(MaintenanceScheduleRepositoryInterface $repository, VehicleRepositoryInterface $vehicleRepository)
    {
        $this->middleware(['role_or_permission:' . ROLE_HEADQUARTER . '|' . ROLE_OPERATOR . '|' . ROLE_TEAM]);
        $this->repository = $repository;
        $this->vehicleRepository = $vehicleRepository;
    }
    /**
     * @OA\Get(
     *   path="/api/maintenance-schedule",
     *   tags={"MaintenanceSchedule"},
     *   summary="List MaintenanceSchedule",
     *   operationId="maintenance_schedule_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code": 200,"data": {{"id":1,"date": "2021-12-29","number_of_plate": "14Z1-4896","accessories" : {{"accessor_id": 1,"accessor_name": "AAAA","is_other": "false"},{"accessor_id": 2,"accessor_name": "BBBB","is_other": "true"}}},{"id":1,"date": "2021-12-30","number_of_plate": "14Z1-99999","accessories" : {{"accessor_id": 1,"accessor_name": "AAAA","is_other": "false"},{"accessor_id": 2,"accessor_name": "BBBB","is_other": "true"}}}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(
     *     name="plate",
     *     in="query",
     *     @OA\Schema(
     *      type="string",
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="from",
     *     in="query",
     *     @OA\Schema(
     *      type="string",
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="to",
     *     in="query",
     *     @OA\Schema(
     *      type="string",
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Login false",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Username or password invalid"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $plate = Arr::get($request->all(), 'plate', null);
        return $this->responseJson(200, new MaintenanceScheduleResource([
            "data" => $this->repository->loadSchedule($request->year, $request->department, $plate)
        ]));
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, $options);
    }

    public function vehicle(Request $request) {
        $data = $this->vehicleRepository->paginate($request->per_page);
        return $this->responseJson(200, VehiclePlateResource::collection($data));
    }
}
