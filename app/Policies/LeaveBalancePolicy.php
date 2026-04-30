<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveBalance;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaveBalancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'hr']);
    }

    public function view(User $user, LeaveBalance $leaveBalance): bool
    {
        return $user->hasRole(['admin', 'hr']) || $user->id === $leaveBalance->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'hr']);
    }

    public function update(User $user, LeaveBalance $leaveBalance): bool
    {
        return $user->hasRole(['admin', 'hr']);
    }

    public function delete(User $user, LeaveBalance $leaveBalance): bool
    {
        return $user->hasRole(['admin', 'hr']);
    }
}
