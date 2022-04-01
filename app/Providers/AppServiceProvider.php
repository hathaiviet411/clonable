<?php

namespace App\Providers;


use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\MaintenanceCostRepositoryInterface;
use App\Repositories\Contracts\MaintenanceScheduleRepositoryInterface;
use App\Repositories\Contracts\AccessoriesRepositoryInterface;
use App\Repositories\Contracts\CloudRepositoryInterface;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\AccessoryScheduleRepositoryInterface;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use App\Repositories\Contracts\SystemConfigRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use Repository\BaseRepository;
use Repository\AuthRepository;

use Repository\RoleRepository;
use Repository\MaintenanceCostRepository;
use Repository\MaintenanceScheduleRepository;
use Repository\AccessoriesRepository;
use Repository\DepartmentRepository;
use Repository\VehicleRepository;
use Laravel\Dusk\DuskServiceProvider;
use Repository\CloudRepository;
use Repository\AccessoryScheduleRepository;
use Repository\ScheduleRepository;
use Repository\SystemConfigRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);

        //Customer
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
//        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(MaintenanceCostRepositoryInterface::class, MaintenanceCostRepository::class);
        $this->app->bind(MaintenanceScheduleRepositoryInterface::class, MaintenanceScheduleRepository::class);
        $this->app->bind(AccessoriesRepositoryInterface::class, AccessoriesRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(CloudRepositoryInterface::class, CloudRepository::class);
        $this->app->bind(AccessoryScheduleRepositoryInterface::class, AccessoryScheduleRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(SystemConfigRepositoryInterface::class, SystemConfigRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
