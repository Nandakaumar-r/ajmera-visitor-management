<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateCreation extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; // use second DB
    protected $table = 'users';       // users table in second DB

    protected $fillable = [
        'name',
        'email',
        'password', // or other fields
    ];
}