<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Travel Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('travel.store') }}" id="travelRequestForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="start_date" :value="__('Travel Start Date')" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                            </div>

                            <div class="col-md-6">
                                <x-input-label for="end_date" :value="__('Travel End Date')" />
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="start_time" :value="__('Departure Time (if known)')" />
                                <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" />
                                <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
                            </div>

                            <div class="col-md-6">
                                <x-input-label for="end_time" :value="__('Return Time (if known)')" />
                                <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" />
                                <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="destination" :value="__('Destination Location')" />
                                <x-location-autocomplete />
                                <x-input-error class="mt-2" :messages="$errors->get('destination')" />
                            </div>

                            <div class="col-md-6">
                                <x-input-label for="transport_mode" :value="__('Mode of Transportation')" />
                                <select id="transport_mode" name="transport_mode" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Select transportation mode</option>
                                    <option value="flight" {{ old('transport_mode') == 'flight' ? 'selected' : '' }}>Flight</option>
                                    <option value="train" {{ old('transport_mode') == 'train' ? 'selected' : '' }}>Train</option>
                                    <option value="bus" {{ old('transport_mode') == 'bus' ? 'selected' : '' }}>Bus</option>
                                    <option value="car" {{ old('transport_mode') == 'car' ? 'selected' : '' }}>Car</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('transport_mode')" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <x-input-label for="travel_reason" :value="__('Purpose of Travel')" />
                                <textarea id="travel_reason" name="travel_reason" rows="3" class="mt-1 block w-full" required>{{ old('travel_reason') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('travel_reason')" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="estimated_cost" :value="__('Estimated Total Cost')" />
                                <x-text-input id="estimated_cost" name="estimated_cost" type="number" step="0.01" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('estimated_cost')" />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label d-block">Is this an International Trip?</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_international" id="is_international_yes" value="1" {{ old('is_international') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_international_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_international" id="is_international_no" value="0" {{ old('is_international') == '0' ? 'checked' : '' }} checked>
                                    <label class="form-check-label" for="is_international_no">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                Accommodation Details (if required)
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-input-label for="accommodation_details[hotel_name]" :value="__('Hotel/Accommodation Name')" />
                                        <x-text-input id="accommodation_details[hotel_name]" name="accommodation_details[hotel_name]" type="text" class="mt-1 block w-full" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-input-label for="accommodation_details[location]" :value="__('Location')" />
                                        <x-text-input id="accommodation_details[location]" name="accommodation_details[location]" type="text" class="mt-1 block w-full" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Travelers</span>
                                <button type="button" class="btn btn-sm btn-primary" id="addTraveler">Add Traveler</button>
                            </div>
                            <div class="card-body" id="travelersContainer">
                                <div class="traveler-entry mb-3">
                                    <input type="hidden" name="number_of_travelers" id="number_of_travelers" value="1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-input-label for="travelers[0][name]" :value="__('Full Name')" />
                                            <x-text-input id="travelers[0][name]" name="travelers[0][name]" type="text" class="mt-1 block w-full" required />
                                        </div>
                                        <div class="col-md-6">
                                            <x-input-label for="travelers[0][employee_id]" :value="__('Employee ID (if applicable)')" />
                                            <x-text-input id="travelers[0][employee_id]" name="travelers[0][employee_id]" type="text" class="mt-1 block w-full" />
                                        </div>
                                    </div>
                                    <div class="row mt-2 passport-fields" style="display: none;">
                                        <div class="col-md-6">
                                            <x-input-label for="travelers[0][passport_number]" :value="__('Passport Number')" />
                                            <x-text-input id="travelers[0][passport_number]" name="travelers[0][passport_number]" type="text" class="mt-1 block w-full" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-input-label for="travelers[0][passport_expiry]" :value="__('Passport Expiry Date')" />
                                            <x-text-input id="travelers[0][passport_expiry]" name="travelers[0][passport_expiry]" type="date" class="mt-1 block w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <x-primary-button>
                                    {{ __('Submit Travel Request') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let travelerCount = 1;
            const addTravelerBtn = document.getElementById('addTraveler');
            const travelersContainer = document.getElementById('travelersContainer');
            const numberofTravelersInput = document.getElementById('number_of_travelers');
            const isInternationalInputs = document.querySelectorAll('input[name="is_international"]');

            // Show/hide passport fields based on international selection
            function togglePassportFields() {
                const isInternational = document.querySelector('input[name="is_international"]:checked').value === '1';
                const passportFields = document.querySelectorAll('.passport-fields');
                const passportInputs = document.querySelectorAll('.passport-number, .passport-expiry');

                passportFields.forEach(field => {
                    field.style.display = isInternational ? 'flex' : 'none';
                });

                passportInputs.forEach(input => {
                    input.required = isInternational;
                });
            }

            // Initial passport fields visibility
            togglePassportFields();

            // Listen for international travel selection changes
            isInternationalInputs.forEach(input => {
                input.addEventListener('change', togglePassportFields);
            });

            // Add new traveler
            addTravelerBtn.addEventListener('click', function() {
                const isInternational = document.querySelector('input[name="is_international"]:checked').value === '1';
                const newTraveler = document.createElement('div');
                newTraveler.className = 'traveler-entry mb-3';
                newTraveler.innerHTML = `
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <x-input-label for="travelers[${travelerCount}][name]" :value="__('Full Name')" />
                            <x-text-input id="travelers[${travelerCount}][name]" name="travelers[${travelerCount}][name]" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div class="col-md-6">
                            <x-input-label for="travelers[${travelerCount}][employee_id]" :value="__('Employee ID (if applicable)')" />
                            <x-text-input id="travelers[${travelerCount}][employee_id]" name="travelers[${travelerCount}][employee_id]" type="text" class="mt-1 block w-full" />
                        </div>
                    </div>
                    <div class="row mt-2 passport-fields" style="display: ${isInternational ? 'flex' : 'none'};">
                        <div class="col-md-6">
                            <x-input-label for="travelers[${travelerCount}][passport_number]" :value="__('Passport Number')" />
                            <x-text-input id="travelers[${travelerCount}][passport_number]" name="travelers[${travelerCount}][passport_number]" type="text" class="mt-1 block w-full" ${isInternational ? 'required' : ''} />
                        </div>
                        <div class="col-md-6">
                            <x-input-label for="travelers[${travelerCount}][passport_expiry]" :value="__('Passport Expiry Date')" />
                            <x-text-input id="travelers[${travelerCount}][passport_expiry]" name="travelers[${travelerCount}][passport_expiry]" type="date" class="mt-1 block w-full" ${isInternational ? 'required' : ''} />
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger mt-2 remove-traveler">Remove Traveler</button>
                `;

                travelersContainer.appendChild(newTraveler);
                travelerCount++;
                numberofTravelersInput.value = travelerCount;

                // Add remove functionality to the new traveler
                newTraveler.querySelector('.remove-traveler').addEventListener('click', function() {
                    newTraveler.remove();
                    travelerCount--;
                    numberofTravelersInput.value = travelerCount;
                });
            });

            // Form validation before submit
            document.getElementById('travelRequestForm').addEventListener('submit', function(e) {
                const startDate = new Date(document.getElementById('start_date').value);
                const endDate = new Date(document.getElementById('end_date').value);

                if (endDate < startDate) {
                    e.preventDefault();
                    alert('End date cannot be earlier than start date');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
