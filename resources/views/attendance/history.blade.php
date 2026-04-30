@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Attendance History
        </h1>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Present Days</div>
            <div class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ $stats['present'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Leave Days</div>
            <div class="mt-1 text-2xl font-semibold text-red-600 dark:text-red-400">{{ $stats['leave'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Half Days</div>
            <div class="mt-1 text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['half_day'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Work From Home</div>
            <div class="mt-1 text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $stats['wfh'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Attendance %</div>
            <div class="mt-1 text-2xl font-semibold text-purple-600 dark:text-purple-400">{{ $stats['attendance_percentage'] }}%</div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4 sm:mb-0">
                    {{ $months[$month] }} {{ $year }}
                </h2>
                <div class="flex flex-col sm:flex-row gap-2">
                    <form method="GET" action="{{ route('attendance.history') }}" class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                        <select name="month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @foreach($months as $key => $value)
                                <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full sm:w-auto text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Filter
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200 rounded-lg overflow-hidden">
                {{-- Calendar header --}}
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                    <div class="bg-gray-50 p-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $dayName }}
                    </div>
                @endforeach

                {{-- Calendar days --}}
                @foreach($calendar as $day)
                    @php
                        $isToday = $day['isToday'];
                        $attendance = $day['attendance'];
                        $status = $attendance ? $attendance->status : null;
                        
                        // Define status colors
                        $statusColors = [
                            'present' => 'bg-green-100 text-green-800 border-green-200',
                            'leave' => 'bg-red-100 text-red-800 border-red-200',
                            'half_day' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'wfh' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'pending' => 'bg-gray-100 text-gray-800 border-gray-200',
                        ];
                        
                        $baseClass = $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50 opacity-50';
                        $statusClass = $status ? ($statusColors[$status] ?? $baseClass) : $baseClass;
                        if ($day['isWeekend']) {
                            $statusClass = 'bg-gray-50';
                        }
                        if ($isToday) {
                            $statusClass = 'bg-blue-50';
                        }
                    @endphp
                    
                    <div class="min-h-[100px] p-2 {{ $statusClass }} border">
                        <div class="flex flex-col h-full">
                            <div class="flex items-center justify-between">
                                <span class="text-sm {{ $isToday ? 'font-bold text-blue-600' : 'text-gray-700' }}">
                                    {{ $day['day'] }}
                                </span>
                                @if($status)
                                    <span class="text-xs px-2 py-1 rounded-full capitalize {{ $statusColors[$status] }}">
                                        {{ $status }}
                                    </span>
                                @endif
                            </div>
                            @if($attendance)
                                <div class="mt-2 space-y-1 text-xs text-gray-600">
                                    @if($attendance->work_mode)
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $attendance->work_mode === 'WFH' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $attendance->work_mode }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($attendance->first_in)
                                        <div>In: {{ Carbon\Carbon::parse($attendance->first_in)->format('H:i') }}</div>
                                    @endif
                                    @if($attendance->last_out)
                                        <div>Out: {{ Carbon\Carbon::parse($attendance->last_out)->format('H:i') }}</div>
                                    @endif
                                    @if($attendance->total_hours)
                                        <div>Hours: {{ number_format($attendance->total_hours, 2) }}</div>
                                    @endif
                                    @if($attendance->location_address)
                                        <div class="text-xs text-gray-500 truncate" title="{{ $attendance->location_address }}">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ Str::limit($attendance->location_address, 30) }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
