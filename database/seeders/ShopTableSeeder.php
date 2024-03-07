<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shops')->insert([
            'name' => 'Madirex Books',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
