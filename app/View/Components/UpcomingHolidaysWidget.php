<?php

namespace App\View\Components;

use App\Models\Holiday;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UpcomingHolidaysWidget extends Component
{
    public $upcomingHolidays;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->upcomingHolidays = $this->getUpcomingHolidays();
    }

    /**
     * Fetch upcoming holidays.
     */
    public function getUpcomingHolidays()
    {
        return Holiday::where('date', '>=', now())->orderBy('date')->take(5)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.upcoming-holidays-widget', [
            'upcomingHolidays' => $this->upcomingHolidays,
        ]);
    }
}
