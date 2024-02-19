<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cart_codes')->insert([
            ['code' => 'madicode42', 'percent_discount' => 0.0, 'fixed_discount' => 10.0, 'available_uses' => 10, 'expiration_date' => '2024-02-19', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '42forall', 'percent_discount' => 0.1, 'fixed_discount' => 0.0, 'available_uses' => 5, 'expiration_date' => '2024-02-19', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '91011', 'percent_discount' => 0.0, 'fixed_discount' => 20.0, 'available_uses' => 15, 'expiration_date' => '2024-02-19', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '121314', 'percent_discount' => 0.2, 'fixed_discount' => 0.0, 'available_uses' => 20, 'expiration_date' => '2024-02-19', 'created_at' => now(), 'updated_at' => now()],
            ['code' => '151617', 'percent_discount' => 0.0, 'fixed_discount' => 30.0, 'available_uses' => 25, 'expiration_date' => '2024-02-19', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
