<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalUser extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'candidate_details'; // table name in second DB

      protected $casts = [
        'payslips' => 'array',
        'bank_proof' => 'array',
        'education_docs' => 'array',
        'salary_revision_letter' => 'array',
        'experience_letters' => 'array',
        'passport_photo' => 'array',
    ];
}
