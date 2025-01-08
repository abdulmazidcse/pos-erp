<?php

namespace Database\Factories;

use App\Models\GeneralSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeneralSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GeneralSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'payment_status' => $this->faker->word,
        'date_status' => $this->faker->word,
        'date_format' => $this->faker->word,
        'sender_id' => $this->faker->word,
        'created_at' => $this->faker->date('Y-m-d H:i:s'),
        'updated_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
