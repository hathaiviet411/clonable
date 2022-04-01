<?php

namespace App\Console\Commands;

use App\Jobs\RecalculateScheduleJob;
use App\Models\Vehicle;
use Illuminate\Console\Command;

class RecalculateScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recalculate:schedule {vehicleId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $vehicleId = null;

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
        $vehicles = Vehicle::select('id')->get();
        if ($this->argument('vehicleId')) {
            RecalculateScheduleJob::dispatch($this->argument('vehicleId'));
        } else {
            foreach ($vehicles as $vehicle) {
                RecalculateScheduleJob::dispatch($vehicle->id);
            }
        }
    }
}
