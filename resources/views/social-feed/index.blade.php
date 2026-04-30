<x-social-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Social Feed') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="flex space-x-6">
                <!-- Main Feed Column -->
                <div class="flex-1 max-w-3xl">
                    <!-- Create Post Form -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <form action="{{ route('posts.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                                @csrf
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Share your thoughts</label>
                                    <textarea name="content" id="content" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" placeholder="What's on your mind?"></textarea>
                                </div>
                                <div>
                                    <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Share a link</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <input type="url" name="url" id="url" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" 
                                            placeholder="https://www.linkedin.com/post/...">
                                    </div>
                                </div>
                                <!-- <div>
                                    <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Add a photo</label>
                                    <input type="file" name="photo" id="photo" accept="image/*"
                                        class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100
                                        dark:file:bg-gray-700 dark:file:text-gray-300">
                                </div> -->
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Post
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Posts List -->
                    <div id="posts-container">
                        @include('social-feed.partials.posts')
                    </div>

                    <!-- Load More Button -->
                    @if($posts->hasMorePages())
                        <div class="flex justify-center mt-6">
                            <button onclick="loadMore({{ $posts->currentPage() + 1 }})" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Load More
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Right Sidebar -->
                <div class="hidden lg:block w-96 space-y-6">
                    <!-- Grid Layout for Widgets -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Birthdays Widget -->
                        <div class="col-span-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    🎂 Today's Birthdays
                                </h3>
                                <div class="space-y-3">
                                    @forelse($birthdays ?? [] as $user)
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                    <span class="text-indigo-700 dark:text-indigo-300 text-sm font-semibold">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $user->name }}
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No birthdays today</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Events Widget -->
                        <div class="col-span-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    📅 Upcoming Events
                                </h3>
                                <div class="space-y-4">
                                    @forelse($events ?? [] as $event)
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900 flex flex-col items-center justify-center">
                                                    <span class="text-indigo-700 dark:text-indigo-300 text-sm font-bold">
                                                        {{ \Carbon\Carbon::parse($event->date)->format('M') }}
                                                    </span>
                                                    <span class="text-indigo-700 dark:text-indigo-300 text-lg font-bold">
                                                        {{ \Carbon\Carbon::parse($event->date)->format('d') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $event->title }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($event->date)->format('g:i A') }}
                                                </p>
                                                @if($event->location)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        📍 {{ $event->location }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming events</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        @foreach($quickLinks ?? [] as $link)
                            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                                <a href="{{ $link->url }}" 
                                   class="flex flex-col items-center justify-center h-full space-y-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 rounded-lg p-3">
                                    <span class="text-2xl">{{ $link->icon }}</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                                        {{ $link->title }}
                                    </span>
                                </a>
                            </div>
                        @endforeach

                        <!-- Announcements -->
                        <div class="col-span-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    📢 Announcements
                                </h3>
                                <div class="space-y-4">
                                    @forelse($announcements ?? [] as $announcement)
                                        <div class="border-b border-gray-200 dark:border-gray-700 last:border-0 pb-4 last:pb-0">
                                            <div class="flex items-center space-x-2">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white flex-grow">
                                                    {{ $announcement->title }}
                                                </p>
                                                @if($announcement->is_pinned)
                                                    <span class="text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 px-2 py-1 rounded">
                                                        📌 Pinned
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                {{ Str::limit($announcement->content, 100) }}
                                            </p>
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $announcement->published_at->diffForHumans() }}
                                                </span>
                                                <a href="#" class="text-xs text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                                    Read More →
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No announcements</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/social-feed.js', 'resources/js/linkedin-preview.js'])
    @endpush
</x-social-layout>