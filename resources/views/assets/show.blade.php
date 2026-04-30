<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Asset Request Details') }}
            </h2>
            <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Asset Information</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Asset Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $assetRequest->asset->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $assetRequest->asset->category->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $assetRequest->quantity }}</p>
                                </div>
                                @if($assetRequest->justification)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Justification</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $assetRequest->justification }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Request Status</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($assetRequest->status === 'approved') bg-green-100 text-green-800
                                        @elseif($assetRequest->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($assetRequest->status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Requested On</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $assetRequest->created_at->format('M d, Y H:i A') }}</p>
                                </div>
                                @if($assetRequest->status === 'approved')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Handover Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $assetRequest->handover_date ? $assetRequest->handover_date->format('M d, Y') : 'Not set' }}</p>
                                </div>
                                @endif
                                @if($assetRequest->remarks)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Remarks</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $assetRequest->remarks }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @can('approve', $assetRequest)
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="flex justify-end space-x-3">
                            <form action="{{ route('assets.reject', $assetRequest) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                    {{ __('Reject') }}
                                </button>
                            </form>
                            <form action="{{ route('assets.approve', $assetRequest) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    {{ __('Approve') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>