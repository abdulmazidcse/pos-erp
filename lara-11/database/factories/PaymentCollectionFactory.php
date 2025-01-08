<?php

namespace Database\Factories;

use App\Models\PaymentCollection;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentCollectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentCollection::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sale_id' => $this->faker->randomDigitNotNull,
        'paying_by' => $this->faker->word,
        'amount' => $this->faker->word,
        'payment_note' => $this->faker->text,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
