<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalORFCreation extends Model
{
    use HasFactory;

    protected $table = 'orf_creation'; // primary DB uses default connection

    protected $fillable = [
        'candidate_id',
        'name',
        'experience_level',
        'email',
        'gender',
        'expiry_date',
        'user_id',
        'company',
        'date_of_joining',
        'candidate_ctc',
        'designation',
        'employee_type',
        'candidate_type',
        'interview_selection_date'

    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
