<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\MaintenanceCost;
use App\Models\MaintenanceLease;
use App\Models\PlateHistory;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Accessory;
use Faker\Generator as Faker;

class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $vehicle = Department::pluck('id', 'id')->toArray();
        return [
            'department_id' => 1,//array_rand($vehicle),
            'driving_classification' => 'CVS',
            'tonnage' => '2t超',
            'truck_classification' => '3トン（ＣＶＳ）',
            'truck_classification_number' => 3,
            'truck_classification_2' => '小型',
            'manufactor' => 'いすゞ',
            'first_registration' => rand(2010, 2015) . '-' . rand(1, 12),
            'box_distinction' => '冷蔵',
            'inspection_expiration_date' => $this->faker->dateTimeBetween('now', '10 years'),
            'vehicle_identification_number' => 'NMR85-' . rand(100000, 9999999),
            'owner' => 'イズミ物流㈱',
            'etc_certification_number' => null,
            'etc_number' => null,
            'fuel_card_number_1' => null,
            'fuel_card_number_2' => null,
            'driving_recorder' => null,
            'box_shape' => null,
            'mount' => null,
            'refrigerator' => null,
            'eva_type' => null,
            'gate' => null,
            'humidifier' => rand(0, 1),
            'type' => null,
            'motor' => null,
            'displacement' => null,
            'length' => null,
            'width' => null,
            'height' => null,
            'maximum_loading_capacity' => null,
            'vehicle_total_weight' => null,
            'in_box_length' => null,
            'in_box_width' => null,
            'in_box_height' => null,
            'voluntary_insurance' => null,
            'liability_insurance_period' => null,
            'insurance_company' => null,
            'agent' => null,
            'tire_size' => null,
            'battery_size' => null,
            'monthly_mileage' => null,
            'remark_old_car_1' => null,
            'remark_old_car_2' => null,
            'remark_old_car_3' => null,
            'remark_old_car_4' => null,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Vehicle $vhc) {
            //create history

        })->afterCreating(function (Vehicle $vhc) {
            PlateHistory::factory()->count(1)->state(function (array $attributes) use ($vhc) {
                return array_merge($attributes, ['vehicle_id' => $vhc->id]);
            })->create();
        });
    }
}
