<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PendingApprovalsWidget extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function getPendingApprovals($employeeId)
    {
        return [
            'leave_requests' => LeaveRequest::where('employee_id', $employeeId)->where('status', 'pending')->count(),
            'task_approvals' => TaskApproval::where('employee_id', $employeeId)->where('status', 'pending')->count(),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pending-approvals-widget');
    }
}
