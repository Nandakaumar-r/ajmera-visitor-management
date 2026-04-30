<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AttendanceDetails extends Component
{
    public $date;

    /**
     * Create a new component instance.
     *
     * @param  string  $date
     */
    public function __construct($date)
    {
        $this->date = $date;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render(): View|string
    {
        return view('components.attendance-details');
    }
}
