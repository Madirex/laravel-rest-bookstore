<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cart_codes')->insert([
            ['id' => 'c5944365-d957-4221-a743-778c507a5397', 'code' => 'NULLER', 'percent_discount' => 0.0, 'fixed_discount' => 10.0, 'available_uses' => 10, 'expiration_date' => '2024-04-19', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'code' => '42forall', 'percent_discount' => 10, 'fixed_discount' => 0.0, 'available_uses' => 5, 'expiration_date' => '2024-04-19', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'code' => '91011', 'percent_discount' => 0.0, 'fixed_discount' => 20.0, 'available_uses' => 15, 'expiration_date' => '2024-04-19', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'code' => '121314', 'percent_discount' => 20, 'fixed_discount' => 0.0, 'available_uses' => 20, 'expiration_date' => '2024-04-19', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'code' => '151617', 'percent_discount' => 0.0, 'fixed_discount' => 30.0, 'available_uses' => 25, 'expiration_date' => '2024-04-19', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
