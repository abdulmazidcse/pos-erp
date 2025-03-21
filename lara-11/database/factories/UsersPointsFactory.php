<?php

namespace Database\Factories;

use App\Models\UsersPoints;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsersPointsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UsersPoints::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->randomDigitNotNull,
        'sale_id' => $this->faker->randomDigitNotNull,
        'type' => $this->faker->word,
        'points' => $this->faker->randomDigitNotNull,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
