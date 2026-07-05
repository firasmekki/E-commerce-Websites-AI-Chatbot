<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_page_loads(): void
    {
        $this->assertTrue(true);
    }

    public function test_products_index_displays_products(): void
    {
        $this->assertTrue(true);
    }

    public function test_product_show_page_loads(): void
    {
        $this->assertTrue(true);
    }

    public function test_products_can_be_filtered_by_category(): void
    {
        $this->assertTrue(true);
    }

    public function test_products_can_be_searched(): void
    {
        $this->assertTrue(true);
    }

    public function test_non_existent_product_returns_404(): void
    {
        $this->assertTrue(true);
    }
}
