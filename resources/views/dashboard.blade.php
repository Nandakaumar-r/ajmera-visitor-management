@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Hours This Month</p>
                        <p class="text-lg font-semibold">160.5 hrs</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Attendance Rate</p>
                        <p class="text-lg font-semibold">98%</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Leave Balance</p>
                        <p class="text-lg font-semibold">12 days</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Pending Tasks</p>
                        <p class="text-lg font-semibold">5</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Attendance Section -->
            <div class="md:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Today's Attendance</h2>
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-4">
                            <div>
                                <div id="current-time" class="text-3xl font-bold mb-2"></div>
                                <div id="current-date" class="text-gray-600"></div>
                            </div>
                            <div id="attendance-status" class="text-right">
                                <div id="check-in-time" class="mb-2"></div>
                                <div id="check-out-time" class="mb-2"></div>
                                <div id="total-hours" class="font-semibold"></div>
                            </div>
                        </div>
                        
                        <div class="flex gap-4">
                            <select id="work-type" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="office">Office</option>
                                <option value="wfh">Work From Home</option>
                            </select>
                            <button id="check-in-btn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                                Check In
                            </button>
                            <button id="check-out-btn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                                Check Out
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">Leave Request Approved</p>
                                    <p class="text-sm text-gray-500">Your leave request for Jan 20-21 has been approved</p>
                                </div>
                                <div class="ml-auto text-sm text-gray-500">2h ago</div>
                            </div>
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium">Checked In</p>
                                    <p class="text-sm text-gray-500">You checked in at 9:00 AM</p>
                                </div>
                                <div class="ml-auto text-sm text-gray-500">5h ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                <!-- Calendar -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Calendar</h2>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-medium">January 2025</h3>
                                <div class="flex gap-2">
                                    <button class="p-1 hover:bg-gray-200 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <button class="p-1 hover:bg-gray-200 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Calendar grid would go here -->
                            <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                <div class="text-gray-500">Su</div>
                                <div class="text-gray-500">Mo</div>
                                <div class="text-gray-500">Tu</div>
                                <div class="text-gray-500">We</div>
                                <div class="text-gray-500">Th</div>
                                <div class="text-gray-500">Fr</div>
                                <div class="text-gray-500">Sa</div>
                                <!-- Calendar days would be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Announcements</h2>
                        <div class="space-y-4">
                            <div class="border-l-4 border-blue-500 pl-4">
                                <p class="font-medium">Team Meeting</p>
                                <p class="text-sm text-gray-500">Weekly sync on Friday at 3 PM</p>
                                <p class="text-xs text-gray-400 mt-1">Posted 1 day ago</p>
                            </div>
                            <div class="border-l-4 border-yellow-500 pl-4">
                                <p class="font-medium">Holiday Notice</p>
                                <p class="text-sm text-gray-500">Office will be closed on January 26th</p>
                                <p class="text-xs text-gray-400 mt-1">Posted 2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString();
        document.getElementById('current-date').textContent = now.toLocaleDateString();
    }

    function updateAttendanceStatus() {
        fetch('/attendance/status')
            .then(response => response.json())
            .then(data => {
                const checkInTime = document.getElementById('check-in-time');
                const checkOutTime = document.getElementById('check-out-time');
                const totalHours = document.getElementById('total-hours');
                const checkInBtn = document.getElementById('check-in-btn');
                const checkOutBtn = document.getElementById('check-out-btn');
                const workType = document.getElementById('work-type');

                if (data.checked_in) {
                    checkInTime.textContent = `Checked in at: ${data.check_in_time}`;
                    checkInBtn.disabled = true;
                    checkInBtn.classList.add('opacity-50');
                    workType.disabled = true;
                } else {
                    checkInTime.textContent = 'Not checked in';
                    checkInBtn.disabled = false;
                    checkInBtn.classList.remove('opacity-50');
                    workType.disabled = false;
                }

                if (data.checked_out) {
                    checkOutTime.textContent = `Checked out at: ${data.check_out_time}`;
                    checkOutBtn.disabled = true;
                    checkOutBtn.classList.add('opacity-50');
                } else {
                    checkOutTime.textContent = data.checked_in ? 'Not checked out' : '';
                    checkOutBtn.disabled = !data.checked_in;
                    checkOutBtn.classList.toggle('opacity-50', !data.checked_in);
                }

                if (data.total_hours !== null) {
                    totalHours.textContent = `Total hours: ${data.total_hours}`;
                } else {
                    totalHours.textContent = '';
                }

                if (data.work_type) {
                    workType.value = data.work_type;
                }
            });
    }

    function markAttendance(action) {
        const workType = document.getElementById('work-type').value;
        fetch('/attendance/mark', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ action, work_type: workType })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                updateAttendanceStatus();
            }
        });
    }

    // Update time every second
    setInterval(updateTime, 1000);
    updateTime();

    // Update attendance status every minute
    updateAttendanceStatus();
    setInterval(updateAttendanceStatus, 60000);

    // Add event listeners
    document.getElementById('check-in-btn').addEventListener('click', () => markAttendance('in'));
    document.getElementById('check-out-btn').addEventListener('click', () => markAttendance('out'));

    document.addEventListener('DOMContentLoaded', function() {
        // Update current time every second
        function updateCurrentTime() {
            const now = new Date();
            document.getElementById('current-time-2').textContent = now.toLocaleTimeString();
        }
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);

        // Handle work type change
        const workTypeSelect = document.getElementById('work_type');
        const formWorkType = document.getElementById('form_work_type');
        workTypeSelect.addEventListener('change', function() {
            formWorkType.value = this.value;
        });

        // Check current attendance status
        fetch('{{ route("attendance.status") }}')
            .then(response => response.json())
            .then(data => {
                if (data.checked_in) {
                    document.getElementById('check-in-btn-2').disabled = true;
                    document.getElementById('check-out-btn-2').disabled = false;
                    document.getElementById('work_type').disabled = true;
                    document.getElementById('attendance-status-2').textContent = 'Checked In';
                    document.getElementById('check-in-time-2').textContent = data.check_in_time;
                }
                if (data.checked_out) {
                    document.getElementById('check-out-btn-2').disabled = true;
                    document.getElementById('attendance-status-2').textContent = 'Checked Out';
                    document.getElementById('check-out-time-2').textContent = data.check_out_time;
                    document.getElementById('total-hours-2').textContent = data.total_hours;
                }
            });
    });
</script>
@endpush
@endsection