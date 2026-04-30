<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PerformanceWidget extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function getPerformanceSummary($employeeId)
    {
        return [
            'completed_tasks' => Task::where('employee_id', $employeeId)->where('status', 'completed')->count(),
            'goals_achieved' => Goal::where('employee_id', $employeeId)->where('achieved', true)->count(),
            'feedback_score' => Feedback::where('employee_id', $employeeId)->avg('score'),
        ];
    }    

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.performance-widget');
    }
}
