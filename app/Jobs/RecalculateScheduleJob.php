<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Repository\ScheduleServiceRepository;

class RecalculateScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $vehicleId;
    private $startDate;
    private $endDate;
    protected $repository;

    public function __construct($vehicleId, $startDate = null, $endDate = null)
    {
        $this->vehicleId = $vehicleId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->repository = new ScheduleServiceRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->repository->RecalculateScheduledAccessory($this->vehicleId, $this->startDate, $this->endDate);
    }
}
