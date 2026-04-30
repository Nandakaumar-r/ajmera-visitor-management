<x-employee-management-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Document Request Details') }}
            </h2>
            <a href="{{ route('employees.document-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Request Information</h3>
                            <dl class="mt-4 space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Document Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ str_replace('_', ' ', ucfirst($documentRequest->document_type)) }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $documentRequest->status_badge }}">
                                            {{ ucfirst($documentRequest->status) }}
                                        </span>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested By</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $documentRequest->user->name }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested On</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $documentRequest->created_at->format('M d, Y H:i A') }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reason</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $documentRequest->reason }}
                                    </dd>
                                </div>

                                @if($documentRequest->status !== 'pending')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ $documentRequest->status === 'approved' ? 'Approved' : 'Rejected' }} By
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $documentRequest->approver->name }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ $documentRequest->status === 'approved' ? 'Approved' : 'Rejected' }} On
                                        </dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $documentRequest->approved_at->format('M d, Y H:i A') }}
                                        </dd>
                                    </div>

                                    @if($documentRequest->status === 'rejected')
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $documentRequest->rejection_reason }}
                                            </dd>
                                        </div>
                                    @endif
                                @endif
                            </dl>
                        </div>

                        @if(auth()->user()->hasRole(['hr', 'admin']) && $documentRequest->status === 'pending')
                            <div class="md:border-l md:border-gray-200 md:pl-6 dark:md:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Take Action</h3>
                                <form method="POST" action="{{ route('employees.document-requests.update', $documentRequest) }}" class="mt-4 space-y-4" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <x-input-label for="status" :value="__('Decision')" />
                                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                            <option value="">Select a decision</option>
                                            <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approve</option>
                                            <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Reject</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                    </div>

                                    <div id="document-upload" style="display: none;">
                                        <x-input-label for="document" :value="__('Upload Document')" />
                                        <input type="file" id="document" name="document" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-md file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100
                                            dark:file:bg-gray-700 dark:file:text-gray-300">
                                        <x-input-error :messages="$errors->get('document')" class="mt-2" />
                                    </div>

                                    <div id="rejection-reason" style="display: none;">
                                        <x-input-label for="rejection_reason" :value="__('Rejection Reason')" />
                                        <textarea
                                            id="rejection_reason"
                                            name="rejection_reason"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            placeholder="Please provide a reason for rejecting this request"
                                        >{{ old('rejection_reason') }}</textarea>
                                        <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />
                                    </div>

                                    <div class="flex justify-end mt-6">
                                        <x-primary-button>
                                            {{ __('Submit Decision') }}
                                        </x-primary-button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('status').addEventListener('change', function() {
            const documentUpload = document.getElementById('document-upload');
            const rejectionReason = document.getElementById('rejection-reason');

            if (this.value === 'approved') {
                documentUpload.style.display = 'block';
                rejectionReason.style.display = 'none';
            } else if (this.value === 'rejected') {
                documentUpload.style.display = 'none';
                rejectionReason.style.display = 'block';
            } else {
                documentUpload.style.display = 'none';
                rejectionReason.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-employee-management-layout>
