<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccessoriesRequest;
use App\Jobs\CalculateScheduleNewAccessoryJob;
use App\Jobs\RecalculateScheduleJob;
use App\Models\Accessory;
use App\Models\MaintenanceAccessory;
use App\Models\Vehicle;
use App\Repositories\Contracts\AccessoriesRepositoryInterface;
use App\Http\Resources\BaseResource;
use App\Http\Resources\AccessoriesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class AccessoriesController extends Controller
{

    /**
     * var Repository
     */
    protected $repository;

    public function __construct(AccessoriesRepositoryInterface $repository)
    {
        $this->middleware(['role_or_permission:' . ROLE_HEADQUARTER . '|' . ROLE_OPERATOR . '|' . ROLE_TEAM]);
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *   path="/api/accessories",
     *   tags={"Accessories"},
     *   summary="List Accessories",
     *   operationId="accessorie_index",
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{{"id": 1,"name": "V12 Twin Turbo","tonnage": 200,"mileage": 2000, "passed_year":4},{"id": 2,"name": "V8 Twin Turbo","tonnage": 200,"mileage": 2000, "passed_year":4}}}
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
    public function index(AccessoriesRequest $request)
    {
        $page = Arr::get($request, 'page', null);
        $sortBy = Arr::get($request, 'sortby', null);
        $sortType = Arr::get($request, 'sorttype', null);
        $accessoryName = Arr::get($request, 'name', null);
        $data = $this->repository->getPaginate($accessoryName, $sortBy, $sortType);
        $paginage = $this->paginate($data, $request->per_page, $page);
        return $this->responseJson(200, BaseResource::collection($paginage));
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, $options);
    }

    /**
     * @OA\Post(
     *   path="/api/accessories",
     *   tags={"Accessories"},
     *   summary="Add new Accessories",
     *   operationId="accessorie_create",
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *          mediaType="application/json",
     *          example={"name": "V12 Twin Turbo","tonnage": 200,"mileage": 2000, "passed_year": 4, "remark": "Troi oi remark"},
     *          @OA\Schema(
     *            required={"name"},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *            required={"tonnage"},
     *            @OA\Property(
     *              property="tonnage",
     *              format="integer",
     *            ),
     *            required={"mileage"},
     *            @OA\Property(
     *              property="mileage",
     *              format="integer",
     *            ),
     *            required={"remark"},
     *            @OA\Property(
     *              property="remark",
     *              format="string",
     *            )
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"message": "i18n"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(AccessoriesRequest $request)
    {
        try {
            $attributes = $request->all();
            $attributes['created_by'] = $request->user()->id;
            $attributes['updated_by'] = $request->user()->id;

            //validate name and tonage
            $checkAccessory = Accessory::where('name', $request->get('name'))->where('tonnage', $request->get('tonnage'))->first();
            if ($checkAccessory) {
                throw ValidationException::withMessages([trans('accessory_name_of_tonnage_is_exist')]);
            }

            if ($data = $this->repository->create($attributes)) {
                $this->callRecalculation($data->tonnage);
                return $this->responseJson(200, null, trans('messages.mes.create_success'));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *   path="/api/accessories/{id}",
     *   tags={"Accessories"},
     *   summary="Detail Accessories",
     *   operationId="accessorie_show",
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
     *      example={"code":200,"data":{"id": 1,"name": "V12 Twin Turbo","tonnage": 200,"mileage": 2000, "passed_year":4, "remark":"Troi oi remark"}}
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
            $department = $this->repository->find($id);
            return $this->responseJson(200, new BaseResource($department));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @OA\Put(
     *   path="/api/accessories/{id}",
     *   tags={"Accessories"},
     *   summary="Update Accessories",
     *   operationId="accessorie_update",
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
     *          example={"name": "V12 Twin Turbo","tonnage": 200,"mileage": 2000,"passed_year":4, "remark":"Troi oi remark"},
     *          @OA\Schema(
     *            required={"name"},
     *            @OA\Property(
     *              property="name",
     *              format="string",
     *            ),
     *            required={"tonnage"},
     *            @OA\Property(
     *              property="tonnage",
     *              format="integer",
     *            ),
     *            required={"mileage"},
     *            @OA\Property(
     *              property="mileage",
     *              format="integer",
     *            ),
     *            required={"remark"},
     *            @OA\Property(
     *              property="remark",
     *              format="string",
     *            )
     *         )
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"message":"i18n"}
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
    public function update(AccessoriesRequest $request, $id)
    {
        $attributes = $request->except([]);
        $attributes['updated_by'] = $request->user()->id;

        //validate name and tonage
        $msgValidation = [];
        $checkAccessory = Accessory::where('name', $request->get('name'))
            ->where('tonnage', $request->get('tonnage'))
            ->where('id', '<>', $id)
            ->first();
        if ($checkAccessory) {
            $msgValidation[] = trans('accessory_name_of_tonnage_is_exist');
        }

        $accessory = Accessory::find($id);
        if ($request->has('tonnage')) {
            if ($accessory && $accessory->tonnage !== $request->get('tonnage')) {
                $mtAccessoryCk = MaintenanceAccessory::select('maintenance_accessories.accessory_id')
                    ->leftJoin('maintenance_costs', 'maintenance_costs.id', '=', 'maintenance_accessories.maintenance_cost_id')
                    ->where('maintenance_accessories.accessory_id', $id)->first();
                if ($mtAccessoryCk) {
                    $msgValidation[] = trans('not_change_tonnage_accessory_referenced_to_maintenance_cost');
                }
            }
        }

        $listOilElement = Accessory::whereIn('name', ['エンジンオイル', 'オイルエレメント'])->where('passed_year', 0)->pluck('id', 'id')->toArray();
        if (count($listOilElement) > 0 && $request->has('name') && in_array($id, array_values($listOilElement))) {
            if (!in_array($request->get('name'), ['エンジンオイル', 'オイルエレメント'])) {
                $msgValidation[] = 'name_accessory_not_change';
            }
        }

        if (count($msgValidation) > 0) {
            throw ValidationException::withMessages($msgValidation);
        }

        if ($this->repository->update($attributes, $id)) {
            $this->callRecalculation($accessory->tonnage);
            return $this->responseJson(CODE_SUCCESS, null, trans('messages.mes.update_success'));
        } else {
            return $this->responseJson(CODE_CREATE_FAILED, null, trans('messages.mes.update_error'));
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/accessories/{id}",
     *   tags={"Accessories"},
     *   summary="Delete Accessories",
     *   operationId="accessorie_delete",
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *     @OA\Schema(
     *      type="string",
     *     ),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Send request success",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"message":"i18n"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $accessory = Accessory::find($id);
        $this->repository->delete($id);
        if ($accessory) {
            $this->callRecalculation($accessory->tonnage);
        }
        return $this->responseJson(200, null, trans('messages.mes.delete_success'));
    }

    public function callRecalculation($tonnage)
    {
        $vehicles = Vehicle::select('id', 'inspection_expiration_date', 'first_registration', 'truck_classification_number', 'department_id')
            ->where('truck_classification_number', '>=', 3)->get();
        if ($vehicles->count() > 1) {
            foreach ($vehicles as $vehicle) {
                $vhcTonnage = ($vehicle->truck_classification_number <= 4) ? $vehicle->truck_classification_number : 4;
                if ($vhcTonnage !== $tonnage) {
                    continue;
                }
                RecalculateScheduleJob::dispatch($vehicle->id);
            }
        }
    }
}
