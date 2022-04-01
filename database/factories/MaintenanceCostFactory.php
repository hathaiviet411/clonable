<?php

namespace Database\Factories;

use App\Models\MaintenanceCost;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Accessory;
use Faker\Generator as Faker;

class MaintenanceCostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MaintenanceCost::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $vehicle = Vehicle::pluck('id', 'id')->toArray();
        return [
            'type' => array_rand(LIST_TYPE),
            'scheduled_date' => Carbon::parse('2021-02-01'),
            'maintained_date' => Carbon::parse('2021-02-01'),
            'schedule_month' => 2,
            'schedule_year' => 2021,
            'vehicle_id' => array_rand($vehicle),
            'status' => null,
        ];
    }
}
