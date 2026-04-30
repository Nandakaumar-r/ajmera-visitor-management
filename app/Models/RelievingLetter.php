<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelievingLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'resignation_id',
        'letter_date',
        'content',
        'file_path',
        'status',
        'generated_at',
        'sent_at',
        'generated_by'
    ];

    protected $casts = [
        'letter_date' => 'date',
        'generated_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    public function resignation()
    {
        return $this->belongsTo(Resignation::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
