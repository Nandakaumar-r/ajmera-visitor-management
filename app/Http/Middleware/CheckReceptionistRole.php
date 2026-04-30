<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckReceptionistRole
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->hasRole('reception')) {
            return redirect()->route('dashboard')->with('error', 'Access denied. You must be a receptionist to perform this action.');
        }

        return $next($request);
    }
}
