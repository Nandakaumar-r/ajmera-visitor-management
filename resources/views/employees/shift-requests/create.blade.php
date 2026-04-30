<x-employee-management-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Shift Change Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('employees.shift-requests.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium">Current Shift</h3>
                                
                                <div>
                                    <x-input-label for="current_start_time" :value="__('Start Time')" />
                                    <select id="current_start_time" name="current_start_time" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Select start time</option>
                                        @foreach($timeSlots as $value => $label)
                                            <option value="{{ $value }}" {{ old('current_start_time') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('current_start_time')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="current_end_time" :value="__('End Time')" />
                                    <select id="current_end_time" name="current_end_time" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Select end time</option>
                                        @foreach($timeSlots as $value => $label)
                                            <option value="{{ $value }}" {{ old('current_end_time') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('current_end_time')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-lg font-medium">Requested Shift</h3>
                                
                                <div>
                                    <x-input-label for="requested_start_time" :value="__('Start Time')" />
                                    <select id="requested_start_time" name="requested_start_time" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Select start time</option>
                                        @foreach($timeSlots as $value => $label)
                                            <option value="{{ $value }}" {{ old('requested_start_time') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('requested_start_time')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="requested_end_time" :value="__('End Time')" />
                                    <select id="requested_end_time" name="requested_end_time" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Select end time</option>
                                        @foreach($timeSlots as $value => $label)
                                            <option value="{{ $value }}" {{ old('requested_end_time') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('requested_end_time')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6 mt-6">
                            <div>
                                <x-input-label for="effective_from" :value="__('Effective From')" />
                                <x-text-input id="effective_from" name="effective_from" type="date" class="mt-1 block w-full" :value="old('effective_from')" />
                                <x-input-error :messages="$errors->get('effective_from')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="reason" :value="__('Reason for Change')" />
                                <textarea
                                    id="reason"
                                    name="reason"
                                    rows="4"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    placeholder="Please provide a reason for requesting this shift change"
                                >{{ old('reason') }}</textarea>
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button type="button" onclick="window.history.back()" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Submit Request') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Add client-side validation to ensure end time is after start time
        function validateTimes(startId, endId) {
            const startSelect = document.getElementById(startId);
            const endSelect = document.getElementById(endId);

            startSelect.addEventListener('change', function() {
                const startTime = this.value;
                Array.from(endSelect.options).forEach(option => {
                    if (option.value <= startTime && option.value !== '') {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });

                if (endSelect.value <= startTime) {
                    endSelect.value = '';
                }
            });
        }

        validateTimes('current_start_time', 'current_end_time');
        validateTimes('requested_start_time', 'requested_end_time');
    </script>
    @endpush
</x-employee-management-layout>
