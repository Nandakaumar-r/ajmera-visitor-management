<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class HelpRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category',
        'subject',
        'description',
        'user_id',
        'status',
        'priority',
        'attachment_path',
        'closed_by',
        'closed_at'
    ];

    protected $casts = [
        'closed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
