<div class="bg-blue-100 shadow rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Leave Balance</h3>
    <ul class="space-y-2">
        <li><strong>Annual Leave:</strong> {{ $leaveBalance['annual'] }}</li>
        <li><strong>Sick Leave:</strong> {{ $leaveBalance['sick'] }}</li>
        <li><strong>Casual Leave:</strong> {{ $leaveBalance['casual'] }}</li>
    </ul>
</div>
