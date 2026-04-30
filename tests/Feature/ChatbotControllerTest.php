<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChatbotConversation;
use App\Services\ChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class ChatbotControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $chatbotService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a mock of the ChatbotService
        $this->chatbotService = Mockery::mock(ChatbotService::class);
        $this->app->instance(ChatbotService::class, $this->chatbotService);
        
        // Create a test user
        $this->user = User::factory()->create();
    }

    public function test_send_message_requires_authentication()
    {
        $response = $this->postJson('/api/chatbot/message', [
            'message' => 'Test message',
            'platform' => 'web'
        ]);

        $response->assertStatus(401);
    }

    public function test_send_message_validates_input()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/chatbot/message', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_send_message_successful()
    {
        $testMessage = 'Test message';
        $expectedResponse = ['response' => 'Bot response'];

        $this->chatbotService
            ->shouldReceive('handleMessage')
            ->once()
            ->with($testMessage, $this->user, 'web')
            ->andReturn($expectedResponse);

        $response = $this->actingAs($this->user)
            ->postJson('/api/chatbot/message', [
                'message' => $testMessage,
                'platform' => 'web'
            ]);

        $response->assertStatus(200)
            ->assertJson($expectedResponse);
    }

    public function test_submit_feedback_requires_authentication()
    {
        $response = $this->postJson('/api/chatbot/feedback', [
            'conversation_id' => 1,
            'rating' => 5,
            'comment' => 'Great response!'
        ]);

        $response->assertStatus(401);
    }

    public function test_submit_feedback_validates_input()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/chatbot/feedback', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['conversation_id', 'rating']);
    }

    public function test_submit_feedback_successful()
    {
        // Create a conversation first
        $conversation = ChatbotConversation::create([
            'user_id' => $this->user->id,
            'platform' => 'web',
            'message' => 'Test message',
            'response' => 'Test response'
        ]);

        $feedbackData = [
            'conversation_id' => $conversation->id,
            'rating' => 5,
            'comment' => 'Great response!'
        ];

        $this->chatbotService
            ->shouldReceive('saveFeedback')
            ->once()
            ->with($feedbackData['conversation_id'], $feedbackData['rating'], $feedbackData['comment'])
            ->andReturn(true);

        $response = $this->actingAs($this->user)
            ->postJson('/api/chatbot/feedback', $feedbackData);

        $response->assertStatus(200);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
