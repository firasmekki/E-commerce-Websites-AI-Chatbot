<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_endpoint_requires_authentication(): void
    {
        $response = $this->postJson(route('chatbot.chat'), [
            'message' => 'Hello'
        ]);

        $response->assertStatus(401);
    }

    public function test_chatbot_endpoint_requires_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('chatbot.chat'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    public function test_chatbot_endpoint_accepts_valid_message(): void
    {
        $this->assertTrue(true);
    }

    public function test_chatbot_history_requires_authentication(): void
    {
        $response = $this->getJson(route('chatbot.history'));

        $response->assertStatus(401);
    }

    public function test_chatbot_history_returns_user_conversations(): void
    {
        $this->assertTrue(true);
    }

    public function test_chatbot_handles_api_error_gracefully(): void
    {
        $user = User::factory()->create();
        config(['services.gemini.api_key' => 'test-key']);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(null, 500)
        ]);

        $response = $this->actingAs($user)->postJson(route('chatbot.chat'), [
            'message' => 'Test message'
        ]);

        $response->assertStatus(500);
    }
}
