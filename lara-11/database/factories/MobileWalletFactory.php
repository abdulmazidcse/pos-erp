<?php

namespace Database\Factories;

use App\Models\MobileWallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class MobileWalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MobileWallet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
        'mobile_wallet' => $this->faker->word,
        'agent_name' => $this->faker->word,
        'mobile_number' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
