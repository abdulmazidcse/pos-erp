<?php

namespace Database\Factories;

use App\Models\SaleReturnItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleReturnItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleReturnItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sale_id' => $this->faker->randomDigitNotNull,
        'sale_item_id' => $this->faker->randomDigitNotNull,
        'return_type' => $this->faker->randomDigitNotNull,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
