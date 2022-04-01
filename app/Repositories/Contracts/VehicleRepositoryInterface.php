<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Repositories\Contracts;
use Illuminate\Database\Eloquent\Collection;

interface VehicleRepositoryInterface extends BaseRepositoryInterface
{
    //
    public function vehiclesDatas(int $year, int $department, int $vehicle_id = null):Collection;
    public function findByNumberOfPlate(string $numberOfPlate);
    public function vehiclePlates(int $department): Collection;
    public function listVehiclePlates($department);
}
