<?php

namespace Database\Factories;

use App\Models\SaleReturn;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleReturnFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleReturn::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sale_id' => $this->faker->randomDigitNotNull,
        'return_type' => $this->faker->word,
        'return_amount' => $this->faker->word,
        'return_reason' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
