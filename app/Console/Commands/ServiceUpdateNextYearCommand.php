<?php

namespace App\Console\Commands;

use App\Jobs\CalculateScheduleJob;
use App\Jobs\RecalculateScheduleJob;
use App\Models\Accessory;
use App\Models\Schedule;
use App\Models\SystemConfig;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ServiceUpdateNextYearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:update_next_year';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $repository;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $yearConfig = SystemConfig::where('sys_param', 'next_year')->first();
        $nextNewYear = Carbon::create(Carbon::now()->year)->addYears(6)->year;
        if ((int)$yearConfig->sys_value !== $nextNewYear) {
            $yearConfig->sys_value = $nextNewYear;
            $yearConfig->save();
            $vehicles = Vehicle::select('id')->get();
            foreach ($vehicles as $vehicle) {
                CalculateScheduleJob::dispatch($vehicle->id);
                RecalculateScheduleJob::dispatch($vehicle->id);
            }
        }
    }
}
