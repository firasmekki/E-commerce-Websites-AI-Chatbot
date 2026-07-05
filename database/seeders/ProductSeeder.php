<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Les produits sont ajoutés manuellement via l'interface admin.
        // Ne pas générer de produits fake avec la factory.
    }
}
