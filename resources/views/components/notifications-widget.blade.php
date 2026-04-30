<div class="bg-red-100 shadow rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Recent Notifications</h3>
    <ul>
        @foreach($notifications as $notification)
            <li>{{ $notification->message }} - <span class="text-gray-600">{{ $notification->created_at->diffForHumans() }}</span></li>
        @endforeach
    </ul>
</div>
