<?php

namespace App\Providers;

use App\Models\TravelRequest;
use App\Models\LeaveBalance;
use App\Policies\TravelRequestPolicy;
use App\Policies\LeaveBalancePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\CabinBooking;
use App\Policies\CabinBookingPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TravelRequest::class => TravelRequestPolicy::class,
        CabinBooking::class => CabinBookingPolicy::class,
        LeaveBalance::class => LeaveBalancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
