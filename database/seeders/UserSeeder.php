<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (!DB::table('users')->where('email', 'angel@madirex.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'angel@madirex.com',
                'password' => Hash::make('123456789'),
                'surname' => 'Admin',
                'username' => 'admin',
                'isDeleted' => false,
                'phone' => '6565656678',
                'image' => 'images/nullers.png',
                'cart' => '',
                'orders' => '[]',
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'admin',
                'email_verified_at' => '2021-10-01 00:00:00',
                'money' => '10000.00'
            ]);

            DB::table('users')->insert([
                'name' => 'Usuario',
                'email' => 'usuario@example.com',
                'password' => Hash::make('password'),
                'surname' => 'Apellido',
                'username' => 'usuario',
                'isDeleted' => false,
                'phone' => '6565656678',
                'image' => 'images/user.png',
                'cart' => '',
                'orders' => '[]',
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 'user',
                'email_verified_at' => '2021-10-01 00:00:00',
                'money' => '0.00'
            ]);
        }
    }
}
