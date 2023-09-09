<?php

namespace Database\Factories;

use App\Models\SalesDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesDiscountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesDiscount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sale_id' => $this->faker->randomDigitNotNull,
        'discount_value' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
