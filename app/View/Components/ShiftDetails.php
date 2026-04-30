<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShiftDetails extends Component
{
    public $shift;

    /**
     * Create a new component instance.
     *
     * @param  array  $shift
     */
    public function __construct($shift)
    {
        $this->shift = $shift;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render(): View|string
    {
        return view('components.shift-details');
    }
}
