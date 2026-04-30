<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'form_code',
        'description',
        'version',
        'file_path',
        'category',
        'requires_approval',
        'status'
    ];

    protected $casts = [
        'requires_approval' => 'boolean'
    ];
}
