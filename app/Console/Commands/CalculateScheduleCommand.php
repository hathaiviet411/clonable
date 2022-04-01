<?php

namespace App\Console\Commands;

use App\Jobs\CalculateScheduleJob;
use App\Models\Vehicle;
use Illuminate\Console\Command;

class CalculateScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:schedule {vehicleId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


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
            CalculateScheduleJob::dispatch($this->argument('vehicleId'));
        } else {
            foreach ($vehicles as $vehicle) {
                CalculateScheduleJob::dispatch($vehicle->id);
            }
        }
    }
}
