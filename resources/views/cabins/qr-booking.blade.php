<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book') }} {{ $cabin->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Cabin Details</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            <strong>Name:</strong> {{ $cabin->name }}<br>
                            <strong>Capacity:</strong> {{ $cabin->capacity }} people<br>
                            <strong>Description:</strong> {{ $cabin->description }}
                        </p>
                    </div>

                    <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6" id="bookingForm">
                        @csrf
                        <input type="hidden" name="cabin_id" value="{{ $cabin->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="datetime-local" name="start_time" id="start_time" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                <input type="datetime-local" name="end_time" id="end_time" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose</label>
                            <input type="text" name="purpose" id="purpose" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter the purpose of the meeting">
                        </div>

                        <div>
                            <label for="attendees" class="block text-sm font-medium text-gray-700">Attendees</label>
                            <select name="attendees[]" id="attendees" multiple required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Book Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const form = document.getElementById('bookingForm');

            async function checkAvailability() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;

                if (startTime && endTime) {
                    try {
                        const response = await fetch(`{{ route('bookings.check-availability') }}?start_time=${startTime}&end_time=${endTime}`);
                        const data = await response.json();
                        
                        const isCabinAvailable = data.cabins.some(cabin => cabin.id === {{ $cabin->id }});
                        if (!isCabinAvailable) {
                            alert('This cabin is not available for the selected time slot.');
                            form.querySelector('button[type="submit"]').disabled = true;
                        } else {
                            form.querySelector('button[type="submit"]').disabled = false;
                        }
                    } catch (error) {
                        console.error('Error checking availability:', error);
                    }
                }
            }

            startTimeInput.addEventListener('change', checkAvailability);
            endTimeInput.addEventListener('change', checkAvailability);
        });
    </script>
    @endpush
</x-app-layout>
