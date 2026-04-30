@extends('layouts.dashboard')

@section('content')
<div class="flex">
    <!-- Calendar Section -->
    <div class="w-3/4 p-4">
        <h2 class="text-2xl font-semibold mb-4">Attendance Calendar - {{ now()->format('F Y') }}</h2>

        <!-- Stats Overview -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            @php
                $avgWorkHours = $attendances->avg('scheduled_work_hours') ?? '-';
                $avgActualWorkHours = $attendances->avg('actual_work_hours') ?? '-';
                $penaltyDays = $attendances->where('status', 'penalty')->count() ?? 0;
            @endphp
            <x-stat-box title="Avg. Work Hrs" value="{{ $avgWorkHours }}" />
            <x-stat-box title="Avg. Actual Work Hrs" value="{{ $avgActualWorkHours }}" />
            <x-stat-box title="Penalty Days" value="{{ $penaltyDays }}" />
        </div>

        <!-- Calendar -->
        <livewire:attendance-calendar :attendances="$attendances" :shift="['name' => 'General Shift', 'hours' => '09:30 - 18:30', 'location' => 'Head Office', 'scheme' => 'Default']" />

        <!-- Legends Section -->
       

    </div>

    <!-- Side Panel for Shift Details and Legends -->
    <div class="w-1/4 p-4 border-l border-gray-200">
        <!-- Shift Details -->
        <x-shift-details :shift="['name' => 'General Shift', 'hours' => '09:30 - 18:30', 'location' => 'Head Office', 'scheme' => 'Default']" />

        <!-- Daily Attendance Details -->
        <x-attendance-details :date="now()->format('d F Y')" />
    </div>
</div>
@endsection
