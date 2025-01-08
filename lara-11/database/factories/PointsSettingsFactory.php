<?php

namespace Database\Factories;

use App\Models\PointsSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointsSettingsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PointsSettings::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'enable_points_rewards' => $this->faker->randomDigitNotNull,
        'enable_signup_points' => $this->faker->randomDigitNotNull,
        'signup_points' => $this->faker->word,
        'enable_referral_points' => $this->faker->randomDigitNotNull,
        'referral_points' => $this->faker->randomDigitNotNull,
        'enable_social_point' => $this->faker->randomDigitNotNull,
        'social_share_facebook' => $this->faker->randomDigitNotNull,
        'social_share_twitter' => $this->faker->randomDigitNotNull,
        'custom_points_on_cart' => $this->faker->randomDigitNotNull,
        'cart_points_rate' => $this->faker->randomDigitNotNull,
        'cart_price_rate' => $this->faker->randomDigitNotNull,
        'enable_points_order_total' => $this->faker->randomDigitNotNull,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
