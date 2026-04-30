<div class="bg-white p-4 rounded-lg shadow mb-4">
    <h3 class="text-lg font-semibold mb-2">Shift Details</h3>
    <div class="text-sm">
        <p><strong>Shift:</strong> {{ $shift['name'] }}</p>
        <p><strong>Hours:</strong> {{ $shift['hours'] }}</p>
        <p><strong>Location:</strong> {{ $shift['location'] }}</p>
        <p><strong>Attendance Scheme:</strong> {{ $shift['scheme'] }}</p>
    </div>
    <h4 class="font-semibold mt-4">Session Details</h4>
    <div class="text-sm mt-2">
        <p><strong>Session 1:</strong> 09:30 - 13:30</p>
        <p><strong>Session 2:</strong> 14:00 - 18:30</p>
    </div>
</div>
