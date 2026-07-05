<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::firstOrCreate(
            ['code' => 'SOLDES10'],
            [
                'type' => 'percent',
                'value' => 10.00,
                'is_active' => true,
                'expires_at' => now()->addMonth(),
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'NOEL5'],
            [
                'type' => 'fixed',
                'value' => 5.00,
                'is_active' => true,
                'expires_at' => now()->addMonth(),
            ]
        );
    }
}
