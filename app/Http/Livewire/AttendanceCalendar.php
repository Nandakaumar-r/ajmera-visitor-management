<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class AttendanceCalendar extends Component
{
    public $month;
    public $attendances;
    public $shift;

    public function mount($attendances, $shift)
    {
        $this->month = now();
        $this->attendances = $attendances;
        $this->shift = $shift;
    }

    public function prevMonth()
    {
        $this->month = Carbon::parse($this->month)->subMonth();
    }

    public function nextMonth()
    {
        $this->month = Carbon::parse($this->month)->addMonth();
    }

    public function viewAttendance($day)
    {
        $this->emit('daySelected', $day);
    }

    public function render()
    {
        return view('livewire.attendance-calendar');
    }
}
