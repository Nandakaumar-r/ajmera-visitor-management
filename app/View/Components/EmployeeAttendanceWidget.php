<?php

namespace App\View\Components;

use App\Models\Attendance;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EmployeeAttendanceWidget extends Component
{
    public $attendanceSummary;

    /**
     * Create a new component instance.
     */
    public function __construct($employeeId)
    {
        $this->attendanceSummary = $this->getAttendanceSummary($employeeId);
    }

    public function getAttendanceSummary($employeeId)
    {
        return [
            'present' => Attendance::where('employee_id', $employeeId)->where('status', 'Present')->count(),
            'absent' => Attendance::where('employee_id', $employeeId)->where('status', 'Absent')->count(),
            'leave_balance' => 10, // Example static leave balance, fetch as needed
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.employee-attendance-widget');
    }
}
