<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotFeedback extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'rating',
        'comment',
        'was_helpful',
        'improvement_needed',
    ];

    protected $casts = [
        'was_helpful' => 'boolean',
        'improvement_needed' => 'boolean',
        'rating' => 'integer',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
