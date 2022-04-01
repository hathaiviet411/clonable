<?php

namespace Database\Factories;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Helper\Common;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $lstUser = User::pluck('id')->toArray();
        return [
            'user_code' => Common::randNotInArr(111111, 999999, $lstUser),
            'user_name' => $this->faker->name,
            'password' => Hash::make('123456789'),
        ];
    }
}
