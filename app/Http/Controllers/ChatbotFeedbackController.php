<?php

namespace App\Http\Controllers;

use App\Models\ChatbotFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chatbot_conversations,id',
            'was_helpful' => 'required|boolean',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $feedback = ChatbotFeedback::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => Auth::id(),
            'was_helpful' => $request->was_helpful,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'data' => $feedback
        ]);
    }

    public function index()
    {
        $feedback = ChatbotFeedback::with(['user', 'conversation'])
            ->latest()
            ->paginate(20);

        return view('chatbot.feedback', compact('feedback'));
    }
}
