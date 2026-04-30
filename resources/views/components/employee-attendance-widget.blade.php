<div class="bg-white shadow rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Attendance Summary</h3>
    <ul class="space-y-2">
        <li><strong>Days Present:</strong> {{ $attendanceSummary['present'] }}</li>
        <li><strong>Days Absent:</strong> {{ $attendanceSummary['absent'] }}</li>
        <li><strong>Leave Balance:</strong> {{ $attendanceSummary['leave_balance'] }}</li>
    </ul>
</div>
