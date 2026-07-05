<?php

namespace Tests\Unit;

use App\Services\ProductService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
    }

    public function test_get_all_products(): void
    {
        Product::factory()->count(5)->create();

        $products = $this->productService->getAllProducts();

        $this->assertCount(5, $products);
    }

    public function test_get_product_by_id(): void
    {
        $product = Product::factory()->create();

        $foundProduct = $this->productService->getProductById($product->id);

        $this->assertNotNull($foundProduct);
        $this->assertEquals($product->id, $foundProduct->id);
    }

    public function test_get_in_stock_products(): void
    {
        Product::factory()->count(3)->create(['stock' => 0]);
        Product::factory()->count(2)->create(['stock' => 10]);

        $inStockProducts = $this->productService->getInStockProducts();

        $this->assertCount(2, $inStockProducts);
    }

    public function test_search_products_by_name(): void
    {
        Product::factory()->create(['name' => 'iPhone 15']);
        Product::factory()->create(['name' => 'Samsung Galaxy']);
        Product::factory()->create(['name' => 'MacBook Pro']);

        $results = $this->productService->searchProducts('iPhone');

        $this->assertCount(1, $results);
        $this->assertEquals('iPhone 15', $results->first()->name);
    }

    public function test_get_product_statistics(): void
    {
        Product::factory()->count(5)->create(['stock' => 10, 'price' => 100]);
        Product::factory()->count(2)->create(['stock' => 0, 'price' => 50]);

        $stats = $this->productService->getProductStatistics();

        $this->assertEquals(7, $stats['total_products']);
        $this->assertEquals(5, $stats['in_stock']);
        $this->assertEquals(2, $stats['out_of_stock']);
        $this->assertArrayHasKey('total_value', $stats);
    }

    public function test_get_top_selling_products(): void
    {
        $products = Product::factory()->count(5)->create();
        
        $results = $this->productService->getTopSellingProducts(3);

        $this->assertCount(3, $results);
    }
}
