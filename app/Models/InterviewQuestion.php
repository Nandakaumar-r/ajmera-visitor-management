<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question',
        'description',
        'type',
        'options',
        'is_required',
        'order',
        'is_active'
    ];

    protected $casts = [
        'options' => 'json',
        'is_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    protected $table = 'interview_questions';
}
