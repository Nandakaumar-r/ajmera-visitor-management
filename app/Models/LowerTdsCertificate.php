<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LowerTdsCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'certificate_number',
        'start_date',
        'end_date',
        'rate_percentage',
        'max_value',
        'file_path',
        'file_name',
        'file_size',
        'file_mime_type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rate_percentage' => 'decimal:2',
        'max_value' => 'decimal:2',
    ];

    /**
     * Get the vendor that owns this certificate.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
