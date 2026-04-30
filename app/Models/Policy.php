<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'description',
        'content',
        'document_path',
        'requires_acknowledgment',
        'effective_from',
        'effective_to',
        'version',
        'created_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'requires_acknowledgment' => 'boolean',
    ];

    public const CATEGORIES = [
        'leave' => 'Leave Policy',
        'code_of_conduct' => 'Code of Conduct',
        'it_security' => 'IT Security',
        'hr' => 'HR Policies',
        'travel' => 'Travel Policy',
        'expense' => 'Expense Policy',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function acknowledgments()
    {
        return $this->hasMany(PolicyAcknowledgment::class);
    }

    public function acknowledgedBy()
    {
        return $this->belongsToMany(User::class, 'policy_acknowledgments')
            ->withTimestamps()
            ->withPivot(['acknowledged_at', 'ip_address', 'user_agent']);
    }

    public function getCategoryDisplayAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getStatusAttribute()
    {
        $now = now();
        
        if ($this->effective_from > $now) {
            return 'upcoming';
        }
        
        if ($this->effective_to && $this->effective_to < $now) {
            return 'expired';
        }
        
        return 'active';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'upcoming' => 'bg-blue-100 text-blue-800',
            'active' => 'bg-green-100 text-green-800',
            'expired' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            });
    }
}
