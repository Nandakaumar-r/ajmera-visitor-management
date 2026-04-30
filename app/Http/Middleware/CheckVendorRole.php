<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckVendorRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (!Auth::user()->hasRole('Vendor')) {
            // If user is not a vendor, redirect to appropriate dashboard based on role
            if (Auth::user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->hasRole('HR')) {
                return redirect()->route('hr.dashboard');
            } elseif (Auth::user()->hasRole('Finance')) {
                return redirect()->route('finance.dashboard');
            } elseif (Auth::user()->hasRole('Vendor')) {
                return redirect()->route('vendor.dashboard');
            }

            // Default redirect for other roles
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access the vendor portal.');
        }

        return $next($request);
    }
}
