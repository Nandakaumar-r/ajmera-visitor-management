<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'contact_information',
        'purpose_of_visit',
        'whom_to_visit',
        'government_id_type',
        'government_id_last_digits',
        'additional_details',
        'photo_path',
        'signature_path',
        'status',
        'rejection_reason',
        'check_in_time',
        'check_out_time',
        'created_by',
        'approved_by',
        'visiting_card_path',
        'visiting_card_ocr_text',
        'visiting_card_data',
        'company',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'visiting_card_data' => 'array'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function logs()
    {
        return $this->hasMany(VisitorLog::class);
    }

}
