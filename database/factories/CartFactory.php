<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'id' => fake()->uuid,
            'user_id' => fake()->uuid,
            'book_id'=> fake()->numberBetween(1, 10),
            'is_checked' => fake()->boolean
        ];
    }
}
