<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->withAvg('reviews', 'rating')->withCount('reviews');
        
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->latest()->get();
        $categories = Category::withCount('products')->get();
        $heroProduct = Product::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->inStock()
            ->orderByDesc('is_featured')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('stock')
            ->first();
        $featuredProducts = Product::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->inStock()
            ->orderByDesc('is_featured')
            ->latest()
            ->take(3)
            ->get();
        $saleProducts = Product::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->inStock()
            ->whereNotNull('sale_price')
            ->where(function ($query) {
                $query->whereNull('sale_ends_at')
                    ->orWhere('sale_ends_at', '>', now());
            })
            ->orderByRaw('(price - sale_price) DESC')
            ->take(3)
            ->get();
        $mainPromo = $saleProducts->first();
        $stats = [
            'products' => Product::count(),
            'in_stock' => Product::inStock()->count(),
            'categories' => Category::count(),
        ];
        
        return view('products.index', compact('products', 'categories', 'heroProduct', 'featuredProducts', 'mainPromo', 'saleProducts', 'stats'));
    }
    
    public function show(Product $product): View
    {
        $product->load(['category', 'reviews.user']);
        $averageRating = $product->reviews()->avg('rating') ?: 0;
        $reviewsCount = $product->reviews()->count();
        $relatedProducts = Product::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereKeyNot($product->id)
            ->when($product->category_id, function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
            })
            ->inStock()
            ->latest()
            ->take(4)
            ->get();
        
        $userReview = auth()->check()
            ? $product->reviews()->where('user_id', auth()->id())->first()
            : null;

        return view('products.show', compact('product', 'averageRating', 'reviewsCount', 'userReview', 'relatedProducts'));
    }
}
