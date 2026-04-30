<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'status',
        'comments',
        'changed_by',
    ];

    /**
     * Get the bill that owns this status history.
     */
    public function bill()
    {
        return $this->belongsTo(VendorBill::class);
    }

    /**
     * Get the user who changed the status.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
