<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillApprovalWorkflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bill_id',
        'level_1_approver_id',
        'level_1_status',
        'level_1_comments',
        'level_1_approved_at',
        'level_2_approver_id',
        'level_2_status',
        'level_2_comments',
        'level_2_approved_at',
        'level_3_approver_id',
        'level_3_status',
        'level_3_comments',
        'level_3_approved_at',
        'level_4_approver_id',
        'level_4_status',
        'level_4_comments',
        'level_4_approved_at',
        'level_5_approver_id',
        'level_5_status',
        'level_5_comments',
        'level_5_approved_at',
        'current_level',
        'final_status',
        'final_approved_at',
    ];

    protected $casts = [
        'level_1_approved_at' => 'datetime',
        'level_2_approved_at' => 'datetime',
        'level_3_approved_at' => 'datetime',
        'level_4_approved_at' => 'datetime',
        'level_5_approved_at' => 'datetime',
        'final_approved_at' => 'datetime',
    ];

    /**
     * Get the bill that this workflow belongs to.
     */
    public function bill()
    {
        return $this->belongsTo(VendorBill::class, 'bill_id');
    }

    /**
     * Get the level 1 approver user.
     */
    public function level1Approver()
    {
        return $this->belongsTo(User::class, 'level_1_approver_id');
    }

    /**
     * Get the level 2 approver user.
     */
    public function level2Approver()
    {
        return $this->belongsTo(User::class, 'level_2_approver_id');
    }

    /**
     * Get the level 3 approver user.
     */
    public function level3Approver()
    {
        return $this->belongsTo(User::class, 'level_3_approver_id');
    }

    /**
     * Get the level 4 approver user.
     */
    public function level4Approver()
    {
        return $this->belongsTo(User::class, 'level_4_approver_id');
    }

    /**
     * Get the level 5 approver user.
     */
    public function level5Approver()
    {
        return $this->belongsTo(User::class, 'level_5_approver_id');
    }

    /**
     * Get the final approver user.
     */
    public function finalApprover()
    {
        return $this->belongsTo(User::class, 'final_approver_id');
    }
}
