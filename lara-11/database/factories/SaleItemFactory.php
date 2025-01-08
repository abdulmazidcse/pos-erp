<?php

namespace Database\Factories;

use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sale_id' => $this->faker->randomDigitNotNull,
        'product_id' => $this->faker->randomDigitNotNull,
        'quantity' => $this->faker->word,
        'discount' => $this->faker->word,
        'item_quantity' => $this->faker->word,
        'vat' => $this->faker->word,
        'vat_id' => $this->faker->randomDigitNotNull,
        'inite_price' => $this->faker->word,
        'net_unit_price' => $this->faker->word,
        'cost_price' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
