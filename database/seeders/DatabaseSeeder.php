<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Idempotent: évite les doublons de users quand on relance db:seed.
        // (important car users.email est unique)

        // if (!User::query()->exists()) {
        //     User::factory(10)->create();
        // }

        User::firstOrCreate(
            ['email' => 'admin@nextcommerce.test'],
            [
                'name' => 'Admin NextCommerce',
                'is_admin' => true,
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'client@nextcommerce.test'],
            [
                'name' => 'Client NextCommerce',
                'is_admin' => false,
                'password' => bcrypt('password'),
            ]
        );



        $this->call([
            CategorySeeder::class,
            CouponSeeder::class,
            // ProductSeeder::class, // désactivé pour éviter d'ajouter des produits “fake”
            // OrderSeeder::class, // désactivé pour n'avoir que les commandes réelles
        ]);
    }
}
