<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => $this->faker->unique()->word(),
            'amount' => $this->faker->randomFloat(3, 1000, 99999),
            'count' => $this->faker->randomNumber(),
            'start_time' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s'),
            'expiration_time' => $this->faker->dateTimeBetween('+1minute', '+1 year')->format('Y-m-d H:i:s'),
        ];
    }
}
