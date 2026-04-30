<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalOnboardingJoiningDoc extends Model
{
    use HasFactory;

    protected $table = 'internal_joining_doc';

    protected $fillable = [
        'candidate_id',
        'offer_letter',
        'acceptence_mail',
        'bgv',
        'epf',
        'gratuity',
        'joining_form',
        'nomination_declaration',
        'posh_ack',
    ];

    public function candidate()
    {
        return $this->belongsTo(InternalOnboardingCandidateDetails::class, 'candidate_id');
    }
}
