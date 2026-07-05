<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;

class ProductService
{
    public function getAllProducts(): Collection
    {
        return Product::with('category')->get();
    }

    public function getProductById(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    public function getProductsByCategory(int $categoryId): Collection
    {
        return Product::byCategory($categoryId)->with('category')->get();
    }

    public function getInStockProducts(): Collection
    {
        return Product::inStock()->with('category')->get();
    }

    public function searchProducts(string $query): Collection
    {
        return Product::where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->with('category')
            ->get();
    }

    public function getProductStatistics(): array
    {
        return [
            'total_products' => Product::count(),
            'in_stock' => Product::inStock()->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'total_value' => Product::selectRaw('SUM(price * stock) as value')->value('value') ?? 0,
        ];
    }

    public function getTopSellingProducts(int $limit = 5): Collection
    {
        return Product::with('category')
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
