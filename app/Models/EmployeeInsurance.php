<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeInsurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'employee_name',
        'spouse_name',
        'spouse_dob',
        'spouse_aadhar',
        'spouse_gender',
        'child1_name',
        'child1_dob',
        'child1_aadhar',
        'child1_gender',
        'child2_name',
        'child2_dob',
        'child2_aadhar',
        'child2_gender',
        'premium',
        'status',
    ];

    protected $casts = [
        'spouse_dob' => 'date',
        'child1_dob' => 'date',
        'child2_dob' => 'date',
        'premium' => 'decimal:2',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Accessor for family members count
    public function getFamilyCountAttribute(): int
    {
        $count = 1; // Employee always counted
        
        if ($this->spouse_name) $count++;
        if ($this->child1_name) $count++;
        if ($this->child2_name) $count++;
        
        return $count;
    }

    // Scope for filtering by status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}

