<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\CartController;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    if ($user->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    $recentOrders = Order::where('user_id', $user->id)
        ->latest('order_date')
        ->take(4)
        ->get();

    return view('dashboard', [
        'stats' => [
            'products' => Product::count(),
            'inStock' => Product::where('stock', '>', 0)->count(),
            'categories' => Category::count(),
            'orders' => Order::where('user_id', $user->id)->count(),
            'pendingOrders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'totalSpent' => Order::where('user_id', $user->id)->sum('total_amount'),
            'cartItems' => collect($request->session()->get('cart', []))->sum('quantity'),
        ],
        'recentOrders' => $recentOrders,
        'recentProducts' => Product::with('category')->inStock()->latest()->take(4)->get(),
        'saleProducts' => Product::with('category')
            ->inStock()
            ->whereNotNull('sale_price')
            ->where(function ($query) {
                $query->whereNull('sale_ends_at')
                    ->orWhere('sale_ends_at', '>', now());
            })
            ->take(3)
            ->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public routes
Route::resource('products', ProductController::class)->only(['index', 'show']);
Route::resource('categories', CategoryController::class)->only(['index', 'show']);

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::patch('/admin/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
    Route::patch('/admin/customers/{customer}/accept', [AdminCustomerController::class, 'accept'])->name('admin.customers.accept');
    Route::patch('/admin/customers/{customer}/refuse', [AdminCustomerController::class, 'refuse'])->name('admin.customers.refuse');
    Route::resource('admin/customers', AdminCustomerController::class)
        ->names('admin.customers')
        ->except(['show']);
    Route::resource('admin/products', AdminProductController::class)
        ->names('admin.products')
        ->except(['show']);
    Route::resource('admin/categories', AdminCategoryController::class)
        ->names('admin.categories')
        ->except(['show']);
    Route::resource('admin/coupons', \App\Http\Controllers\AdminCouponController::class)
        ->names('admin.coupons')
        ->except(['show']);
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::post('/products/{product}/reviews', [\App\Http\Controllers\ProductReviewController::class, 'store'])->name('products.reviews.store');
    Route::delete('/admin/reviews/{review}', [\App\Http\Controllers\ProductReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
    Route::post('/cart/checkout/place', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/api/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::get('/api/chatbot/history', [ChatbotController::class, 'history'])->name('chatbot.history');
});

require __DIR__.'/auth.php';
