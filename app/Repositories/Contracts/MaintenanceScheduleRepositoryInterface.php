<?php
/**
 * Created by VeHo.
 * Year: 2022-01-04
 */

namespace App\Repositories\Contracts;


interface MaintenanceScheduleRepositoryInterface extends BaseRepositoryInterface
{
    public function finalSchedule(int $year, int $department, string $numberOfPlate = null):array;
    public function loadSchedule(int $year, int $department, string $numberOfPlate = null);
}
