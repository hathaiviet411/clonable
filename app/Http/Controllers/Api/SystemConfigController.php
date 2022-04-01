<?php
/**
 * Created by VeHo.
 * Year: 2022-02-08
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemConfigRequest;
use App\Repositories\Contracts\SystemConfigRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\SystemConfigResource;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Http\Request;

class SystemConfigController extends Controller
{

    /**
     * var Repository
     */
    protected $repository;
    protected $vehicleRepository;

    public function __construct(SystemConfigRepositoryInterface $repository, VehicleRepositoryInterface $vehicleRepository)
    {
        $this->repository = $repository;
        $this->vehicleRepository = $vehicleRepository;
    }


    public function index(SystemConfigRequest $request)
    {
        $data = $this->repository->paginate($request->per_page);
        return $this->responseJson(200, BaseResource::collection($data));
    }


    /**
     * @OA\Get(
     *   path="/api/system-config/list-status-and-type",
     *   tags={"SystemConfig"},
     *   summary="List Maintenance status",
     *   operationId="system_config_status",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listStatusAndType(SystemConfigRequest $request)
    {
        return $this->responseJson(200, ['list_status' => LIST_STATUS, 'list_type' => LIST_TYPE]);
    }


    /**
     * @OA\Get(
     *   path="/api/system-config/list-garage",
     *   tags={"SystemConfig"},
     *   summary="List garage",
     *   operationId="system_config_garage",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="department_id",
     *     in="query",
     *     required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   ),
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listGarage(SystemConfigRequest $request)
    {
        $data = $this->repository->getListGarage($request->department_id);
        return $this->responseJson(200, $data);
    }

    /**
     * @OA\Get(
     *   path="/api/system-config/vehicle-plates",
     *   tags={"SystemConfig"},
     *   summary="List vehicle plates",
     *   operationId="system_config_vehicle_plates",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="department_id",
     *     in="query",
     *     required=true,
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
    public function vehicle(Request $request)
    {
        $data = $this->vehicleRepository->listVehiclePlates($request->department_id);
        return $this->responseJson(200, $data);
    }

    /**
     * @OA\Get(
     *   path="/api/system-config/year-conf",
     *   tags={"SystemConfig"},
     *   summary="List year config",
     *   operationId="system_config_year_conf",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function yearConf()
    {
        $data = $this->repository->yearConf();
        return $this->responseJson(200, new BaseResource($data));
    }
}
