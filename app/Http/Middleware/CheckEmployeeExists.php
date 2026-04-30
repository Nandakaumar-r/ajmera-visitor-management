<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class CheckEmployeeExists
{
    public function handle(Request $request, Closure $next)
    {
        $employee = Employee::where('employee_email', Auth::user()->email)->first();

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee record not found. Please contact HR to set up your employee profile.');
        }

        // Add employee to the request so controllers don't need to look it up again
        $request->merge(['employee' => $employee]);

        return $next($request);
    }
}
