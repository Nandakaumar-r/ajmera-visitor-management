<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitInterviewResponse extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'user_id',
        'answer',
    ];

    public function question()
    {
        return $this->belongsTo(ExitInterviewQuestion::class, 'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
