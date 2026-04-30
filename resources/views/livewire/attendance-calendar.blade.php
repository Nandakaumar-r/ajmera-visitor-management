<div>
    <!-- Calendar Navigation -->
    <div class="flex justify-between items-center mb-4">
        <button wire:click="prevMonth" class="text-blue-500">&lt; Prev</button>
        <h3 class="text-xl font-semibold text-gray-800">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</h3>
        <button wire:click="nextMonth" class="text-blue-500">Next &gt;</button>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-2 text-center">
        <!-- Days of the week -->
        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="font-semibold text-gray-700">{{ $day }}</div>
        @endforeach

        <!-- First blank days for the starting week -->
        @php
            $startOfMonth = \Carbon\Carbon::parse($month)->startOfMonth(); // Get first day of the month
            $startDay = $startOfMonth->dayOfWeek; // Get the day of the week for the first day (0 for Sunday, 6 for Saturday)
        @endphp

        <!-- Fill the leading empty days in the calendar -->
        @for ($i = 0; $i < $startDay; $i++)
            <div></div> <!-- Empty cell for the leading days -->
        @endfor

        <!-- Calendar Days with Attendance Data -->
        @foreach (range(1, \Carbon\Carbon::parse($month)->daysInMonth) as $day)
            @php
                $currentDay = \Carbon\Carbon::parse($month)->day($day);
            @endphp
            <a href="#" wire:click="viewAttendance({{ $day }})"
                class="block p-2 rounded-md
                @if ($currentDay->isToday()) bg-blue-500 text-white font-bold @elseif ($currentDay->isWeekend()) bg-gray-200 text-gray-700 @else bg-white text-gray-800 @endif
                hover:bg-blue-100">
                <div>{{ $day }}</div>
                @foreach ($attendances as $attendance)
                    @if ($attendance->date->day == $day)
                        <div>{{ $attendance->first_in }} - {{ $attendance->last_out }}</div>
                    @endif
                @endforeach
            </a>
        @endforeach

        <!-- Fill the trailing empty days in the calendar -->
        @php
            $totalDaysInMonth = \Carbon\Carbon::parse($month)->daysInMonth;
            $endDay = ($startDay + $totalDaysInMonth) % 7;
        @endphp

        @for ($i = $endDay; $i < 7; $i++)
            <div></div> <!-- Empty cell for the trailing days -->
        @endfor
    </div>

    <!-- Modal or Popup to display selected day attendance details -->
    @if($selectedDay)
        <div class="mt-4 p-4 border rounded-lg">
            <h3 class="font-semibold text-xl">Attendance for {{ $selectedDay }}</h3>
            <!-- Show details for the selected day -->
            @foreach ($attendances as $attendance)
                @if ($attendance->date->day == $selectedDay)
                    <div>{{ $attendance->status }} - {{ $attendance->remarks }}</div>
                @endif
            @endforeach
        </div>
    @endif
</div>
