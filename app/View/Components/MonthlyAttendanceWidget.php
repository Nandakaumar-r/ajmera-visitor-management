<?php

namespace App\View\Components;

use App\Models\Attendance;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MonthlyAttendanceWidget extends Component
{
    public $employeeId;
    public $monthlyAttendanceSummary;
    /**
     * Create a new component instance.
     */
    public function __construct($employeeId)
    {
        $this->monthlyAttendanceSummary = $this->getMonthlyAttendanceSummary($this->employeeId);
    }

    public function getMonthlyAttendanceSummary($employeeId)
    {
        return [
            'present' => Attendance::whereMonth('date', now()->month)
                ->where('employee_id', $employeeId)
                ->where('status', 'Present')->count(),
            'absent' => Attendance::whereMonth('date', now()->month)
                ->where('employee_id', $employeeId)
                ->where('status', 'Absent')->count(),
            'late' => Attendance::whereMonth('date', now()->month)
                ->where('employee_id', $employeeId)
                ->where('status', 'Late')->count(),
        ];
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.monthly-attendance-widget', [
            'monthlyAttendanceSummary' => $this->monthlyAttendanceSummary
        ]);
    }
}
