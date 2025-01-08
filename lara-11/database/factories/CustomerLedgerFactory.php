<?php

namespace Database\Factories;

use App\Models\CustomerLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerLedgerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerLedger::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id' => $this->faker->word,
        'transaction_type' => $this->faker->word,
        'opening_balance' => $this->faker->word,
        'collection_amount' => $this->faker->word,
        'return_amount' => $this->faker->word,
        'discount_amount' => $this->faker->word,
        'closing_balance' => $this->faker->word,
        'transaction_date' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
