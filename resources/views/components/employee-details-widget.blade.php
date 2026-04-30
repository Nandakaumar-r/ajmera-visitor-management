<div class="bg-indigo-100 shadow rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Employee Details</h3>
    <ul class="space-y-2">
        <li><strong>Department:</strong> {{ $employee->department->name }}</li>
        <li><strong>Position:</strong> {{ $employee->position }}</li>
        <li><strong>Manager:</strong> {{ $employee->manager->name }}</li>
    </ul>
</div>
