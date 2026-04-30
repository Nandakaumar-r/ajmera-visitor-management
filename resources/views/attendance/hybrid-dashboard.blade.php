@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Hybrid Attendance Dashboard
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

    <!-- Work Mode Selection -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form id="workModeForm" action="{{ route('attendance.mode') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Work Mode</label>
                <select name="work_mode" id="work_mode" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600" onchange="this.form.submit()">
                    <option value="" selected>Select work mode</option>
                    <option value="WFO" {{ $currentMode === 'WFO' ? 'selected' : '' }}>Work from Office (WFO)</option>
                    <option value="WFH" {{ $currentMode === 'WFH' ? 'selected' : '' }}>Work from Home (WFH)</option>
                </select>
            </div>
        </form>
    </div>

    <!-- WFH Time Logging Section -->
    @if($currentMode === 'WFH')
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Log Working Hours</h2>
        <form id="wfhForm" action="{{ route('attendance.log-wfh') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                    <input type="time" name="start_time" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                    <input type="time" name="end_time" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600">
                </div>
            </div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Submit Attendance
            </button>
        </form>
    </div>
    @endif

    <!-- Today's Attendance Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Work Mode</div>
            <div class="mt-1 text-2xl font-semibold {{ $currentMode === 'WFH' ? 'text-blue-600' : 'text-green-600' }}">
                {{ $currentMode }}
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Hours</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $todayHours ?? '0.00' }}
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</div>
            <div class="mt-1 text-2xl font-semibold {{ $status === 'present' ? 'text-green-600' : 'text-red-600' }}">
                {{ ucfirst($status ?? 'Not Logged') }}
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</div>
            <div class="mt-1 text-sm text-gray-900 dark:text-white truncate">
                {{ $location ?? 'Not Captured' }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get geolocation when submitting WFH form
    const wfhForm = document.getElementById('wfhForm');
    if (wfhForm) {
        wfhForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    wfhForm.submit();
                }, function(error) {
                    alert('Error getting location. Please enable location services.');
                });
            } else {
                alert('Geolocation is not supported by your browser');
            }
        });
    }
});
</script>
@endpush
@endsection
