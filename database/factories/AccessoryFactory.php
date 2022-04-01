<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Accessory;
use Faker\Generator as Faker;
class AccessoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Accessory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'tonnage' => rand(1, 10),
            'passed_year' => rand(0, 10),
            'mileage' => rand(1, 10),
            'created_by' => 1,
            'updated_by' => 1
        ];
    }
}
