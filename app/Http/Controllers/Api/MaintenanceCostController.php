<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaintenanceCostRequest;
use App\Repositories\Contracts\MaintenanceCostRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\FindVehicleResource;
use App\Http\Resources\MaintenanceCostResource;
use App\Repositories\Contracts\AccessoriesRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaintenanceCostExport;
class MaintenanceCostController extends Controller
{

    /**
     * var Repository
     */
    protected $repository;
    protected $vehicleRepository;
    protected $accessoriesRepository;
    public function __construct(MaintenanceCostRepositoryInterface $repository, VehicleRepositoryInterface $vehicleRepository, AccessoriesRepositoryInterface $accessoriesRepository)
    {
        $this->repository = $repository;
        $this->vehicleRepository = $vehicleRepository;
        $this->accessoriesRepository = $accessoriesRepository;
    }

    /**
     * @OA\Get(
     *   path="/api/maintenance-cost",
     *   tags={"MaintenanceCost"},
     *   summary="List MaintenanceCost",
     *   operationId="maintenance_cost_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data": {{"id" : 1, "number_plate": "NO-00110011", "maintenance_date": "2021-11-21","garage_id" : 1,"garage_name" : "A"},{"id" : 2, "number_plate": "NO-00110011", "maintenance_date": "2021-11-22","garage_id" : 1,"garage_name" : "A"},{"id" : 3, "number_plate": "NO-00110011", "maintenance_date": "2021-11-23","garage_id" : 1,"garage_name" : "A"}}}
     *     )
     *   ),
     *   @OA\Parameter(name="department_id", in="query", required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter( name="year_month", in="query", required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter( name="vehicle_id", in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(name="garage", in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter( name="type", in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter( name="status", in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(name="scheduled_date_from", in="query",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(name="scheduled_date_to", in="query",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(name="maintained_date_from", in="query",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(name="maintained_date_to", in="query",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(name="sortby", in="query",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(name="sorttype", in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
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
    public function index(MaintenanceCostRequest $request)
    {
        $scheduledDate = ['from' => $request->get('scheduled_date_from'), 'to' => $request->get('scheduled_date_to')];
        $maintainedDate = ['from' => $request->get('maintained_date_from'), 'to' => $request->get('maintained_date_to')];
        $data = $this->repository->maintenanceCodePaginate(
            $request->get('department_id'),
            $request->get('year'),
            $request->get('vehicle_id'),
            $request->get('garage'),
            $request->get('type'),
            $request->get('status'),
            $scheduledDate,
            $maintainedDate,
            $request->get('sortby'),
            $request->get('sorttype'),
            $request->get('per_page')
        );
        return $this->responseJson(200, BaseResource::collection($data));
    }

    /**
     * @OA\Post(
     *   path="/api/maintenance-cost",
     *   tags={"MaintenanceCost"},
     *   summary="Add new MaintenanceCost",
     *   operationId="maintenance_cost_create",
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="application/json",
     *          example={ "type": "1. 3 moth | 2. 12 moth | 3.  accessory change | 4. other",  "charge_type": "1:external | 2: Internal", "scheduled_date": "2022-01-21", "maintained_date" : "2022-01-25",  "vehicle_id": 1,"total_amount_excluding_tax" : 50000, "mileage_current" : "400000", "discount": 5000, "total_amount_including_tax": 5000,
     *                "accessories":{{"accessory_id": 1, "quantity": 1, "price": 10000},{"accessory_id": 2, "quantity": 2, "price": 20000}},
     *                "wages": {{"work_content": "work content 1"},{"work_content": "work content 2"}},
     *                "note": "Note content",
     *          },
     *        @OA\Schema(
     *            required={},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *         )
     *      )
     *   ),
     *
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name": "......"}}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(MaintenanceCostRequest $request)
    {
        try {

            // int $id, int $vehicle_id, int $charge_type, int $maintained_date, string $note, array $accessories
            $data = $this->repository->maintenanceCost($request);
            return $this->responseJson(200, new MaintenanceCostResource($data));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *   path="/api/maintenance-cost/{id}",
     *   tags={"MaintenanceCost"},
     *   summary="Detail MaintenanceCost",
     *   operationId="maintenance_cost_show",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code": 200,"data" : {"maintenance_date" : "2021-12-11","garage_id": 1,"garage_name": "A","number_plate": "NO-00110011","team_id": 1,"team_name" : "AA","accessories" : {{"accessor_id":1 ,"name": "AAAAAA","price": null,"wage": null},{"accessor_id":2, "name": "BBBBBBB","price": 10000,"wage": null}},"discount": 1000}}
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
    public function show($id)
    {
        try {
            $cost = $this->repository->MaintenanceCostDetail($id);
            return $this->responseJson(200, new BaseResource($cost));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Put(
     *   path="/api/maintenance-cost/{id}",
     *   tags={"MaintenanceCost"},
     *   summary="Update MaintenanceCost",
     *   operationId="maintenance_cost_update",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="application/json",
     *          example={"charge_type": "1:external | 2: Internal", "total_amount_excluding_tax": 50000,"discount": 5000, "total_amount_including_tax": 5000,
     *                "accessories":{{"id":1, "accessory_id": 1, "quantity": 1, "price": 10000},
     *                               {"id":null, "accessory_id": 2, "quantity": 2, "price": 20000}},
     *                "accessories_delete":{1,2,3,4},
     *                "wages": {{"id":1, "work_content": "work content 1"},
     *                          {"id":null, "work_content": "work content 2"}},
     *                "wages_delete":{1,2,3,4},
     *                "note": "Note content",
     *          },
     *        @OA\Schema(
     *            required={},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1,"name":  "............."}}
     *     ),
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Access Deny permission",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":403,"message":"Access Deny permission"}
     *     ),
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MaintenanceCostRequest $request, $id)
    {
        $data = $this->repository->updateMaintenanceCost($request, $id);
        return $this->responseJson(200, new BaseResource($data));
    }

    public function findVehicleInformation($plate) {
        $vehilce = $this->vehicleRepository->findByNumberOfPlate($plate);
        return $this->responseJson(200, new FindVehicleResource($vehilce));
    }

    public function accessoryPullDown(Request $request) {
        if ($request->vehicle_id) {
            $data = $this->accessoriesRepository->accessoriesPullDown($request->vehicle_id);
        } else $data = $this->accessoriesRepository->accessoriesPullDown();
        return $this->responseJson(200, BaseResource::collection($data));
    }

/**
     * @OA\Get(
     *   path="/api/maintenance-cost/export/{Y-m-d}",
     *   tags={"MaintenanceCost"},
     *   summary="Detail MaintenanceCost",
     *   operationId="maintenance_cost_export",
     *   @OA\Parameter(
     *     name="Y-m-d",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Login false",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Username or password invalid"}
     *     )
     *   )
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function export($date) {
        return $this->repository->export($date);
    }
}
