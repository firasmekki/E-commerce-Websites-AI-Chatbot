<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

class OrderService
{
    public function getAllOrders(): Collection
    {
        return Order::with('user', 'orderItems.product')->get();
    }

    public function getOrderById(int $id): ?Order
    {
        return Order::with('user', 'orderItems.product.category')->find($id);
    }

    public function getOrdersByUser(int $userId): Collection
    {
        return Order::byUser($userId)->with('orderItems.product')->get();
    }

    public function getOrdersByStatus(string $status): Collection
    {
        return Order::byStatus($status)->with('user', 'orderItems.product')->get();
    }

    public function getOrderStatistics(): array
    {
        return [
            'total_orders' => Order::count(),
            'pending' => Order::byStatus('pending')->count(),
            'processing' => Order::byStatus('processing')->count(),
            'shipped' => Order::byStatus('shipped')->count(),
            'delivered' => Order::byStatus('delivered')->count(),
            'cancelled' => Order::byStatus('cancelled')->count(),
            'total_revenue' => Order::sum('total_amount'),
        ];
    }

    public function getMonthlyRevenue(int $months = 6): array
    {
        $revenue = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue[] = [
                'month' => $date->format('F Y'),
                'amount' => Order::whereYear('order_date', $date->year)
                    ->whereMonth('order_date', $date->month)
                    ->sum('total_amount'),
            ];
        }
        return $revenue;
    }
}
