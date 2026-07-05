<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'old_name' => 'Electronics',
                'name' => 'Smartphones & Tech',
                'description' => 'Telephones, accessoires connectes et gadgets utiles au quotidien.',
            ],
            [
                'old_name' => 'Clothing',
                'name' => 'Mode urbaine',
                'description' => 'Pieces tendance, accessoires et essentiels faciles a porter.',
            ],
            [
                'old_name' => 'Books',
                'name' => 'Gaming & Setup',
                'description' => 'Accessoires gaming, bureau moderne et equipement de performance.',
            ],
            [
                'old_name' => 'Home & Garden',
                'name' => 'Maison connectee',
                'description' => 'Objets pratiques pour une maison plus confortable et moderne.',
            ],
            [
                'old_name' => 'Sports',
                'name' => 'Sport & Wellness',
                'description' => 'Articles fitness, bien-etre et lifestyle actif.',
            ],
            [
                'old_name' => 'Toys',
                'name' => 'Cadeaux tendance',
                'description' => 'Idees cadeaux, loisirs et produits coup de coeur.',
            ],
        ];

        foreach ($categories as $category) {
            $model = Category::where('name', $category['old_name'])
                ->orWhere('name', $category['name'])
                ->first();

            unset($category['old_name']);

            if ($model) {
                $model->update($category);
            } else {
                Category::create($category);
            }
        }
    }
}
