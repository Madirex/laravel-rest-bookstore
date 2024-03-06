<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       Order::create(
            [
                'id' => '388473c7-a77b-48fb-9280-48a794c679a8',
                'user_id' => 1,
                'status' => 'pendiente',
                'total_amount' => 10.00,
                'total_lines' => 1,
                'is_deleted' => false,
                'finished_at' => null,
            ]
       );
    }
}
