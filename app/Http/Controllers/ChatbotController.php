<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function index()
    {
        return view('test-chatbot');
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'platform' => 'string|in:web,slack,teams,whatsapp',
        ]);

        // Limit the number of requests per day per user use counter to 5 without middleware using session
        $user = $request->user();
        //$user->increment('chatbot_requests', 1);
        $user->save();

        // if ($user->chatbot_requests > 5) {
        //     return response()->json([
        //         'success' => false,
        //         'error' => 'You have reached the maximum number of requests per day. Please try again tomorrow.',
        //         'data' => [
        //             'response' => 'You have reached the maximum number of requests per day. Please try again tomorrow.',
        //             'needs_escalation' => true
        //         ]
        //     ], 429);
        // }

        try {
            $response = $this->chatbotService->handleMessage(
                $request->message,
                $request->user(),
                $request->platform ?? 'web'
            );

            if (!$response['success']) {
                return response()->json($response, 500);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to process message',
                'data' => [
                    'response' => 'An error occurred while processing your request. Please try again later.',
                    'needs_escalation' => true
                ]
            ], 500);
        }
    }

    public function submitFeedback(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:chatbot_conversations,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        try {
            $this->chatbotService->saveFeedback(
                $request->conversation_id,
                $request->rating,
                $request->comment
            );

            return response()->json(['message' => 'Feedback saved successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save feedback',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
