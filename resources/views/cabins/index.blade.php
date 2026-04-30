<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cabin Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <!-- Booking Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Book a Cabin</h3>
                    <form action="{{ route('bookings.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="datetime-local" name="start_time" id="start_time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                <input type="datetime-local" name="end_time" id="end_time" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label for="cabin_id" class="block text-sm font-medium text-gray-700">Select Cabin</label>
                            <select name="cabin_id" id="cabin_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a cabin</option>
                                @foreach($cabins as $cabin)
                                    @if($selectedCabin && $selectedCabin->id == $cabin->id)
                                        <option value="{{ $cabin->id }}" selected>{{ $cabin->name }} (Capacity: {{ $cabin->capacity }})</option>
                                    @else
                                        <option value="{{ $cabin->id }}">{{ $cabin->name }} (Capacity: {{ $cabin->capacity }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                            <input type="text" name="purpose" id="purpose" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="attendees" class="block text-sm font-medium text-gray-700">Attendees</label>
                            <select name="attendees[]" id="attendees" multiple required class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Book Cabin
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bookings List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Your Bookings</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cabin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendees</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->cabin->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $booking->start_time->format('M d, Y H:i') }} -
                                            {{ $booking->end_time->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4">{{ $booking->purpose }}</td>
                                        <td class="px-6 py-4">
                                            <ul class="list-disc list-inside">
                                                @foreach($booking->attendees as $attendee)
                                                    <li>{{ $attendee->user->name }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 flex space-x-2">
                                            @if($booking->status === 'confirmed' && !$booking->end_time->isPast())
                                                <button 
                                                    onclick="extendBooking('{{ $booking->id }}')" 
                                                    class="text-blue-600 hover:underline extend-booking">
                                                    Extend
                                                </button>
                                                <button 
                                                    onclick="cancelBooking('{{ $booking->id }}')" 
                                                    class="text-red-600 hover:underline cancel-booking">
                                                    Cancel
                                                </button>
                                            @else
                                                <a class="text-blue-600 hover:underline" href="{{ route('bookings.details', $booking) }}">Details</a>
                                            @endif
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

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('scripts')
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const cabinSelect = document.getElementById('cabin_id');
            const bookingForm = document.querySelector('form[action="{{ route('bookings.store') }}"]');
            
            // Set default times
            const now = new Date();
            const istOffset = 5.5 * 60; // IST is UTC+5:30
            const localNow = new Date(now.getTime() + istOffset * 60000);
            const thirtyMinutesLater = new Date(localNow.getTime() + 30 * 60000);

            startTimeInput.value = localNow.toISOString().slice(0, 16);
            endTimeInput.value = thirtyMinutesLater.toISOString().slice(0, 16);

            // Initialize Select2
            $('.select2').select2({
                placeholder: 'Select attendees',
                allowClear: true,
                width: '100%'
            });

            // Check cabin availability when times change
            function checkAvailability() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;
                // Extract cabin ID from URL parameter
                const urlParams = new URLSearchParams(window.location.search);
                const selectedCabinId = urlParams.get('c') ? parseInt(urlParams.get('c')) : null;

                if (startTime && endTime) {
                    fetch(`{{ route('bookings.check-availability') }}?start_time=${startTime}&end_time=${endTime}`)
                        .then(response => response.json())
                        .then(data => {
                            // Clear current options
                            while (cabinSelect.options.length > 1) {
                                cabinSelect.remove(1);
                            }

                            // Add available cabins and select the one that matches the selected cabin
                            data.cabins.forEach(cabin => {
                                const option = new Option(
                                    `${cabin.name} (Capacity: ${cabin.capacity})`,
                                    cabin.id,
                                    false,
                                    cabin.id === selectedCabinId
                                );
                                cabinSelect.add(option);
                            });
                        });
                }
            }

            // Handle form submission
            bookingForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('{{ route('bookings.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {                    
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Failed to save booking');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Booking saved successfully!');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to save booking. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred while saving the booking. Please try again.');
                });
            });

            // Add event listeners for time changes
            startTimeInput.addEventListener('change', checkAvailability);
            endTimeInput.addEventListener('change', checkAvailability);

            // Check availability on page load
            checkAvailability();
        });
    </script>
    <script>
        function extendBooking(bookingId) {
            const hours = prompt('Enter number of hours to extend (1-60):', '1');
            if (hours === null) return;

            fetch(`/bookings/${bookingId}/extend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ additional_hours: parseInt(hours) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    if (data.booking) {
                        window.location.reload();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to extend booking. Please try again.');
            });
        }

        function cancelBooking(bookingId) {
            if (!confirm('Are you sure you want to cancel this booking?')) return;

            fetch(`/bookings/${bookingId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to cancel booking. Please try again.');
            });
        }
    </script>
    @endpush
</x-app-layout>
