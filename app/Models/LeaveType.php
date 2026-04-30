<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description'];

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }
}
