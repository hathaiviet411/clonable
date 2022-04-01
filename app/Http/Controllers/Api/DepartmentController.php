<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

     /**
     * var Repository
     */
    protected $repository;

    public function __construct(DepartmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/department",
     *   tags={"Department"},
     *   summary="List Department",
     *   operationId="department_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "Tokyo"}, {"id": 2,"name": "Kyoto"}, {"id": 3,"name": "Bodoimethienha"}}}
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
     *   security={},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DepartmentRequest $request)
    {
        $data = $this->repository->getAll();
        return $this->responseJson(200, new BaseResource($data));
    }

    public function show($id)
    {
        try {
            $department = $this->repository->find($id);
            return $this->responseJson(200, new BaseResource($department));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
