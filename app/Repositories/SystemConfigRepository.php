<?php
/**
 * Created by VeHo.
 * Year: 2022-02-08
 */

namespace Repository;

use App\Models\MaintenanceLease;
use App\Models\SystemConfig;
use App\Repositories\Contracts\SystemConfigRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class SystemConfigRepository extends BaseRepository implements SystemConfigRepositoryInterface
{

    public function __construct(Application $app)
    {
        parent::__construct($app);

    }

    /**
     * Instantiate model
     *
     * @param SystemConfig $model
     */

    public function model()
    {
        return SystemConfig::class;
    }

    public function getListGarage($departmentId)
    {
        return MaintenanceLease::select('maintenance_leases.garage', 'maintenance_leases.id')
            ->leftJoin('vehicles', 'vehicles.id', '=', 'maintenance_leases.vehicle_id')
            ->where('vehicles.department_id', $departmentId)
            ->whereNotNull('maintenance_leases.garage')
            ->groupBy('maintenance_leases.garage')
            ->pluck('maintenance_leases.garage')->toArray();
    }

    public function yearConf()
    {
        $startYear = SystemConfig::where('sys_param', 'start_year')->first();
        $startYearAccounting = Carbon::create((int)$startYear->sys_value, 4)->subYear();
        $nextYear = SystemConfig::where('sys_param', 'next_year')->first();
        $nextYearAccounting = Carbon::create((int)$nextYear->sys_value, 3)->addYear()->lastOfMonth();

        $result = CarbonPeriod::create($startYearAccounting, '1 month', $nextYearAccounting);

        $listMonthYear = null;
        foreach ($result as $dt) {
            $listMonthYear[] = $dt->format("Y-m");
        }

        $listYear = null;
        $resultYears = CarbonPeriod::create($startYearAccounting, '1 year', $nextYearAccounting);
        foreach ($resultYears as $resultYear) {
            $listYear[] = $resultYear->year;
        }

        return ['listYear' => $listYear, 'listYearMonth' => $listMonthYear];
    }

}
