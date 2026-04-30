<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Manager extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'department_id',
        'manager_name',
        'manager_email',
        'manager_id',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'manager_id', 'manager_id');
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail()
    {
        return $this->email; // Assuming your managers table has an email column
    }
}
