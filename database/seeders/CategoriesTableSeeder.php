<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Misterio', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desarrollo personal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manga', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Terror', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Drama', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
