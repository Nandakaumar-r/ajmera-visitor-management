<div class="relative">
    <button wire:click="toggleNotifications" class="text-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14V9a6 6 0 00-12 0v5a2.032 2.032 0 01-.595 1.595L4 17h5m6 0a3 3 0 11-6 0" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 text-xs bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center">{{ $unreadCount }}</span>
        @endif
    </button>

    @if($showNotifications)
        <div class="absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-lg">
            <div class="p-4">
                <h4 class="font-semibold text-gray-800">Notifications</h4>
                <ul class="mt-2">
                    @foreach($notifications as $notification)
                        <li class="py-2 border-b">
                            <p class="text-sm">{{ $notification->message }}</p>
                            <button wire:click="markAsRead({{ $notification->id }})" class="text-xs text-blue-500">Mark as Read</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
