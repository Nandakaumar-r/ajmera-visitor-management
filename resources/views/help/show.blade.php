@extends('layouts.dashboard')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Help Request #{{ $helpRequest->id }}</h2>
        <a href="{{ route('help-requests.index') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
            Back to Requests
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        {{ $helpRequest->subject }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Submitted by {{ $helpRequest->user->name }} on {{ $helpRequest->created_at->format('M d, Y H:i') }}
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $helpRequest->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($helpRequest->status) }}
                    </span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $helpRequest->priority === 'high' ? 'bg-red-100 text-red-800' : 
                           ($helpRequest->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst($helpRequest->priority) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Category
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $helpRequest->category }}
                    </dd>
                </div>
                
                @if($helpRequest->attachment_path)
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Attachment
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <a href="{{ Storage::url($helpRequest->attachment_path) }}" 
                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                           target="_blank">
                            View Attachment
                        </a>
                    </dd>
                </div>
                @endif

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Description
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $helpRequest->description }}
                    </dd>
                </div>

                @if($helpRequest->closed_at)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Closed Information
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        Closed by {{ $helpRequest->closedBy->name }} on {{ $helpRequest->closed_at->format('M d, Y H:i') }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        @if($helpRequest->status === 'active' && auth()->user()->hasRole(['admin', 'hr']))
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
            <form action="{{ route('help-requests.close', $helpRequest) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Close Request
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
