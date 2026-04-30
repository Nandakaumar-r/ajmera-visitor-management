<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'email',
        'phone',
        'designation',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
