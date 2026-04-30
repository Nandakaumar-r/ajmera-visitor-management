<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Travel Request Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Request Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p class="font-medium">
                                    @switch($travelRequest->status)
                                        @case('pending')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                            @break
                                        @case('manager_approved')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">Manager Approved</span>
                                            @break
                                        @case('cfo_approved')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded">CFO Approved</span>
                                            @break
                                        @case('rejected')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded">Rejected</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded">{{ $travelRequest->status }}</span>
                                    @endswitch
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Created On</p>
                                <p class="font-medium">{{ $travelRequest->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Travel Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Start Date</p>
                                <p class="font-medium">{{ $travelRequest->start_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">End Date</p>
                                <p class="font-medium">{{ $travelRequest->end_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Purpose</p>
                                <p class="font-medium">{{ $travelRequest->purpose }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Location</p>
                                <p class="font-medium">{{ $travelRequest->location }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Additional Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Transport Mode</p>
                                <p class="font-medium">{{ $travelRequest->transport_mode }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Number of Travelers</p>
                                <p class="font-medium">{{ $travelRequest->number_of_travelers }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Travel Reason</p>
                                <p class="font-medium">{{ $travelRequest->travel_reason }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Accommodation Details</p>
                                <ul class="list-disc pl-5">
                                    @foreach($travelRequest->accommodation_details as $key => $value)
                                        <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Estimated Cost</p>
                                <p class="font-medium">{{ $travelRequest->estimated_cost }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Actual Cost</p>
                                <p class="font-medium">{{ $travelRequest->actual_cost }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">International Travel</p>
                                <p class="font-medium">{{ $travelRequest->is_international ? 'Yes' : 'No' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Group Travel</p>
                                <p class="font-medium">{{ $travelRequest->is_group_travel ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($travelRequest->travelers->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Travelers</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($travelRequest->travelers as $traveler)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $traveler->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $traveler->email }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $traveler->department }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($travelRequest->booking_details)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Booking Details</h3>
                            <div class="bg-gray-50 p-4 rounded">
                                <pre class="whitespace-pre-wrap">{{ json_encode($travelRequest->booking_details, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif

                    @if($travelRequest->comments)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Comments</h3>
                            <p class="text-gray-700">{{ $travelRequest->comments }}</p>
                        </div>
                    @endif

                    <div class="mt-8 flex gap-4">
                        @if($travelRequest->status === 'pending' && auth()->user()->can('approveAsManager', $travelRequest))
                            <form action="{{ route('travel.approve.manager', $travelRequest) }}" method="POST">
                                @csrf
                                <x-primary-button>Approve as Manager</x-primary-button>
                            </form>
                        @endif

                        @if($travelRequest->status === 'manager_approved' && auth()->user()->can('approveAsCFO', $travelRequest))
                            <form action="{{ route('travel.approve.cfo', $travelRequest) }}" method="POST">
                                @csrf
                                <x-primary-button>Approve as CFO</x-primary-button>
                            </form>
                        @endif

                        @if(($travelRequest->status === 'pending' || $travelRequest->status === 'manager_approved') && auth()->user()->can('reject', $travelRequest))
                            <form action="{{ route('travel.reject', $travelRequest) }}" method="POST" class="inline">
                                @csrf
                                <x-danger-button type="submit" onclick="return confirm('Are you sure you want to reject this travel request?')">
                                    Reject Request
                                </x-danger-button>
                            </form>
                        @endif

                        @if($travelRequest->status === 'cfo_approved' && auth()->user()->can('updateBooking', $travelRequest))
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Update Booking Details
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
