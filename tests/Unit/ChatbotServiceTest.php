<?php

namespace Tests\Unit;

use App\Services\ChatbotService;
use App\Services\ProductService;
use App\Services\OrderService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChatbotService $chatbotService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chatbotService = new ChatbotService(
            app(ProductService::class),
            app(OrderService::class)
        );
    }

    public function test_extract_search_terms_removes_stop_words(): void
    {
        $message = 'le la un des recherche iPhone';
        
        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('extractSearchTerms');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->chatbotService, $message);
        
        $this->assertStringContainsString('iphone', strtolower($result));
        $this->assertStringNotContainsString('le', $result);
    }

    public function test_build_prompt_includes_context_and_data(): void
    {
        $message = 'Quels produits sont disponibles?';
        $businessData = ['test' => 'data'];
        $conversationHistory = [];
        
        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('buildPrompt');
        $method->setAccessible(true);
        
        $prompt = $method->invoke($this->chatbotService, $message, $businessData, $conversationHistory);
        
        $this->assertStringContainsString($message, $prompt);
        $this->assertStringContainsString('test', $prompt);
    }

    public function test_retrieve_business_data_for_product_query(): void
    {
        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('retrieveBusinessData');
        $method->setAccessible(true);
        
        $user = User::factory()->create();
        $data = $method->invoke($this->chatbotService, 'Quels produits sont en stock?', $user);
        
        $this->assertArrayHasKey('in_stock_products', $data);
    }

    public function test_retrieve_business_data_for_order_query(): void
    {
        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('retrieveBusinessData');
        $method->setAccessible(true);
        
        $user = User::factory()->create();
        $data = $method->invoke($this->chatbotService, 'Affiche mes commandes', $user);
        
        $this->assertArrayHasKey('user_orders', $data);
    }

    public function test_retrieve_business_data_for_statistics_query(): void
    {
        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('retrieveBusinessData');
        $method->setAccessible(true);
        
        $user = User::factory()->create();
        $data = $method->invoke($this->chatbotService, 'Quelles sont les statistiques?', $user);
        
        $this->assertArrayHasKey('product_stats', $data);
        $this->assertArrayHasKey('order_stats', $data);
    }
}
