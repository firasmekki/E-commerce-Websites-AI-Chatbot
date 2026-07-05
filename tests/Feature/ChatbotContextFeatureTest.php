<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotContextFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $this->product = Product::create([
            'name' => 'SuperLaptop',
            'description' => 'Fast laptop',
            'price' => 1200.00,
            'stock' => 5,
            'category_id' => $category->id,
        ]);

        Review::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Incredible performance!',
        ]);

        $this->coupon = Coupon::create([
            'code' => 'CHATSALE10',
            'type' => 'percent',
            'value' => 10,
            'expires_at' => now()->addDays(2),
            'is_active' => true,
        ]);
    }

    public function test_chatbot_returns_product_reviews_in_local_mode(): void
    {
        // When config('services.gemini.api_key') is empty, it uses local fallback response
        // Let's ensure it handles it correctly
        $response = $this->actingAs($this->user)->postJson(route('chatbot.chat'), [
            'message' => 'avis SuperLaptop',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'reply']);
        $response->assertJsonFragment(['status' => 'success']);
        
        $reply = $response->json('reply');
        $this->assertStringContainsString('SuperLaptop', $reply);
        $this->assertStringContainsString('Incredible performance!', $reply);
        $this->assertStringContainsString('Test Client', $reply);
    }

    public function test_chatbot_returns_active_coupons_in_local_mode(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('chatbot.chat'), [
            'message' => 'Est-ce qu\'il y a un code promo ?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'reply']);
        $response->assertJsonFragment(['status' => 'success']);
        
        $reply = $response->json('reply');
        $this->assertStringContainsString('CHATSALE10', $reply);
        $this->assertStringContainsString('10%', $reply);
    }
}
