@extends('layouts.dashboard')

@section('content')

<!-- Show Leave -->

<!-- Show Error & Success -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- Show Leave -->
<div class="py-12">
    <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Header with status badge -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Leave Application Details</h2>
                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                        <a href="{{ route('leaves.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Back to List
                        </a>
                    </div>
                </div>

                <!-- Leave details grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Leave Type</h3>
                            <p class="text-gray-900">{{ ucfirst($leave->leave_type) }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Duration</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">From</p>
                                    <p class="text-gray-900">
                                        {{ $leave->from_date ? $leave->from_date->format('d M Y') : 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ ucfirst($leave->session_1) }} Session</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">To</p>
                                    <p class="text-gray-900">
                                        {{ $leave->to_date ? $leave->to_date->format('d M Y') : 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ ucfirst($leave->session_2) }} Session</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Contact Details</h3>
                            <p class="text-gray-900">{{ $leave->contact_details }}</p>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Application Date</h3>
                            <p class="text-gray-900">{{ $leave->created_at ? $leave->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Reason</h3>
                            <p class="text-gray-900">{{ $leave->reason }}</p>
                        </div>

                        @if($leave->attachment_path)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Attachment</h3>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <a href="{{ Storage::url($leave->attachment_path) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm"
                                   target="_blank">
                                    View Attachment
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Status History -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status History</h3>
                    <div class="border-l-2 border-gray-200 ml-3">
                        <!-- Created Status -->
                        <div class="relative pb-6">
                            <div class="absolute -left-[9px] mt-2">
                                <div class="bg-blue-500 rounded-full w-4 h-4"></div>
                            </div>
                            <div class="ml-6">
                                <p class="text-sm font-medium text-gray-900">Application Submitted</p>
                                <p class="text-sm text-gray-500">{{ $leave->created_at ? $leave->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Status Update (if any) -->
                        @if($leave->status !== 'pending')
                        <div class="relative pb-6">
                            <div class="absolute -left-[9px] mt-2">
                                <div class="rounded-full w-4 h-4 {{ $leave->status === 'approved' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            </div>
                            <div class="ml-6">
                                <p class="text-sm font-medium text-gray-900">
                                    Application {{ ucfirst($leave->status) }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $leave->updated_at ? $leave->updated_at->format('d M Y, h:i A') : 'N/A' }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($leave->status === 'pending')
                <div class="mt-8 flex gap-4">
                    <form action="{{ route('leaves.destroy', $leave) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                            onclick="return confirm('Are you sure you want to cancel this leave application?')">
                            Cancel Application
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
