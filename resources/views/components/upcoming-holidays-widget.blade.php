<div class="bg-green-100 shadow rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Upcoming Holidays</h3>
    <ul class="space-y-2">
        @forelse ($upcomingHolidays as $holiday)
            <li>
                <strong>{{ $holiday->name }}</strong> - {{ $holiday->date->format('d M, Y') }}
            </li>
        @empty
            <li>No upcoming holidays.</li>
        @endforelse
    </ul>
</div>
