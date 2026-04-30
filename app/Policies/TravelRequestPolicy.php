<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TravelRequestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, TravelRequest $travelRequest)
    {
        return $user->id === $travelRequest->user_id
            || $user->id === $travelRequest->manager_id
            || $user->id === $travelRequest->cfo_id
            || $user->hasRole(['hr', 'admin']);
    }

    public function create(User $user)
    {
        return true; // All authenticated users can create travel requests
    }

    public function approveAsManager(User $user, TravelRequest $travelRequest)
    {
        return $user->id === $travelRequest->manager_id
            && $travelRequest->status === 'pending_manager';
    }

    public function approveAsCFO(User $user, TravelRequest $travelRequest)
    {
        return $user->hasRole('cfo')
            && $travelRequest->status === 'pending_cfo';
    }

    public function reject(User $user, TravelRequest $travelRequest)
    {
        return ($user->id === $travelRequest->manager_id && $travelRequest->status === 'pending_manager')
            || ($user->hasRole('cfo') && $travelRequest->status === 'pending_cfo');
    }

    public function updateBooking(User $user, TravelRequest $travelRequest)
    {
        return $user->hasRole(['hr', 'admin'])
            && $travelRequest->status === 'approved';
    }

    public function createReimbursement(User $user, TravelRequest $travelRequest)
    {
        return $user->id === $travelRequest->user_id
            && $travelRequest->status === 'booked';
    }
}
