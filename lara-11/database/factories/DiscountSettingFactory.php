<?php

namespace Database\Factories;

use App\Models\DiscountSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DiscountSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_wise' => $this->faker->randomDigitNotNull,
        'customer_group_wise' => $this->faker->randomDigitNotNull,
        'individual_customer' => $this->faker->randomDigitNotNull,
        'product_wise' => $this->faker->randomDigitNotNull,
        'category_wise' => $this->faker->randomDigitNotNull,
        'sub_category_wise' => $this->faker->randomDigitNotNull,
        'vendor_wise' => $this->faker->randomDigitNotNull,
        'slow_moving_product' => $this->faker->randomDigitNotNull,
        'slow_moving_product_discount' => $this->faker->randomDigitNotNull,
        'fast_moving_product' => $this->faker->randomDigitNotNull,
        'fast_moving_product_discount' => $this->faker->randomDigitNotNull,
        'sales_platform' => $this->faker->randomDigitNotNull,
        'sales_platform_pos' => $this->faker->randomDigitNotNull,
        'sales_platform_pos_discount' => $this->faker->randomDigitNotNull,
        'sales_platform_ecom' => $this->faker->randomDigitNotNull,
        'sales_platform_ecom_discount' => $this->faker->randomDigitNotNull,
        'gp_wise' => $this->faker->randomDigitNotNull,
        'gp_wise_discount' => $this->faker->randomDigitNotNull,
        'enable_conditional_discount' => $this->faker->randomDigitNotNull,
        'discount_within_range' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
