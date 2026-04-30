<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFromHome extends Model
{
    use HasFactory;

    protected $table = 'work_from_home';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'work_location',
        'current_location',
        'remarks',
        'captured_photo_path',
        'latitude',
        'longitude',
        'sign_in_time',
        'sign_out_time',
    ];


    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sign_in_time'  => 'datetime',
        'sign_out_time' => 'datetime',
    ];

    /**
     * Relationship with User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
