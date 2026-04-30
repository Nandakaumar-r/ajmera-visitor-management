<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'action_type',
        'description',
        'effective_from',
        'effective_to',
        'severity_level',
        'improvement_plan',
        'consequences',
        'status',
        'issued_by',
        'document_path',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public const ACTION_TYPES = [
        'warning' => 'Warning',
        'pip' => 'Performance Improvement Plan',
        'termination' => 'Termination',
    ];

    public const SEVERITY_LEVELS = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getActionTypeDisplayAttribute()
    {
        return self::ACTION_TYPES[$this->action_type] ?? $this->action_type;
    }

    public function getSeverityLevelDisplayAttribute()
    {
        return self::SEVERITY_LEVELS[$this->severity_level] ?? $this->severity_level;
    }

    public function getSeverityBadgeAttribute()
    {
        return match($this->severity_level) {
            'low' => 'bg-yellow-100 text-yellow-800',
            'medium' => 'bg-orange-100 text-orange-800',
            'high' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'bg-red-100 text-red-800',
            'resolved' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
