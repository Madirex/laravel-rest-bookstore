<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderLinesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderLines = [
            [
                'id' => '388473c7-a77b-48fb-9280-48a794c679a9',
                'order_id' => '388473c7-a77b-48fb-9280-48a794c679a8',
                'subtotal' => 10.00,
                'quantity' => 1,
                'book_id' => 1,
                'price' => 10.00,
                'total' => 10.00,
                'selected' => true,
            ],
        ];

        foreach ($orderLines as $orderLine) {
            \App\Models\OrderLine::create($orderLine);
        }
    }
}
