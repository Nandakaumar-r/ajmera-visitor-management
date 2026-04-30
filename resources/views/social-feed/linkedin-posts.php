<!-- Todo : Add LinkedIn Post -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Social Feed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(isset($posts['error']))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ $posts['error'] }}</span>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($posts['posts'] as $post)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <div class="flex items-center mb-4">
                                    @if(isset($post['author']['profilePicture']['displayImage']))
                                        <img src="{{ $post['author']['profilePicture']['displayImage'] }}" 
                                             alt="Profile Picture" 
                                             class="h-10 w-10 rounded-full mr-4">
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-semibold">
                                            {{ $post['author']['name'] ?? 'Unknown Author' }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($post['created']['time'])->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                @if(isset($post['commentary']))
                                    <div class="mb-4 text-gray-700">
                                        {!! nl2br(e($post['commentary'])) !!}
                                    </div>
                                @endif

                                @if(isset($post['content']))
                                    <div class="mb-4">
                                        @if(isset($post['content']['media']))
                                            @foreach($post['content']['media'] as $media)
                                                @if($media['type'] === 'image')
                                                    <img src="{{ $media['url'] }}" 
                                                         alt="Post Image" 
                                                         class="rounded-lg max-h-96 w-auto">
                                                @elseif($media['type'] === 'video')
                                                    <video controls class="rounded-lg max-h-96 w-auto">
                                                        <source src="{{ $media['url'] }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                @endif

                                <div class="flex items-center space-x-4 text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                        </svg>
                                        {{ $post['likes']['totalCount'] ?? 0 }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        {{ $post['comments']['totalCount'] ?? 0 }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                        </svg>
                                        {{ $post['shares']['totalCount'] ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>