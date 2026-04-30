<x-reception-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Visitor Details') }}
            </h2>
            <a href="{{ route('visitors.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Visitor Information -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Visitor Information</h3>
                                <dl class="mt-2 space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $visitor->first_name }} {{ $visitor->last_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Contact Information</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $visitor->phone_number }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Purpose of Visit</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $visitor->purpose_of_visit }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Whom to Visit</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $visitor->whomToVisitUser->name ?? 'N/A' }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Government ID Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $visitor->government_id_type }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">ID Last Digits</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $visitor->government_id_last_digits }}</dd>
                                    </div>
                                    @if($visitor->additional_details)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Additional Details</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $visitor->additional_details }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Visit Status</h3>
                                <dl class="mt-2 space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1">
                                            @if($visitor->status === 'pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                            @elseif($visitor->status === 'approved')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Approved
                                            </span>
                                            @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Rejected
                                            </span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Check In Time</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $visitor->check_in_time->format('M d, Y H:i') }}</dd>
                                    </div>
                                    @if($visitor->check_out_time)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Check Out Time</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $visitor->check_out_time->format('M d, Y H:i') }}</dd>
                                    </div>
                                    @endif
                                    @if($visitor->rejection_reason)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Rejection Reason</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $visitor->rejection_reason }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                        </div>

                        <!-- Photo, Signature, and Actions -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Photo</h3>
                                <div class="mt-2">
                                    <img src="{{ Storage::url($visitor->photo_path) }}" alt="Visitor Photo" class="w-full h-auto rounded-lg">
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Signature</h3>
                                <div class="mt-2">
                                    <img src="{{ Storage::url($visitor->signature_path) }}" alt="Visitor Signature" class="w-full h-auto rounded-lg bg-white">
                                </div>
                            </div>

                            @if($visitor->visiting_card_path)
                            <div class="mt-8">
                                <h3 class="text-lg font-medium text-gray-900">Visiting Card Information</h3>
                                <div class="mt-4 grid grid-cols-1 gap-4">
                                    <div>
                                        <img src="{{ Storage::url($visitor->visiting_card_path) }}"
                                            alt="Visiting Card"
                                            class="max-w-md rounded-lg shadow-sm">
                                    </div>
                                    @if($visitor->visiting_card_data)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Extracted Information</h4>
                                        <dl class="grid grid-cols-1 gap-2">
                                            @foreach($visitor->visiting_card_data as $key => $value)
                                            @if($value)
                                            <div class="flex">
                                                <dt class="text-sm font-medium text-gray-500 w-24">{{ ucfirst($key) }}:</dt>
                                                <dd class="text-sm text-gray-900 ml-2">{{ $value }}</dd>
                                            </div>
                                            @endif
                                            @endforeach
                                        </dl>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($visitor->status === 'pending')
                            @role('hr')
                            <div class="space-y-3">
                                <form action="{{ route('visitors.approve', $visitor) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Approve Visit
                                    </button>
                                </form>

                                <button type="button" onclick="toggleRejectForm()" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Reject Visit
                                </button>

                                <form id="reject-form" action="{{ route('visitors.reject', $visitor) }}" method="POST" class="hidden">
                                    @csrf
                                    <div class="mt-3">
                                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                                        <textarea name="rejection_reason" id="rejection_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                                    </div>
                                    <button type="submit" class="mt-3 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Confirm Rejection
                                    </button>
                                </form>
                            </div>
                            @endrole
                            @endif

                            @role('reception')
                            @if(!$visitor->check_out_time)
                            <form action="{{ route('visitors.checkout', $visitor) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Check Out Visitor
                                </button>
                            </form>
                            @endif
                            @endrole
                        </div>
                    </div>

                    <!-- Audit Trail -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900">Audit Trail</h3>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Time
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Action
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($visitor->logs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->user->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $log->description }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleRejectForm() {
            const form = document.getElementById('reject-form');
            form.classList.toggle('hidden');
        }
    </script>
    @endpush
    </x-app-layout>