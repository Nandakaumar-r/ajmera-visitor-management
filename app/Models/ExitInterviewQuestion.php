<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitInterviewQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'field_type',
        'options', // JSON encoded options for radio/checkbox fields
    ];

    public function responses()
    {
        return $this->hasMany(ExitInterviewResponse::class, 'question_id');
    }
}
