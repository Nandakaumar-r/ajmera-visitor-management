<div class="bg-purple-100 shadow rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Performance Summary</h3>
    <ul>
        <li><strong>Tasks Completed:</strong> {{ $performance['completed_tasks'] }}</li>
        <li><strong>Goals Achieved:</strong> {{ $performance['goals_achieved'] }}</li>
        <li><strong>Feedback Score:</strong> {{ number_format($performance['feedback_score'], 1) }}</li>
    </ul>
</div>
