<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Mail\CustomerCreatedMail;
use App\Mail\CustomerAcceptedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CustomerAndReviewFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $client;
    protected Product $product;

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
            'status' => 'refused', // initially refused to test accept
        ]);

        $category = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
        ]);

        $this->product = Product::create([
            'name' => 'iPhone 15',
            'description' => 'Latest iPhone',
            'price' => 999.00,
            'stock' => 10,
            'category_id' => $category->id,
        ]);
    }

    public function test_admin_adding_customer_sends_email_with_credentials(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.customers.store'), [
            'name' => 'New Customer',
            'email' => 'newcust@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'newcust@test.com',
            'status' => 'active',
        ]);

        Mail::assertSent(CustomerCreatedMail::class, function ($mail) {
            return $mail->hasTo('newcust@test.com') && 
                   $mail->password === 'password123' &&
                   $mail->user->name === 'New Customer';
        });
    }

    public function test_admin_accepting_customer_sends_acceptance_email(): void
    {
        Mail::fake();

        // 1. Using direct accept route
        $response = $this->actingAs($this->admin)->patch(route('admin.customers.accept', $this->client));

        $response->assertRedirect();
        $this->client->refresh();
        $this->assertEquals('active', $this->client->status);

        Mail::assertSent(CustomerAcceptedMail::class, function ($mail) {
            return $mail->hasTo('client@test.com') && 
                   $mail->user->name === 'Client User';
        });
    }

    public function test_admin_updating_customer_status_to_active_sends_email(): void
    {
        Mail::fake();

        // 2. Using update route to change status from refused to active
        $response = $this->actingAs($this->admin)->put(route('admin.customers.update', $this->client), [
            'name' => 'Client User Updated',
            'email' => 'client@test.com',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $this->client->refresh();
        $this->assertEquals('active', $this->client->status);

        Mail::assertSent(CustomerAcceptedMail::class, function ($mail) {
            return $mail->hasTo('client@test.com');
        });
    }

    public function test_client_can_submit_review_and_rating(): void
    {
        // Make client active so they can authenticate/login correctly if checked
        $this->client->update(['status' => 'active']);

        $response = $this->actingAs($this->client)->post(route('products.reviews.store', $this->product), [
            'rating' => 4,
            'comment' => 'Very good quality product.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->client->id,
            'product_id' => $this->product->id,
            'rating' => 4,
            'comment' => 'Very good quality product.',
        ]);

        // Eager load average rating check on product index
        $this->get(route('products.index'))
            ->assertStatus(200)
            ->assertSee('iPhone 15')
            ->assertSee('4.0')
            ->assertSee('(1)');

        // Check product show page details
        $this->get(route('products.show', $this->product))
            ->assertStatus(200)
            ->assertSee('Very good quality product.')
            ->assertSee('Client User');
    }

    public function test_admin_can_view_product_ratings_and_moderate_them(): void
    {
        $this->client->update(['status' => 'active']);

        // Create a review
        $review = \App\Models\Review::create([
            'user_id' => $this->client->id,
            'product_id' => $this->product->id,
            'rating' => 3,
            'comment' => 'Inappropriate review content.',
        ]);

        // 1. Admin goes to products index and sees the average rating
        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $response->assertStatus(200);
        $response->assertSee('3.0');
        $response->assertSee('(1)');

        // 2. Admin goes to edit product and sees the review
        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $this->product));
        $response->assertStatus(200);
        $response->assertSee('Inappropriate review content.');
        $response->assertSee('Client User');
        $response->assertSee('Supprimer l\'avis', false);

        // 3. Admin deletes the review
        $response = $this->actingAs($this->admin)->delete(route('admin.reviews.destroy', $review));
        $response->assertRedirect();
        
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}
