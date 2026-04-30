<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotConversation extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'response',
        'intent',
        'sentiment_score',
        'language',
        'platform',
        'feedback_rating',
        'feedback_comment',
        'is_escalated',
    ];

    protected $casts = [
        'sentiment_score' => 'float',
        'is_escalated' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
