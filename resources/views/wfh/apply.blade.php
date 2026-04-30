@extends('layouts.app')


@section('head')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Work From Home Application</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2 mt-8">Work From Home Application</h1>
            <p class="text-gray-600">Submit your WFH request for approval</p>
        </div>

        <!-- Instructions Card -->
        <div class="bg-blue-50 border border-amber-200 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-amber-800">Important Guidelines</h3>
                    <div class="mt-2 text-sm text-amber-700">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            <li>WFH is <strong>not</strong> a regular entitlement and should only be requested in exceptional circumstances</li>
                            <li>Apply only in case of emergency, illness, or unavoidable personal situations</li>
                            <li>Ensure you have reliable internet connection and necessary equipment</li>
                            <li>Submit your request at least 24 hours in advance (except for emergencies)</li>
                            <li>Your manager and HR will review your request and respond within 2 business days</li>
                            <li>You must be available during regular working hours and respond to calls/messages promptly</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Application Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Application Details</h2>
            </div>

            <form action="{{ route('wfh.store') }}" method="POST" class="p-6 space-y-6" x-data="wfhForm()">
                @csrf

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                        <input type="date"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date <span class="text-red-500">*</span></label>
                        <input type="date"
                            id="end_date"
                            name="end_date"
                            value="{{ old('end_date') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for WFH Request <span class="text-red-500">*</span></label>
                    <textarea id="reason"
                        name="reason"
                        rows="4"
                        placeholder="Please provide a detailed reason for your WFH request..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                    @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Work Location -->
                <div>
                    <label for="work_location" class="block text-sm font-medium text-gray-700 mb-2">Work Location Address <span class="text-red-500">*</span></label>
                    <input type="text"
                        id="work_location"
                        name="work_location"
                        value="{{ old('work_location') }}"
                        placeholder="Enter your work from home address"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('work_location') border-red-500 @enderror">
                    @error('work_location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <button type="button"
                        @click="getLocation()"
                        :disabled="locationLoading"
                        class="mt-2 inline-flex items-center px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!locationLoading">📍 Use Current Location</span>
                        <span x-show="locationLoading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Getting Location...
                        </span>
                    </button>
                    <div x-show="locationError" class="mt-2 text-sm text-red-600" x-text="locationError"></div>
                    <div x-show="locationSuccess" class="mt-2 text-sm text-green-600">✓ Location captured successfully</div>
                </div>

                <!-- Hidden Location Fields -->
                <input type="hidden" name="latitude" x-model="latitude">
                <input type="hidden" name="longitude" x-model="longitude">

                <!-- Emergency Contact -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Number <span class="text-red-500">*</span></label>
                        <input type="tel"
                            id="emergency_contact"
                            name="emergency_contact"
                            value="{{ old('emergency_contact') }}"
                            placeholder="+91 9876543210"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('emergency_contact') border-red-500 @enderror">
                        @error('emergency_contact')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="internet_speed" class="block text-sm font-medium text-gray-700 mb-2">Internet Speed (Mbps) <span class="text-red-500">*</span></label>
                        <input type="number"
                            id="internet_speed"
                            name="internet_speed"
                            value="{{ old('internet_speed') }}"
                            placeholder="25"
                            min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('internet_speed') border-red-500 @enderror">
                        @error('internet_speed')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Equipment and Backup Plan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="equipment_needed" class="block text-sm font-medium text-gray-700 mb-2">Equipment Needed (if any)</label>
                        <input type="text"
                            id="equipment_needed"
                            name="equipment_needed"
                            value="{{ old('equipment_needed') }}"
                            placeholder="Laptop, headset, etc."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="backup_plan" class="block text-sm font-medium text-gray-700 mb-2">Backup Plan <span class="text-red-500">*</span></label>
                        <input type="text"
                            id="backup_plan"
                            name="backup_plan"
                            value="{{ old('backup_plan') }}"
                            placeholder="Mobile hotspot, nearby cafe, etc."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('backup_plan') border-red-500 @enderror">
                        @error('backup_plan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Declaration -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <input type="checkbox"
                            id="declaration"
                            required
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                        <label for="declaration" class="ml-2 text-sm text-gray-700">
                            I hereby declare that the information provided is true and accurate. I understand that WFH is a privilege and not a right, and I commit to maintaining the same level of productivity and availability as if I were working from the office. I agree to follow all company policies and guidelines while working from home.
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <button type="button"
                        class="px-6 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function wfhForm() {
        return {
            latitude: '',
            longitude: '',
            locationLoading: false,
            locationError: '',
            locationSuccess: false,

            getLocation() {
                this.locationLoading = true;
                this.locationError = '';
                this.locationSuccess = false;

                if (!navigator.geolocation) {
                    this.locationError = 'Geolocation is not supported by this browser.';
                    this.locationLoading = false;
                    return;
                }

                const options = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.latitude = position.coords.latitude;
                        this.longitude = position.coords.longitude;
                        this.locationLoading = false;
                        this.locationSuccess = true;

                        // Get address from coordinates
                        this.reverseGeocode(this.latitude, this.longitude);
                    },
                    (error) => {
                        this.locationLoading = false;
                        this.handleLocationError(error);
                    },
                    options
                );
            },

            handleLocationError(error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        this.locationError = 'Location access denied by user. Please enable location access and try again.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        this.locationError = 'Location information is unavailable. Please enter address manually.';
                        break;
                    case error.TIMEOUT:
                        this.locationError = 'Location request timed out. Please try again or enter address manually.';
                        break;
                    default:
                        this.locationError = 'An unknown error occurred while retrieving location.';
                        break;
                }
            },

            reverseGeocode(lat, lng) {
                // Using OpenStreetMap Nominatim API for reverse geocoding (free)
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.display_name) {
                            document.getElementById('work_location').value = data.display_name;
                        } else {
                            // Fallback to coordinates if no address found
                            document.getElementById('work_location').value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                        }
                    })
                    .catch(error => {
                        console.error('Reverse geocoding failed:', error);
                        // Fallback to coordinates
                        document.getElementById('work_location').value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                    });
            }
        }
    }

    // Check if browser supports geolocation on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (!navigator.geolocation) {
            const locationButton = document.querySelector('[x-on\\:click="getLocation()"]');
            if (locationButton) {
                locationButton.style.display = 'none';
            }

            // Show a message that geolocation is not supported
            const workLocationDiv = document.getElementById('work_location').parentElement;
            const warningDiv = document.createElement('div');
            warningDiv.className = 'mt-2 text-sm text-amber-600';
            warningDiv.innerHTML = '⚠️ Geolocation is not supported by this browser. Please enter your address manually.';
            workLocationDiv.appendChild(warningDiv);
        }
    });
</script>

@endsection