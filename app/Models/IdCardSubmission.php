<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdCardSubmission extends Model
{
    use HasFactory;

    protected $table = 'id_card_submissions';

    protected $fillable = [
        'employee_id',
        'file_path',
        'remarks',
        'submitted_by'
    ];
}
