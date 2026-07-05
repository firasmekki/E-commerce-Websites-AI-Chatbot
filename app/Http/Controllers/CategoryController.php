<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('products')->get();
        return view('categories.index', compact('categories'));
    }
    
    public function show(Category $category): View
    {
        $category->load('products');
        return view('categories.show', compact('category'));
    }
}
