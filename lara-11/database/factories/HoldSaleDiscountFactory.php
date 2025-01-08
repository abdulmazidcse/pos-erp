<?php

namespace Database\Factories;

use App\Models\HoldSaleDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

class HoldSaleDiscountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HoldSaleDiscount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'hold_sale_id' => $this->faker->randomDigitNotNull,
        'discount_value' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
