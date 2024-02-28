<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategoriesTableSeeder::class,
        ]);

        $this->call([
            CartCodesTableSeeder::class,
        ]);

        $this->call([
            BooksTableSeeder::class,
        ]);

        $this->call([
            UserSeeder::class,
        ]);

        $this->call([
            AddressTableSeeder::class,
        ]);
    }
}
