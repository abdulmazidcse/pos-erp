<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Coupon::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->word,
        'name' => $this->faker->word,
        'description' => $this->faker->text,
        'uses' => $this->faker->randomDigitNotNull,
        'max_uses' => $this->faker->randomDigitNotNull,
        'max_uses_user' => $this->faker->randomDigitNotNull,
        'type' => $this->faker->word,
        'discount_amount' => $this->faker->randomDigitNotNull,
        'is_fixed' => $this->faker->word,
        'starts_at' => $this->faker->date('Y-m-d H:i:s'),
        'expires_at' => $this->faker->date('Y-m-d H:i:s'),
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
