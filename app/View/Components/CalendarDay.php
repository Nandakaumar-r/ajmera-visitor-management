<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CalendarDay extends Component
{
    public $attendance;

    /**
     * Create a new component instance.
     *
     * @param  $attendance
     */
    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render(): View|string
    {
        return view('components.calendar-day');
    }
}
