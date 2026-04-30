<div class="mt-4 bg-gray-50 p-4 rounded-lg">
    <h4 class="text-sm font-semibold mb-2">Comments</h4>
    
    @foreach($comments as $comment)
        <div class="mb-2">
            <p class="text-sm"><strong>{{ $comment->user->name }}</strong>: {{ $comment->comment }}</p>
            <p class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
        </div>
    @endforeach

    <!-- Add Comment Form -->
    <div class="mt-3">
        <input type="text" wire:model="commentText" placeholder="Write a comment..." class="w-full border rounded p-2 mb-2">
        <button wire:click="addComment" class="bg-blue-500 text-white py-1 px-3 rounded">Post</button>
    </div>
</div>
