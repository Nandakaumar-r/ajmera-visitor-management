<?php

namespace App\Livewire;

use App\Models\Attendance;
use Carbon\Carbon;
use Livewire\Component;

class AttendanceCalendar extends Component
{
    public $month;
    public $attendances = [];
    public $selectedDay;
    
    // Initializes the calendar with the current month
    public function mount()
    {
        $this->month = Carbon::now()->format('Y-m');
        $this->loadAttendances();
    }

    // Loads attendance data based on the selected month
    public function loadAttendances()
    {
        // Example: Load attendance data for the current month
        // Assuming you have an Attendance model or other way to fetch attendance data
        $this->attendances = \App\Models\Attendance::whereMonth('date', Carbon::parse($this->month)->month)
            ->whereYear('date', Carbon::parse($this->month)->year)
            ->get();
    }

    // Handle the click to view attendance on a specific day
    public function viewAttendance($day)
    {
        $this->selectedDay = $day;
        // Additional logic to show attendance for the day
    }

    // Handle next month navigation
    public function nextMonth()
    {
        $this->month = Carbon::parse($this->month)->addMonth()->format('Y-m');
        $this->loadAttendances();
    }

    // Handle previous month navigation
    public function prevMonth()
    {
        $this->month = Carbon::parse($this->month)->subMonth()->format('Y-m');
        $this->loadAttendances();
    }


    public function render()
    {
        return view('livewire.attendance-calendar');
    }
}
