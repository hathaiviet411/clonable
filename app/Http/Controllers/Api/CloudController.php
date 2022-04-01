<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CloudRequest;
use App\Http\Resources\BaseResource;
use App\Models\ConnectionLog;
use Illuminate\Http\Request;
use App\Repositories\Contracts\CloudRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Cloud;

class CloudController extends Controller
{


    protected $repository;

    public function __construct(CloudRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    /**
     * @OA\Get(
     *   path="/api/maintenance-vehicles-data",
     *   tags={"Cloud"},
     *   summary="Api for cloud get maintenance and vehicles data",
     *   operationId="cloud-maintenance-vehicles-data",
     *   @OA\Response(
     *     response=200,
     *     description="Request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{}}
     *     )
     *   ),
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        return $this->responseJson(200, BaseResource::collection(["data"=>'Request success']));
    }


    /**
     * @OA\Post(
     *   path="/api/receive-vehicles-data",
     *   tags={"Cloud"},
     *   summary="Receive maintenance and vehicles data",
     *   operationId="cloud-receive-vehicles-data",
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"file","item_id"},
     *              @OA\Property(
     *                   description="Zip file upload",
     *                   property="file",
     *                   type="string",
     *                   format="binary",
     *               ),
     *              @OA\Property(
     *                   description="Item id",
     *                   property="item_id",
     *                   type="integer",
     *                   format="integer",
     *               ),
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Receive vehicles data success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{}}
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
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CloudRequest $request)
    {
        $log = ConnectionLog::create([
            'from' => "cloud",
            'call_to_api' => $request->fullUrl(),
            'status' => "api connected",
            'file_size' => null,
            'file_path' => null,
            'contents' => json_encode([
                "item_id" =>  $request->item_id
            ])
        ]);
        $now = Carbon::now();
        try {
            return $this->responseJson(200, $this->repository->unZip($request->file('file'),  $log, $request->item_id, $now));
        } catch (\Exception $e) {
            $log->status = "failed";
            $log->contents = $e;
            $log->save();
            throw $e;
        }
    }
}
