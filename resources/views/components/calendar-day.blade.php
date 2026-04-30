<div class="p-2 border rounded-lg">
    <h4 class="font-semibold">{{ \Carbon\Carbon::parse($attendance->date)->format('d') }}</h4>
    <p class="text-xs text-gray-600">{{ $attendance->shift }}</p>
    <p class="text-xs text-gray-600">Hours: {{ $attendance->total_work_hours ?? '-' }}</p>
    <p class="text-xs text-gray-600">&nbsp;</p>
</div>
