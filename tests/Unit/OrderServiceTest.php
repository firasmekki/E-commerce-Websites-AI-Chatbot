<?php

namespace Tests\Unit;

use App\Services\OrderService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    public function test_get_all_orders(): void
    {
        Order::factory()->count(5)->create();

        $orders = $this->orderService->getAllOrders();

        $this->assertCount(5, $orders);
    }

    public function test_get_order_by_id(): void
    {
        $order = Order::factory()->create();

        $foundOrder = $this->orderService->getOrderById($order->id);

        $this->assertNotNull($foundOrder);
        $this->assertEquals($order->id, $foundOrder->id);
    }

    public function test_get_orders_by_user(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user->id]);
        Order::factory()->count(2)->create(); // Other user's orders

        $userOrders = $this->orderService->getOrdersByUser($user->id);

        $this->assertCount(3, $userOrders);
    }

    public function test_get_orders_by_status(): void
    {
        Order::factory()->count(3)->create(['status' => 'pending']);
        Order::factory()->count(2)->create(['status' => 'delivered']);

        $pendingOrders = $this->orderService->getOrdersByStatus('pending');

        $this->assertCount(3, $pendingOrders);
    }

    public function test_get_order_statistics(): void
    {
        Order::factory()->count(3)->create(['status' => 'pending', 'total_amount' => 100]);
        Order::factory()->count(2)->create(['status' => 'delivered', 'total_amount' => 200]);

        $stats = $this->orderService->getOrderStatistics();

        $this->assertEquals(5, $stats['total_orders']);
        $this->assertEquals(3, $stats['pending']);
        $this->assertEquals(2, $stats['delivered']);
        $this->assertEquals(700, $stats['total_revenue']);
    }

    public function test_get_monthly_revenue(): void
    {
        Order::factory()->count(3)->create([
            'order_date' => now()->subMonth(),
            'total_amount' => 100
        ]);

        $revenue = $this->orderService->getMonthlyRevenue(2);

        $this->assertCount(2, $revenue);
        $this->assertArrayHasKey('month', $revenue[0]);
        $this->assertArrayHasKey('amount', $revenue[0]);
    }
}
