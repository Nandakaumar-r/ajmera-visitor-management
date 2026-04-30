<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrgChartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee information not found.');
        }

        // Get manager's information
        $manager = null;
        if ($employee->manager_id) {
            $manager = Employee::with(['designation', 'department'])
                ->where('employee_id', $employee->manager_id)
                ->first();
        }

        // Get team members (employees with the same manager)
        $teamMembers = Employee::with(['designation', 'department'])
            ->where('manager_id', $employee->manager_id)
            ->where('employee_id', '!=', $employee->employee_id)
            ->get();

        // Get subordinates (employees who report to the current employee)
        $subordinates = Employee::with(['designation', 'department'])
            ->where('manager_id', $employee->employee_id)
            ->get();

        // Get department colleagues (employees in the same department)
        $departmentColleagues = Employee::with(['designation', 'department'])
            ->where('employee_department', $employee->employee_department)
            ->where('employee_id', '!=', $employee->employee_id)
            ->where('manager_id', '!=', $employee->manager_id)
            ->get();

        return view('org-chart.index', compact(
            'employee',
            'manager',
            'teamMembers',
            'subordinates',
            'departmentColleagues'
        ));
    }
}
