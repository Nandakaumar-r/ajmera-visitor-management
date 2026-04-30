<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalOnboardingCandidateDetails extends Model
{
    use HasFactory;

    protected $table = 'internal_candidate_details';

    protected $fillable = [
        'user_id',
        'status',
        'remarks',
        'name',
        'email',
        'mobile',
        'dob',
        'aadhar_no',
        'pan_no',
        'present_address',
        'permanent_address',
        'aadhar_card',
        'pan_card',
        'payslips',
        'bank_proof',
        'education_docs',
        'salary_revision_letter',
        'experience_letters',
        'passport_photo',
        'resume',
    ];

    // Cast JSON fields to arrays
    protected $casts = [
        'aadhar_card' => 'array',
        'pan_card' => 'array',
        'resume' => 'array',
        'payslips' => 'array',
        'bank_proof' => 'array',
        'education_docs' => 'array',
        'salary_revision_letter' => 'array',
        'experience_letters' => 'array',
        'passport_photo' => 'array',
    ];

    public function salaryBreakup()
    {
        return $this->hasOne(InternalSalaryBreakup::class, 'candidate_id', 'id');
    }

    public function orfCreation()
    {
        return $this->hasOne(InternalOrfCreation::class, 'candidate_id', 'id');
    }
}
