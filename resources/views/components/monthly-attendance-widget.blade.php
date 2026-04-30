<div class="bg-yellow-100 shadow rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Monthly Attendance</h3>
    <ul class="space-y-2">
        <li><strong>Days Present:</strong> {{ $monthlyAttendanceSummary['present'] }}</li>
        <li><strong>Days Absent:</strong> {{ $monthlyAttendanceSummary['absent'] }}</li>
        <li><strong>Days Late:</strong> {{ $monthlyAttendanceSummary['late'] }}</li>
    </ul>
</div>
