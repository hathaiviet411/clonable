<?php

namespace Database\Factories;

use App\Models\PlateHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
class PlateHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlateHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => Carbon::now(),
            'no_number_plate' => '横浜800あ' . rand(1000, 9999),
        ];
    }
}
