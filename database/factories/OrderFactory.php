<?php

namespace Database\Factories;

use App\Models\OrderLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 10),
            'status' => fake()->randomElement(['pending', 'finished', 'cancelled']),
            'subtotal' => fake()->randomFloat(2, 0, 1000),
            'total_amount' => fake()->randomFloat(2, 0, 1000),
            'total_lines' => fake()->numberBetween(1, 10),
            'is_deleted' => fake()->boolean(),
            'cart_code' => fake()->uuid(),
            'order_lines' => OrderLine::factory()->count(1),
            'finished_at' => fake()->optional()->dateTime(),
        ];
    }
}
