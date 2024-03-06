<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => fake()->uuid(),
            'book_id' => fake()->numberBetween(1, 10),
            'quantity' => fake()->numberBetween(1, 10),
            'price' => fake()->randomFloat(2, 0, 1000),
            'total' => fake()->randomFloat(2, 0, 1000),
            'subtotal' => fake()->randomFloat(2, 0, 1000),
            'selected' => fake()->boolean(),
        ];
    }
}
