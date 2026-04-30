<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewResponse extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'question_id', 'response', 'audio_path'];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function question() {
        return $this->belongsTo(InterviewQuestion::class, 'question_id');
    }
}
