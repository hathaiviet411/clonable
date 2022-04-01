<?php
/**
 * Created by VeHo.
 * Year: 2022-01-26
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessoryScheduleRequest;
use App\Repositories\Contracts\AccessoryScheduleRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\AccessoryScheduleResource;
use Illuminate\Http\Request;

class AccessoryScheduleController extends Controller
{

    /**
     * var Repository
     */
    protected $repository;

    public function __construct(AccessoryScheduleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/accessory-schedule",
     *   tags={"AccessorySchedule"},
     *   summary="List AccessorySchedule",
     *   operationId="accessory_schedule_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "..........."}}}
     *     )
     *   ),
     *   @OA\Parameter(name="year", in="query", required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter( name="department_id", in="query", required=true,
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(name="vehicle_id", in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(name="accessory", in="query",
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Parameter(name="page", in="query",
     *     @OA\Schema(
     *      type="integer",
     *     ),
     *   ),
     *   @OA\Parameter(name="per_page", in="query",
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
    public function index(AccessoryScheduleRequest $request)
    {
        $data = $this->repository->index($request);
        return $this->responseJson(200, BaseResource::collection($data));
    }
}
