<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHrRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('HR')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized. HR access required.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Unauthorized. HR access required.');
        }

        return $next($request);
    }
}
