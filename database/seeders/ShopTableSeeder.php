<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = [
            [
                'name' => 'Librería Angelus',
                'address' => 'Calle de la Lectura, 12',
                'books' => 'La mansión de las pesadillas, Abre la mente, piensa diferente',
                'active' => true,
            ],
            [
                'name' => 'Librería La Buena Letra',
                'address' => 'Calle de la Escritura, 15',
                'books' => '¿El asesino sigue aquí?',
                'active' => true,
            ],
            [
                'name' => 'Librería La Buena Tinta',
                'address' => 'Calle de la Poesía, 18',
                'books' => 'La mansión de las pesadillas, Abre la mente, piensa diferente, ¿El asesino sigue aquí?',
                'active' => true,
            ],
        ];

        foreach ($shops as $shop) {
            \App\Models\Shop::create($shop);
        }
    }
}
