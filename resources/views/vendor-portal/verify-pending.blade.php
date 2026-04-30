@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Vendor Verification</h1>
            
            @if($vendor->status === 'pending_verification')
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium">
                                Your verification is pending. Please complete all required information below.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($vendor->status === 'active')
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium">
                                Congratulations! Your vendor account has been verified and approved.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($vendor->status === 'blocked')
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium">
                                Your account is currently blocked. Please contact support or update information as instructed and resubmit.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('vendor.verification.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Basic Information</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                                Fidelis Contact Person Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="contact_person" 
                                   name="contact_person" 
                                   value="{{ old('contact_person', $vendor->contact_person) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required
                                   readonly>
                        </div>

                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Company Name <span class="text-red-500"></span>
                            </label>
                            <input type="text" 
                                   id="company_name" 
                                   name="company_name" 
                                   value="{{ old('company_name', $vendor->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   >
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $vendor->email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700"
                                   readonly required>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $vendor->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

                        <!-- <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                Company Website
                            </label>
                            <input type="url"
                                   id="website"
                                   name="website"
                                   value="{{ old('website', $vendor->website) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div> -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Do you have an MSME Certificate?<span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="has_msme" value="yes" class="text-blue-600 focus:ring-blue-500" onchange="toggleMsmeUpload(true)">
                                    <span class="ml-2 text-gray-700">Yes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="has_msme" value="no" class="text-blue-600 focus:ring-blue-500" onchange="toggleMsmeUpload(false)">
                                    <span class="ml-2 text-gray-700">No</span>
                                </label>
                            </div>
                        </div>

                        <div id="msme_upload_section" class="hidden">
                            <label for="msme_certificate" class="block text-sm font-medium text-gray-700 mb-2">
                                MSME Certificate Upload<span class="text-red-500">*</span>
                            </label>
                            <input type="file"
                                id="msme_certificate"
                                name="msme_certificate"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Supported formats: PDF, JPG, PNG (Max: 2MB)</p>
                        </div>

                        <script>
                            function toggleMsmeUpload(show) {
                                const msmeSection = document.getElementById('msme_upload_section');
                                if (show) {
                                    msmeSection.classList.remove('hidden');
                                } else {
                                    msmeSection.classList.add('hidden');
                                }
                            }
                        </script>
                    </div>
                </div>

                <!-- Tax Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Tax Information</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="pan_number" class="block text-sm font-medium text-gray-700 mb-2">
                                PAN Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="pan_number" 
                                   name="pan_number" 
                                   value="{{ old('pan_number', $vendor->pan_number) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
                                   placeholder="ABCDE1234F"
                                   required>
                            @if($vendor->pan_verified)
                                <p class="text-green-600 text-sm mt-1">✓ PAN Verified</p>
                            @else
                                <p class="text-orange-600 text-sm mt-1">⚠ PAN Verification Pending</p>
                            @endif
                        </div>

                        <div>
                            <label for="pan_document" class="block text-sm font-medium text-gray-700 mb-2">
                                PAN Document Upload <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                   id="pan_document" 
                                   name="pan_document" 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Supported formats: PDF, JPG, PNG (Max: 2MB)</p>
                        </div>

                        <div>
                            <label for="gst_number" class="block text-sm font-medium text-gray-700 mb-2">
                                GST Number
                            </label>
                            <input type="text" 
                                   id="gst_number" 
                                   name="gst_number" 
                                   value="{{ old('gst_number', $vendor->gst_number) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}"
                                   placeholder="22ABCDE1234F1Z5">
                            @if($vendor->gst_verified)
                                <p class="text-green-600 text-sm mt-1">✓ GST Verified</p>
                            @elseif($vendor->gst_number)
                                <p class="text-orange-600 text-sm mt-1">⚠ GST Verification Pending</p>
                            @endif
                        </div>

                        <div>
                            <label for="gst_document" class="block text-sm font-medium text-gray-700 mb-2">
                                GST Certificate Upload
                            </label>
                            <input type="file" 
                                   id="gst_document" 
                                   name="gst_document" 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Supported formats: PDF, JPG, PNG (Max: 2MB)</p>
                        </div>

                        <!-- Add this after the GST Number field in resources/views/vendor-portal/verify-pending.blade.php -->
                        <div>
                            <label for="tan_number" class="block text-sm font-medium text-gray-700 mb-2">
                                TAN Number
                            </label>
                            <input type="text" 
                                id="tan_number" 
                                name="tan_number" 
                                value="{{ old('tan_number', $vendor->tan_number) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                pattern="[A-Z]{4}[0-9]{5}[A-Z]{1}"
                                placeholder="ABCD12345E">
                            @if($vendor->tan_number)
                                <p class="text-green-600 text-sm mt-1">✓ TAN Provided</p>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <label for="gst_exemption_certificate" class="block text-sm font-medium text-gray-700 mb-2">
                                GST Exemption Certificate (if applicable)
                            </label>
                            <input type="file" 
                                   id="gst_exemption_certificate" 
                                   name="gst_exemption_certificate" 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Upload only if you are exempt from GST. Supported formats: PDF, JPG, PNG (Max: 2MB)</p>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Address Information</h2>
                    
                    <div class="grid md:grid-cols-1 gap-6 mb-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Complete Address <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      required>{{ old('address', $vendor->address) }}</textarea>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $vendor->city) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                State <span class="text-red-500">*</span>
                            </label>
                            <select id="state" 
                                    name="state" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="">Select State</option>
                                <option value="Andhra Pradesh" {{ old('state', $vendor->state) == 'Andhra Pradesh' ? 'selected' : '' }}>Andhra Pradesh</option>
                                <option value="Karnataka" {{ old('state', $vendor->state) == 'Karnataka' ? 'selected' : '' }}>Karnataka</option>
                                <option value="Maharashtra" {{ old('state', $vendor->state) == 'Maharashtra' ? 'selected' : '' }}>Maharashtra</option>
                                <option value="Tamil Nadu" {{ old('state', $vendor->state) == 'Tamil Nadu' ? 'selected' : '' }}>Tamil Nadu</option>
                                <option value="Delhi" {{ old('state', $vendor->state) == 'Delhi' ? 'selected' : '' }}>Delhi</option>
                                <!-- Add more states as needed -->
                            </select>
                        </div>

                        <div>
                            <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">
                                Pincode <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="pincode" 
                                   name="pincode" 
                                   value="{{ old('pincode', $vendor->pincode) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   pattern="[0-9]{6}"
                                   placeholder="123456"
                                   required>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Business Information</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="nature_of_work" class="block text-sm font-medium text-gray-700 mb-2">
                                Nature of Work <span class="text-red-500">*</span>
                            </label>
                            <select id="nature_of_work" 
                                    name="nature_of_work" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="">Select Nature of Work</option>
                                <option value="Manufacturing" {{ old('nature_of_work', $vendor->nature_of_work) == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="Trading" {{ old('nature_of_work', $vendor->nature_of_work) == 'Trading' ? 'selected' : '' }}>Trading</option>
                                <option value="Service Provider" {{ old('nature_of_work', $vendor->nature_of_work) == 'Service Provider' ? 'selected' : '' }}>Service Provider</option>
                                <option value="Consultant" {{ old('nature_of_work', $vendor->nature_of_work) == 'Consultant' ? 'selected' : '' }}>Consultant</option>
                                <option value="Contractor" {{ old('nature_of_work', $vendor->nature_of_work) == 'Contractor' ? 'selected' : '' }}>Contractor</option>
                                <option value="Other" {{ old('nature_of_work', $vendor->nature_of_work) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Business Type <span class="text-red-500">*</span>
                            </label>
                            <select id="type" 
                                    name="type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="">Select Business Type</option>
                                <option value="Sole Proprietorship" {{ old('type', $vendor->type) == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                <option value="Partnership" {{ old('type', $vendor->type) == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                <option value="Private Limited" {{ old('type', $vendor->type) == 'Private Limited' ? 'selected' : '' }}>Private Limited</option>
                                <option value="Public Limited" {{ old('type', $vendor->type) == 'Public Limited' ? 'selected' : '' }}>Public Limited</option>
                                <option value="LLP" {{ old('type', $vendor->type) == 'LLP' ? 'selected' : '' }}>LLP</option>
                            </select>
                        </div>
                    </div>

                </div>

                <!-- Points of Contact -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Points of Contact</h2>
                    
                    <div class="grid md:grid-cols-1 gap-6 mb-4">
                        <div>
                            <!-- <p class="text-sm text-gray-600 mb-4">Add primary and secondary contacts for your organization.</p> -->
                            
                            <div id="poc-container">
                                <!-- Default POC (Contact Person from above) -->
                                <div class="poc-entry mb-4 p-4 border rounded-md bg-gray-50">
                                    <h3 class="font-medium text-gray-700 mb-3">Add Primary Contact of your organization.</h3>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                            <input type="text" 
                                                   name="vendor_contact_person" 
                                                   value="{{ $vendor->vendor_contact_person }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   >
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                            <input type="email" 
                                                   name="vendor_email" 
                                                   value="{{ $vendor->vendor_email }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   >
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                            <input type="tel" 
                                                   name="vendor_phone" 
                                                   value="{{ $vendor->vendor_phone }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        <!-- <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                                            <input type="text" 
                                                   name="contacts[0][designation]" 
                                                   placeholder="e.g., Manager, Director"
                                                   value="{{ old('contacts.0.designation') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div> -->
                                    </div>
                                    <input type="hidden" name="contacts[0][is_primary]" value="1">
                                </div>
                                
                                <!-- Additional POCs -->
                                <div id="additional-pocs">
                                    @if(old('contacts'))
                                        @for($i = 1; $i < count(old('contacts')); $i++)
                                            <div class="poc-entry mb-4 p-4 border rounded-md">
                                                <div class="flex justify-between items-center mb-3">
                                                    <h3 class="font-medium text-gray-700">Additional Contact #{{ $i }}</h3>
                                                    <button type="button" class="remove-poc text-red-600 hover:text-red-800 text-sm font-medium">
                                                        Remove
                                                    </button>
                                                </div>
                                                <div class="grid md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                                        <input type="text" 
                                                               name="contacts[{{ $i }}][name]" 
                                                               value="{{ old('contacts.' . $i . '.name') }}"
                                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                               required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                                        <input type="email" 
                                                               name="contacts[{{ $i }}][email]" 
                                                               value="{{ old('contacts.' . $i . '.email') }}"
                                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                                        <input type="tel" 
                                                               name="contacts[{{ $i }}][phone]" 
                                                               value="{{ old('contacts.' . $i . '.phone') }}"
                                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                                                        <input type="text" 
                                                               name="contacts[{{ $i }}][designation]" 
                                                               placeholder="e.g., Accounts, Operations"
                                                               value="{{ old('contacts.' . $i . '.designation') }}"
                                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    @endif
                                </div>
                                
                                <!-- <button type="button" id="add-poc" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-0.5 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Another Contact
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banking Information -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Banking Information</h2>
                    <p class="text-gray-600 mb-6">Please provide your banking details for payment processing.</p>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Bank Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="bank_name"
                                   name="bank_name"
                                   value="{{ old('bank_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

                        <div>
                            <label for="account_holder_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Account Holder Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="account_holder_name"
                                   name="account_holder_name"
                                   value="{{ old('account_holder_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Account Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="account_number"
                                   name="account_number"
                                   value="{{ old('account_number') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   pattern="[0-9]{9,18}"
                                   required>
                        </div>

                        <div>
                            <label for="ifsc_code" class="block text-sm font-medium text-gray-700 mb-2">
                                IFSC Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="ifsc_code"
                                   name="ifsc_code"
                                   value="{{ old('ifsc_code') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   
                                   required>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="upi_id" class="block text-sm font-medium text-gray-700 mb-2">
                                UPI ID
                            </label>
                            <input type="text"
                                   id="upi_id"
                                   name="upi_id"
                                   value="{{ old('upi_id') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="example@upi">
                            <p class="text-sm text-gray-500 mt-1">Optional</p>
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       id="is_primary"
                                       name="is_primary"
                                       value="1"
                                       {{ old('is_primary') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="is_primary" class="block text-sm font-medium text-gray-700">
                                    Set as Primary Account
                                </label>
                                <p class="text-sm text-gray-500">This will be your default payment account</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="cheque_document" class="block text-sm font-medium text-gray-700 mb-2">
                            Cancelled Cheque <span class="text-red-500">*</span>
                        </label>
                        <input type="file"
                               id="cheque_document"
                               name="cheque_document"
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        <p class="text-sm text-gray-500 mt-1">Supported formats: PDF, JPG, PNG (Max: 2MB)</p>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-8">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="terms_accepted" 
                               name="terms_accepted" 
                               value="1"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                               required>
                        <label for="terms_accepted" class="ml-2 block text-sm text-gray-900">
                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a> and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a> <span class="text-red-500">*</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Submit for Verification
                    </button>
                </div>
            </form>

            <!-- Current Status Display -->
            <div class="mt-8 pt-8 border-t">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Current Status</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Application Status:</p>
                            <p class="font-medium 
                                @if($vendor->status === 'active') text-green-600
                                @elseif($vendor->status === 'blocked') text-red-600
                                @else text-yellow-600 @endif">
                                {{ ucfirst($vendor->status) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Onboarding Status:</p>
                            <p class="font-medium text-gray-800">{{ ucfirst($vendor->onboarding_status) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .container {
        max-width: 1200px;
    }
    
    input:focus, select:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .required::after {
        content: " *";
        color: #ef4444;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addPocButton = document.getElementById('add-poc');
        const additionalPocsContainer = document.getElementById('additional-pocs');
        
        // Counter for new POCs
        let pocCounter = {{ old('contacts') ? count(old('contacts')) : 1 }};
        
        // Add new POC
        addPocButton.addEventListener('click', function() {
            const newPocEntry = document.createElement('div');
            newPocEntry.className = 'poc-entry mb-4 p-4 border rounded-md';
            newPocEntry.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-medium text-gray-700">Additional Contact #${pocCounter}</h3>
                    <button type="button" class="remove-poc text-red-600 hover:text-red-800 text-sm font-medium">
                        Remove
                    </button>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="contacts[${pocCounter}][name]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" 
                               name="contacts[${pocCounter}][email]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" 
                               name="contacts[${pocCounter}][phone]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                        <input type="text" 
                               name="contacts[${pocCounter}][designation]" 
                               placeholder="e.g., Accounts, Operations"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            `;
            
            additionalPocsContainer.appendChild(newPocEntry);
            pocCounter++;
            
            // Add event listener to the new remove button
            const removeButton = newPocEntry.querySelector('.remove-poc');
            removeButton.addEventListener('click', function() {
                newPocEntry.remove();
            });
        });
        
        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-poc').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.poc-entry').remove();
            });
        });
    });
</script>

@endsection
