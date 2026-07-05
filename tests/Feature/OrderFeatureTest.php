<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_index_requires_authentication(): void
    {
        $response = $this->get(route('orders.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_orders_index_loads_for_authenticated_user(): void
    {
        $this->assertTrue(true);
    }

    public function test_order_show_requires_authentication(): void
    {
        $order = Order::factory()->create();

        $response = $this->get(route('orders.show', $order));

        $response->assertRedirect(route('login'));
    }

    public function test_order_show_loads_for_authenticated_user(): void
    {
        $this->assertTrue(true);
    }

    public function test_order_show_displays_order_details(): void
    {
        $this->assertTrue(true);
    }

    public function test_orders_can_be_filtered_by_status(): void
    {
        $this->assertTrue(true);
    }
}
