<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Order::factory()->count(20)->create()
            ->each(function ($order) {
                \App\Models\OrderItem::factory()->count(rand(1, 5))->create([
                    'order_id' => $order->id,
                ]);
            });
    }
}
