<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden my-4" id="post-{{ $post->id }}">
    <!-- Post Header -->
    <div class="p-4 sm:p-6">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                    <span class="text-white text-lg font-semibold">{{ substr($post->user->name, 0, 1) }}</span>
                </div>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $post->user->name }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $post->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Post Content -->
        <div class="mt-4">
            <p class="text-gray-900 dark:text-gray-100">
                {{ $post->content }}
            </p>
        </div>

        <!-- Post Image -->
        @if($post->photo)
            <div class="mt-4 -mx-6">
                <img src="{{ Storage::url($post->photo) }}" 
                     alt="Post image" 
                     class="w-full object-cover max-h-[32rem]" 
                     loading="lazy">
            </div>
        @endif

        <!-- Link Preview -->
        @if($post->hasMetaData())
            <div class="mt-4">
                <a href="{{ $post->meta_url }}" target="_blank" class="block">
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        @if($post->meta_image)
                            <img src="{{ $post->meta_image }}" 
                                 alt="{{ $post->meta_title }}" 
                                 class="w-full h-48 object-cover" 
                                 loading="lazy">
                        @endif
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $post->meta_title }}</h3>
                            @if($post->meta_description)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($post->meta_description, 150) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endif

        <!-- Post Actions -->
        <div class="mt-4 flex items-center space-x-4">
            <button onclick="toggleLike({{ $post->id }})" 
                    class="flex items-center text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">
                <svg class="h-5 w-5 {{ $post->likedBy(auth()->user()) ? 'text-indigo-600 dark:text-indigo-400' : '' }}" 
                     fill="{{ $post->likedBy(auth()->user()) ? 'currentColor' : 'none' }}" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" 
                          stroke-linejoin="round" 
                          stroke-width="2" 
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="ml-2 likes-count">{{ $post->likes()->count() }}</span>
            </button>
            
            <button onclick="toggleComments({{ $post->id }})" 
                    class="flex items-center text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 comment-button"
                    data-post-id="{{ $post->id }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" 
                          stroke-linejoin="round" 
                          stroke-width="2" 
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span class="ml-2 comments-count">{{ $post->comments->count() }}</span>
            </button>
        </div>
    </div>

    <!-- Comments Section -->
    <div id="comments-{{ $post->id }}" class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 {{ $post->comments->count() > 0 ? '' : 'hidden' }}">
        <div class="p-4 sm:p-6 space-y-4">
            <!-- Comments Container -->
            <div class="comments-container">
                @foreach($post->comments->whereNull('parent_id') as $comment)
                    <div class="comment-thread mb-4" id="comment-{{ $comment->id }}">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-gray-400 dark:bg-gray-600 flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">{{ substr($comment->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $comment->user->name }}
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>
                                <div class="mt-2 flex items-center space-x-4">
                                    <button onclick="showReplyForm({{ $comment->id }})" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        Reply
                                    </button>
                                    @if($comment->user_id === auth()->id())
                                        <button onclick="deleteComment({{ $comment->id }})" class="text-sm text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                                <!-- Reply Form -->
                                <div id="reply-form-{{ $comment->id }}" class="mt-3 hidden">
                                    <form onsubmit="submitComment(event, {{ $post->id }}, {{ $comment->id }})">
                                        <textarea name="comment" rows="2"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                                            placeholder="Write a reply..."></textarea>
                                        <div class="mt-2 flex justify-end space-x-2">
                                            <button type="button" onclick="hideReplyForm({{ $comment->id }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-700">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Reply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Nested Comments -->
                        @if($comment->replies->count() > 0)
                            <div class="ml-8 mt-3 space-y-3">
                                @foreach($comment->replies as $reply)
                                    <div class="flex items-start space-x-3" id="comment-{{ $reply->id }}">
                                        <div class="flex-shrink-0">
                                            <div class="h-7 w-7 rounded-full bg-gray-400 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-white text-xs font-semibold">{{ substr($reply->user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $reply->user->name }}
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                            </p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $reply->comment }}</p>
                                            @if($reply->user_id === auth()->id())
                                                <div class="mt-1">
                                                    <button onclick="deleteComment({{ $reply->id }})" class="text-xs text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400">
                                                        Delete
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Add Comment Form -->
            <form onsubmit="submitComment(event, {{ $post->id }})" class="mt-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <textarea name="comment" rows="2"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm"
                            placeholder="Write a comment..."></textarea>
                        <div class="mt-2 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
