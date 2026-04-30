<?php

namespace App\Policies;

use App\Models\TravelReimbursement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TravelReimbursementPolicy
{
    use HandlesAuthorization;

    public function view(User $user, TravelReimbursement $reimbursement)
    {
        return $user->id === $reimbursement->user_id
            || $user->hasRole(['hr', 'admin']);
    }

    public function approveReimbursement(User $user, TravelReimbursement $reimbursement)
    {
        return $user->hasRole(['hr', 'admin'])
            && $reimbursement->status === 'pending';
    }

    public function rejectReimbursement(User $user, TravelReimbursement $reimbursement)
    {
        return $user->hasRole(['hr', 'admin'])
            && $reimbursement->status === 'pending';
    }
}
