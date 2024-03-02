<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AddressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener el usuario
        $user = User::where('email', 'usuario@example.com')->first();

        // Crear dirección para el usuario
        if ($user) {
            Address::create([
                'street' => 'Avenida de la Constitución',
                'number' => '12',
                'city' => 'Sevilla',
                'province' => 'Sevilla',
                'country' => 'España',
                'postal_code' => '41004',
                'addressable_id' => $user->id,
                'addressable_type' => User::class,
            ]);
        }
    }
}
