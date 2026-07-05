<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request, OrderService $orderService): View
    {
        abort_unless($request->user()?->is_admin, 403);

        $monthlyRevenue = $orderService->getMonthlyRevenue(6);

        $categoriesBreakdown = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->get()
            ->map(fn($cat) => [
                'name' => $cat->name,
                'count' => $cat->products_count,
            ])
            ->toArray();

        return view('admin.dashboard', [
            'stats' => [
                'revenue' => Order::sum('total_amount'),
                'orders' => Order::count(),
                'pendingOrders' => Order::where('status', 'pending')->count(),
                'customers' => User::where('is_admin', false)->count(),
                'activeCustomers' => User::where('is_admin', false)->where('status', 'active')->count(),
                'refusedCustomers' => User::where('is_admin', false)->where('status', 'refused')->count(),
                'products' => Product::count(),
                'lowStock' => Product::where('stock', '<=', 5)->count(),
            ],
            'recentOrders' => Order::with('user')->latest('order_date')->take(5)->get(),
            'recentCustomers' => User::where('is_admin', false)->latest()->take(5)->get(),
            'lowStockProducts' => Product::with('category')->where('stock', '<=', 5)->orderBy('stock')->take(5)->get(),
            'monthlyRevenue' => $monthlyRevenue,
            'categoriesBreakdown' => $categoriesBreakdown,
        ]);
    }
}
