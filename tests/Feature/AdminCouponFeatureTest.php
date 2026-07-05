<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCouponFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $client;
    protected Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $this->client = User::create([
            'name' => 'Client User',
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'status' => 'active',
        ]);

        $this->coupon = Coupon::create([
            'code' => 'TESTCOUPON',
            'type' => 'percent',
            'value' => 15,
            'expires_at' => now()->addDays(7),
            'is_active' => true,
        ]);
    }

    public function test_guests_cannot_access_coupons(): void
    {
        $this->get(route('admin.coupons.index'))->assertRedirect(route('login'));
        $this->post(route('admin.coupons.store'), [])->assertRedirect(route('login'));
    }

    public function test_non_admins_cannot_access_coupons(): void
    {
        $this->actingAs($this->client)->get(route('admin.coupons.index'))->assertStatus(403);
        $this->actingAs($this->client)->post(route('admin.coupons.store'), [
            'code' => 'HACKED',
            'type' => 'fixed',
            'value' => 50,
        ])->assertStatus(403);
    }

    public function test_admins_can_view_coupons_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.coupons.index'));

        $response->assertStatus(200);
        $response->assertSee('TESTCOUPON');
        $response->assertSee('Pourcentage (%)');
    }

    public function test_admins_can_create_coupon(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'NEWYEAR50',
            'type' => 'fixed',
            'value' => 50,
            'expires_at' => '2026-12-31',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'code' => 'NEWYEAR50',
            'type' => 'fixed',
            'value' => 50,
            'is_active' => true,
        ]);
    }

    public function test_admins_can_update_coupon(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.coupons.update', $this->coupon), [
            'code' => 'UPDATEDCODE',
            'type' => 'percent',
            'value' => 20,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'id' => $this->coupon->id,
            'code' => 'UPDATEDCODE',
            'value' => 20,
        ]);
    }

    public function test_admins_can_delete_coupon(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.coupons.destroy', $this->coupon));

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseMissing('coupons', [
            'id' => $this->coupon->id,
        ]);
    }
}
