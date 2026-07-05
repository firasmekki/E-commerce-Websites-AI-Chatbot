<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_has_order_items(): void
    {
        $product = Product::factory()->create();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $product->orderItems());
    }

    public function test_in_stock_scope_returns_only_products_with_stock(): void
    {
        Product::factory()->count(3)->create(['stock' => 0]);
        Product::factory()->count(2)->create(['stock' => 10]);

        $inStockProducts = Product::inStock()->get();

        $this->assertCount(2, $inStockProducts);
        $inStockProducts->each(function ($product) {
            $this->assertGreaterThan(0, $product->stock);
        });
    }

    public function test_by_category_scope_filters_by_category(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Product::factory()->count(3)->create(['category_id' => $category1->id]);
        Product::factory()->count(2)->create(['category_id' => $category2->id]);

        $category1Products = Product::byCategory($category1->id)->get();

        $this->assertCount(3, $category1Products);
        $category1Products->each(function ($product) use ($category1) {
            $this->assertEquals($category1->id, $product->category_id);
        });
    }

    public function test_price_is_casted_to_decimal(): void
    {
        $product = Product::factory()->create(['price' => 99.99]);

        $this->assertEquals(99.99, $product->price);
    }
}
