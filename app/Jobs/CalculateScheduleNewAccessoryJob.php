<?php

namespace App\Jobs;

use App\Models\Accessory;
use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Repository\ScheduleServiceRepository;

class CalculateScheduleNewAccessoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $accessoryId;
    protected $repository;

    public function __construct($accessoryId)
    {
        $this->accessoryId = $accessoryId;
        $this->repository = new ScheduleServiceRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }
}
