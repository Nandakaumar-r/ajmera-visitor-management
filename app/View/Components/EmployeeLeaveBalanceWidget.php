<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EmployeeLeaveBalanceWidget extends Component
{
    public $employeeId;
    public $leaveBalance;

    /**
     * Create a new component instance.
     */
    public function __construct($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->leaveBalance = $this->getLeaveBalance($employeeId);
    }

    /**
     * Fetch leave balance for the employee.
     */
    public function getLeaveBalance($employeeId)
    {
        // Replace with dynamic data fetching logic as needed.
        return [
            'annual' => 10,
            'sick' => 5,
            'casual' => 3,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.employee-leave-balance-widget', [
            'leaveBalance' => $this->leaveBalance
        ]);
    }
}
