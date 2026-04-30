<div class="bg-white p-4 rounded-lg shadow-md">
    <div class="flex items-center">
        <img src="https://ui-avatars.com/api/?name={{ $post->user->name }}&background=7E3AF2&color=fff&size=32&rounded=true" alt="Profile" class="w-10 h-10 rounded-full">
        <div class="ml-3">
            <p class="text-sm font-medium">{{ $post->user->name }}</p>
            <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
        </div>
    </div>

    <div class="mt-4">
        <p>{{ $post->content }}</p>
        @if($post->media_url)
            <img src="{{ $post->media_url }}" alt="Post Media" class="mt-3 rounded-lg w-full">
        @endif
    </div>

    <div class="mt-4 flex items-center">
        <button wire:click="toggleLike" class="text-blue-500 mr-4">
            @if($isLiked)
                Liked ({{ $likesCount }})
            @else
                Like ({{ $likesCount }})
            @endif
        </button>
        <button wire:click="toggleCommentSection" class="text-blue-500">
            Comment ({{ $post->comments->count() }})
        </button>
    </div>

    <!-- Comments Section -->
    @if($isCommentSectionVisible)
        <livewire:comment-section :post="$post" :key="$post->id . '-comments'" />
    @endif
</div>
