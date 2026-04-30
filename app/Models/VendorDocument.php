<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'document_type',
        'file_path',
        'file_name',
        'required',
        'verified',
        'verification_notes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'required' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the vendor that owns the document.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who verified the document.
     */
    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
