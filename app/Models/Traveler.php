<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traveler extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_request_id',
        'name',
        'age',
        'passport_number',
        'passport_expiry',
        'employee_id',
        'additional_details',
    ];

    protected $casts = [
        'additional_details' => 'array',
        'passport_expiry' => 'date',
    ];

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }
}
