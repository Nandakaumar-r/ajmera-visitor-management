<x-reception-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Visitor Registration') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="mb-8">
                    <div class="flex items-center justify-center space-x-8">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white bg-indigo-600 mb-2" id="step1-indicator">
                                <span class="text-white">1</span>
                            </div>
                            <span class="text-sm text-gray-600 text-center">Personal Info</span>
                        </div>
                        <div class="h-0.5 w-24 bg-gray-200"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-200 mb-2" id="step2-indicator">
                                <span>2</span>
                            </div>
                            <span class="text-sm text-gray-600 text-center">Photo & ID</span>
                        </div>
                        <div class="h-0.5 w-24 bg-gray-200"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-200 mb-2" id="step3-indicator">
                                <span>3</span>
                            </div>
                            <span class="text-sm text-gray-600 text-center">Review</span>
                        </div>
                    </div>
                </div>

                <form id="visitor-registration-form" class="space-y-6" action="{{ route('visitors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Step 1: Personal Information -->
                    <div id="step1" class="registration-step">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" id="first_name" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                       required>
                                <p id="first_name_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" id="last_name" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                       required>
                                <p id="last_name_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>

                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phone_number" id="phone_number" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                       required pattern="[0-9]{10}">
                                <p id="phone_number_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>

                            <div>
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose of Visit</label>
                                <input list="purpose-list" name="purpose" id="purpose" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                       required>
                                       <datalist id="purpose-list">
                                            <option value="Interview">Interview</option>
                                            <option value="Meeting">Meeting</option>
                                            <option value="Conference">Conference</option>
                                            <option value="Training">Training</option>
                                            <option value="Client Visit">Client Visit</option>
                                            <option value="Visiting">Visiting</option>
                                            <option value="Others">Others</option>
                                        </datalist>
                                <p id="purpose_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>

                            <div>
                                <label for="whom_to_visit" class="block text-sm font-medium text-gray-700 mb-2">Whom to Visit</label>
                                <select name="whom_to_visit" id="whom_to_visit" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                        required>
                                    <option value="">Select Employee</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <p id="whom_to_visit_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>

                            <div>
                                <label for="government_id_type" class="block text-sm font-medium text-gray-700 mb-2">ID Type</label>
                                <select name="government_id_type" id="government_id_type" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                        required>
                                    <option value="">Select ID Type</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Aadhaar Card">Aadhaar Card</option>
                                    <option value="PAN Card">PAN Card</option>
                                    <option value="Voter ID">Voter ID</option>
                                    <option value="Driving License">Driving License</option>
                                    <option value="Other">Other</option>
                                </select>
                                <p id="government_id_type_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>

                            <div>
                                <label for="government_id_last_digits" class="block text-sm font-medium text-gray-700 mb-2">Last 4 Digits of ID</label>
                                <input type="text" name="government_id_last_digits" id="government_id_last_digits" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                       pattern="\d{4}" required maxlength="4">
                                <p id="government_id_last_digits_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                                <input type="text" name="company" id="company" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                       maxlength="255">
                                <p id="company_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>
                            <div>
                                <label for="additional_details" class="block text-sm font-medium text-gray-700 mb-2">Additional Details</label>
                                <textarea name="additional_details" id="additional_details" rows="3"
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                                          maxlength="1000"></textarea>
                                <p id="additional_details_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="button" id="next-step1" 
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Next: Photo & ID
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Photo and ID -->
                    <div id="step2" class="registration-step hidden">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Photo and ID Verification</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Visitor Photo (Optional)</label>
                                <div class="mt-1 flex flex-col items-center justify-center border-2 border-gray-300 border-dashed rounded-md p-6">
                                    <video id="webcam" class="w-full h-auto" autoplay playsinline></video>
                                    <canvas id="canvasPreview" class="hidden w-full h-auto"></canvas>
                                    <div class="mt-4 flex space-x-4">
                                        <button type="button" id="startCamera" 
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                            Start Camera
                                        </button>
                                        <button type="button" id="capturePhoto" 
                                                class="hidden px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            Capture Photo
                                        </button>
                                        <button type="button" id="retakePhoto" 
                                                class="hidden px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                            Retake Photo
                                        </button>
                                    </div>
                                    <input type="hidden" name="photo" id="photoInput">
                                </div>
                            </div>

                            <div class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Visiting Card (Optional)</label>
                                <div class="mt-1 flex flex-col items-center justify-center border-2 border-gray-300 border-dashed rounded-md p-6">
                                    <div class="w-full text-center">
                                        <button type="button" id="start-visiting-card-camera" 
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                            Capture Visiting Card
                                        </button>
                                        <button type="button" id="retake-visiting-card" 
                                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 hidden">
                                            Retake Card
                                        </button>
                                    </div>
                                    <div id="visiting-card-preview-container" class="mt-4 hidden">
                                        <img id="visiting-card-preview" src="" alt="Visiting Card Preview" 
                                             class="max-w-full max-h-48 object-contain">
                                    </div>
                                </div>
                                <input type="hidden" name="visiting_card" id="visiting-card-input">
                                <p id="visiting_card_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Signature (Optional)</label>
                                <div class="mt-1 border-2 border-gray-300 border-dashed rounded-md p-6 flex flex-col items-center justify-center">
                                    <canvas id="signature-pad" class="w-full h-48 bg-white border border-gray-300"></canvas>
                                    <div class="mt-4 flex space-x-4">
                                        <button type="button" id="clear-signature" 
                                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                            Clear
                                        </button>
                                        <button type="button" id="save-signature" 
                                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            Save Signature
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="signature" id="signature-input">
                                <p id="signature_error" class="text-red-500 text-sm mt-1 hidden"></p>
                            </div>
                        </div>
                        <div class="flex justify-between mt-6">
                            <button type="button" id="prev-step2" 
                                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Previous: Personal Info
                            </button>
                            <button type="button" id="next-step2" 
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Next: Review
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Review -->
                    <div id="step3" class="registration-step hidden">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Review Information</h3>
                        <div id="review-content" class="space-y-4">
                            <!-- Dynamically populated review content -->
                        </div>
                        <div class="flex justify-between mt-6">
                            <button type="button" id="prev-step3" 
                                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Previous: Photo & ID
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Submit Registration
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        #stepper li {
            position: relative;
        }
        #stepper li:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 100%;
            height: 1px;
            background-color: #e5e7eb;
            z-index: -1;
        }
        .signature-pad {
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            height: 250px;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#whom_to_visit').select2({
                placeholder: 'Search for a person to visit',
                allowClear: true,
                width: '100%'
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize webcam
            const webcam = document.getElementById('webcam');
            const photoCanvas = document.getElementById('canvasPreview');
            const startCameraBtn = document.getElementById('startCamera');
            const capturePhotoBtn = document.getElementById('capturePhoto');
            const retakePhotoBtn = document.getElementById('retakePhoto');
            const photoInput = document.getElementById('photoInput');
            let stream = null;

            startCameraBtn.addEventListener('click', async function() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                            facingMode: 'user'
                        } 
                    });
                    webcam.srcObject = stream;
                    webcam.classList.remove('hidden');
                    photoCanvas.classList.add('hidden');
                    startCameraBtn.classList.add('hidden');
                    capturePhotoBtn.classList.remove('hidden');
                    retakePhotoBtn.classList.add('hidden');
                } catch (err) {
                    console.error('Error accessing camera:', err);
                    alert('Could not access the camera. Please ensure you have granted camera permissions.');
                }
            });

            capturePhotoBtn.addEventListener('click', function() {
                photoCanvas.width = webcam.videoWidth;
                photoCanvas.height = webcam.videoHeight;
                
                const context = photoCanvas.getContext('2d');
                context.drawImage(webcam, 0, 0, photoCanvas.width, photoCanvas.height);
                
                const imageData = photoCanvas.toDataURL('image/jpeg', 0.8);
                photoInput.value = imageData;
                
                webcam.classList.add('hidden');
                photoCanvas.classList.remove('hidden');
                capturePhotoBtn.classList.add('hidden');
                retakePhotoBtn.classList.remove('hidden');
                
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });

            retakePhotoBtn.addEventListener('click', async function() {
                photoInput.value = '';
                
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                            facingMode: 'user'
                        } 
                    });
                    webcam.srcObject = stream;
                    
                    webcam.classList.remove('hidden');
                    photoCanvas.classList.add('hidden');
                    capturePhotoBtn.classList.remove('hidden');
                    retakePhotoBtn.classList.add('hidden');
                } catch (err) {
                    console.error('Error restarting camera:', err);
                    alert('Could not restart the camera. Please refresh the page and try again.');
                }
            });

            // Initialize signature pad
            const signaturePadCanvas = document.getElementById('signature-pad');
            const signatureInput = document.getElementById('signature-input');
            const clearSignatureBtn = document.getElementById('clear-signature');
            const saveSignatureBtn = document.getElementById('save-signature');
            const signatureError = document.getElementById('signature_error');

            // Resize canvas to match parent container
            function resizeSignaturePad() {
                const canvas = signaturePadCanvas;
                const container = canvas.closest('.border-2');
                canvas.width = container.clientWidth - 40; // Subtract padding
                canvas.height = 192; // Fixed height of 48 * 4 (tailwind h-48)
                canvas.getContext('2d').scale(1, 1);
            }

            // Initialize signature pad
            const signaturePad = new SignaturePad(signaturePadCanvas, {
                backgroundColor: 'rgb(255, 255, 255)', // white background
                penColor: 'rgb(0, 0, 0)', // black pen
                minWidth: 1,
                maxWidth: 3,
                throttle: 16, // 60 fps
                minPointDistance: 3
            });

            // Resize on initialization and window resize
            resizeSignaturePad();
            window.addEventListener('resize', resizeSignaturePad);

            // Clear signature
            clearSignatureBtn.addEventListener('click', function() {
                signaturePad.clear();
                signatureInput.value = '';
                signatureError.classList.add('hidden');
                saveSignatureBtn.textContent = 'Save Signature';
                saveSignatureBtn.disabled = false;
                saveSignatureBtn.classList.remove('bg-gray-400');
                saveSignatureBtn.classList.add('bg-green-600');
            });

            // Save signature
            saveSignatureBtn.addEventListener('click', function() {
                if (signaturePad.isEmpty()) {
                    signatureError.textContent = 'Please provide a signature';
                    signatureError.classList.remove('hidden');
                    return;
                }

                const signatureDataUrl = signaturePad.toDataURL('image/png');
                signatureInput.value = signatureDataUrl;
                signatureError.classList.add('hidden');

                saveSignatureBtn.textContent = 'Signature Saved';
                saveSignatureBtn.disabled = true;
                saveSignatureBtn.classList.remove('bg-green-600');
                saveSignatureBtn.classList.add('bg-gray-400');
            });

            const visitorForm = document.getElementById('visitor-registration-form');
            const steps = ['step1', 'step2', 'step3'];
            const stepIndicators = ['step1-indicator', 'step2-indicator', 'step3-indicator'];
            let currentStep = 0;

            // Validation Functions
            const validations = {
                step1: function() {
                    const firstName = document.getElementById('first_name');
                    const lastName = document.getElementById('last_name');
                    const phoneNumber = document.getElementById('phone_number');
                    const purpose = document.getElementById('purpose');
                    const whomToVisit = document.getElementById('whom_to_visit');
                    
                    // Reset previous error states
                    const fields = ['first_name', 'last_name', 'phone_number', 'purpose', 'whom_to_visit', 'government_id_type', 'government_id_last_digits'];
                    fields.forEach(field => {
                        const input = document.getElementById(field);
                        const error = document.getElementById(`${field}_error`);
                        if (input) input.classList.remove('border-red-500');
                        if (error) error.classList.add('hidden');
                    });

                    let isValid = true;
                    
                    // First Name Validation
                    if (!firstName?.value?.trim()) {
                        isValid = false;
                        firstName?.classList.add('border-red-500');
                        const errorEl = document.getElementById('first_name_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please enter your first name.';
                            errorEl.classList.remove('hidden');
                        }
                    }

                    // Last Name Validation
                    if (!lastName?.value?.trim()) {
                        isValid = false;
                        lastName?.classList.add('border-red-500');
                        const errorEl = document.getElementById('last_name_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please enter your last name.';
                            errorEl.classList.remove('hidden');
                        }
                    }

                    // Phone Number Validation
                    const phoneRegex = /^[0-9]{10}$/;
                    if (!phoneNumber?.value?.trim()) {
                        isValid = false;
                        phoneNumber?.classList.add('border-red-500');
                        const errorEl = document.getElementById('phone_number_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please enter your phone number.';
                            errorEl.classList.remove('hidden');
                        }
                    } else if (!phoneRegex.test(phoneNumber.value.trim())) {
                        isValid = false;
                        phoneNumber?.classList.add('border-red-500');
                        const errorEl = document.getElementById('phone_number_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please enter a valid 10-digit phone number.';
                            errorEl.classList.remove('hidden');
                        }
                    }

                    // Purpose Validation
                    if (!purpose?.value?.trim()) {
                        isValid = false;
                        purpose?.classList.add('border-red-500');
                        const errorEl = document.getElementById('purpose_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please enter a purpose of visit.';
                            errorEl.classList.remove('hidden');
                        }
                    }

                    // Whom to Visit Validation
                    if (!whomToVisit?.value) {
                        isValid = false;
                        whomToVisit?.classList.add('border-red-500');
                        const errorEl = document.getElementById('whom_to_visit_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please select whom to visit.';
                            errorEl.classList.remove('hidden');
                        }
                    }

                    // ID Type Validation
                    const governmentIdType = document.getElementById('government_id_type');
                    if (!governmentIdType?.value) {
                        isValid = false;
                        governmentIdType?.classList.add('border-red-500');
                        const errorEl = document.getElementById('government_id_type_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please select an ID type.';
                            errorEl.classList.remove('hidden');
                        }
                    }

                    // ID Last Digits Validation
                    const governmentIdLastDigits = document.getElementById('government_id_last_digits');
                    if (!governmentIdLastDigits?.value) {
                        isValid = false;
                        governmentIdLastDigits?.classList.add('border-red-500');
                        const errorEl = document.getElementById('government_id_last_digits_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please enter the last 4 digits of your ID.';
                            errorEl.classList.remove('hidden');
                        }
                    }

                    // Additional Details Validation
                    const additionalDetails = document.getElementById('additional_details');
                    if (additionalDetails?.value?.trim()) {
                        isValid = true;
                        additionalDetails?.classList.add('border-red-500');
                        const errorEl = document.getElementById('additional_details_error');
                        if (errorEl) {
                            errorEl.textContent = 'Please enter additional details.';
                            errorEl.classList.remove('hidden');
                        }
                    }

                    return isValid;
                },
                step2: function() {
                    const signatureInput = document.getElementById('signature-input');
                    const startVisitingCardCamera = document.getElementById('start-visiting-card-camera');
                    const retakeVisitingCardBtn = document.getElementById('retake-visiting-card');
                    const visitingCardPreviewContainer = document.getElementById('visiting-card-preview-container');
                    const visitingCardPreview = document.getElementById('visiting-card-preview');
                    const visitingCardInput = document.getElementById('visiting-card-input');
                    let visitingCardStream = null;

                    // Clear previous error messages
                    document.getElementById('signature_error').classList.add('hidden');

                    // Create a video element for the visiting card camera
                    const visitingCardVideo = document.createElement('video');
                    visitingCardVideo.setAttribute('autoplay', '');
                    visitingCardVideo.setAttribute('playsinline', '');
                    visitingCardVideo.classList.add('w-full', 'h-auto', 'mb-4');
                    
                    // Function to start visiting card camera
                    async function startVisitingCardCapture() {
                        try {
                            visitingCardStream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    width: { ideal: 1280 },
                                    height: { ideal: 720 },
                                    facingMode: 'environment' // Use back camera by default for document capture
                                }
                            });

                            visitingCardVideo.srcObject = visitingCardStream;
                            visitingCardPreviewContainer.insertBefore(visitingCardVideo, visitingCardPreview);
                            visitingCardVideo.classList.remove('hidden');
                            visitingCardPreview.classList.add('hidden');
                            startVisitingCardCamera.textContent = 'Capture Card';
                            retakeVisitingCardBtn.classList.add('hidden');
                            visitingCardPreviewContainer.classList.remove('hidden');

                        } catch (error) {
                            console.error('Error accessing camera:', error);
                            alert('Could not access the camera. Please ensure you have granted camera permissions.');
                        }
                    }

                    // Signature is now optional
                    // If signature is drawn, validate and save it
                    if (!signaturePad.isEmpty()) {
                        const signatureDataUrl = signaturePad.toDataURL();
                        signatureInput.value = signatureDataUrl;
                    }

                    return true;
                },
                step3: function() {
                    return true;
                }
            };

            // Function to capture visiting card
            function captureVisitingCard() {
                const canvas = document.createElement('canvas');
                canvas.width = visitingCardVideo.videoWidth;
                canvas.height = visitingCardVideo.videoHeight;
                const context = canvas.getContext('2d');
                context.drawImage(visitingCardVideo, 0, 0, canvas.width, canvas.height);

                // Set the captured image as preview
                visitingCardPreview.src = canvas.toDataURL('image/jpeg', 0.8);
                visitingCardInput.value = canvas.toDataURL('image/jpeg', 0.8);

                // Update UI
                visitingCardVideo.classList.add('hidden');
                visitingCardPreview.classList.remove('hidden');
                startVisitingCardCamera.textContent = 'Start Camera';
                retakeVisitingCardBtn.classList.remove('hidden');

                // Stop the camera stream
                if (visitingCardStream) {
                    visitingCardStream.getTracks().forEach(track => track.stop());
                    visitingCardStream = null;
                }
            }

            const startVisitingCardCamera = document.getElementById('start-visiting-card-camera');
            const retakeVisitingCardBtn = document.getElementById('retake-visiting-card');
            let visitingCardStream = null;

            // Event listener for starting/capturing visiting card
            startVisitingCardCamera.addEventListener('click', async function() {
                if (!visitingCardStream) {
                    await startVisitingCardCapture();
                } else {
                    captureVisitingCard();
                }
            });

            // Event listener for retaking visiting card photo
            retakeVisitingCardBtn.addEventListener('click', async function() {
                visitingCardInput.value = '';
                visitingCardPreview.src = '';
                await startVisitingCardCapture();
            });

            // Clean up function for visiting card camera
            function cleanupVisitingCardCamera() {
                if (visitingCardStream) {
                    visitingCardStream.getTracks().forEach(track => track.stop());
                    visitingCardStream = null;
                }
            }

            // Update Stepper Indicators
            function updateStepperIndicators(stepIndex) {
                stepIndicators.forEach((indicator, index) => {
                    const element = document.getElementById(indicator);
                    if (index === stepIndex) {
                        element.classList.remove('bg-gray-200', 'text-gray-500');
                        element.classList.add('bg-indigo-600', 'text-white');
                    } else if (index < stepIndex) {
                        element.classList.remove('bg-gray-200', 'text-gray-500');
                        element.classList.add('bg-green-500', 'text-white');
                    } else {
                        element.classList.remove('bg-indigo-600', 'text-white', 'bg-green-500');
                        element.classList.add('bg-gray-200', 'text-gray-500');
                    }
                });
            }

            // Show/Hide Steps
            function showStep(stepIndex) {
                // Validate current step before moving forward
                if (stepIndex > currentStep) {
                    const currentStepValidation = validations[`step${currentStep + 1}`];
                    if (currentStepValidation && !currentStepValidation()) {
                        return;
                    }
                }

                steps.forEach((step, index) => {
                    const element = document.getElementById(step);
                    if (index === stepIndex) {
                        element.classList.remove('hidden');
                    } else {
                        element.classList.add('hidden');
                    }
                });
                updateStepperIndicators(stepIndex);
                currentStep = stepIndex;
            }

            // Next Step Button Handlers
            document.getElementById('next-step1').addEventListener('click', () => {
                if (validations.step1()) {
                    showStep(1);
                }
            });

            document.getElementById('next-step2').addEventListener('click', () => {
                if (validations.step2()) {
                    populateReviewInformation();
                    showStep(2);
                }
            });

            // Previous Step Button Handlers
            document.getElementById('prev-step2').addEventListener('click', () => {
                showStep(0);
            });

            document.getElementById('prev-step3').addEventListener('click', () => {
                showStep(1);
            });

            // Form Submission Validation
            visitorForm.addEventListener('submit', async function(event) {
                cleanupVisitingCardCamera();
                event.preventDefault(); // Prevent default form submission
                console.log('Form submission attempted');

                try {
                    // Validate all steps
                    let isValid = true;
                    for (let i = 0; i < steps.length; i++) {
                        if (!validations[`step${i + 1}`]()) {
                            console.log(`Validation failed for step ${i + 1}`);
                            isValid = false;
                            showStep(i);
                            break;
                        }
                    }

                    if (isValid) {
                        console.log('Form is valid, preparing submission...');

                        // Get form data
                        const formData = new FormData(this);

                        // Add photo if captured
                        if (canvasPreview && canvasPreview.toDataURL) {
                            const photoData = canvasPreview.toDataURL('image/jpeg', 0.8);
                            console.log('Adding photo to form data');
                            formData.set('photo', photoData);
                        }

                        // Add visiting card if captured
                        const visitingCardCanvas = document.getElementById('visiting-card-canvas');
                        if (visitingCardCanvas && visitingCardCanvas.toDataURL) {
                            const visitingCardData = visitingCardCanvas.toDataURL('image/jpeg', 0.8);
                            console.log('Adding visiting card to form data');
                            formData.set('visiting_card', visitingCardData);
                        }

                        // Add signature if drawn
                        if (signaturePad && !signaturePad.isEmpty()) {
                            const signatureData = signaturePad.toDataURL('image/png');
                            console.log('Adding signature to form data');
                            formData.set('signature', signatureData);
                        }

                        // Convert FormData to a plain object for submission
                        const formObject = {};
                        formData.forEach((value, key) => {
                            formObject[key] = value;
                        });

                        // Create hidden inputs for base64 data
                        for (const [key, value] of Object.entries(formObject)) {
                            if (value && typeof value === 'string' && value.startsWith('data:image')) {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = key;
                                input.value = value;
                                this.appendChild(input);
                            }
                        }

                        console.log('Submitting form...');
                        this.submit();
                    }
                } catch (error) {
                    console.error('Error during form submission:', error);
                }
            });

            // Populate Review Information
            function populateReviewInformation() {
                const reviewContent = document.getElementById('review-content');
                const whomToVisitSelect = document.getElementById('whom_to_visit');
                const selectedOption = whomToVisitSelect.options[whomToVisitSelect.selectedIndex];
                const whomToVisitText = selectedOption ? selectedOption.text : '';

                reviewContent.innerHTML = `
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="text-md font-semibold text-gray-700 mb-2">Personal Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">First Name</p>
                                <p class="font-medium" id="review-first-name">${document.getElementById('first_name').value}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Last Name</p>
                                <p class="font-medium" id="review-last-name">${document.getElementById('last_name').value}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Contact Number</p>
                                <p class="font-medium" id="review-phone-number">${document.getElementById('phone_number').value}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Purpose of Visit</p>
                                <p class="font-medium" id="review-purpose">${document.getElementById('purpose').value}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Whom to Visit</p>
                                <p class="font-medium" id="review-whom-to-visit">${whomToVisitText}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-md mt-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-2">ID Verification</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">ID Type</p>
                                <p class="font-medium" id="review-id-type">${document.getElementById('government_id_type').value}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">ID Last Digits</p>
                                <p class="font-medium" id="review-id-last-digits">${document.getElementById('government_id_last_digits').value}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-md mt-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-2">Additional Details</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Visitor Photo</p>
                                <p class="font-medium">${canvasPreview.toDataURL() ? 'Captured' : 'Not Captured'}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Signature</p>
                                <p class="font-medium">${signaturePad.isEmpty() ? 'No Signature Provided' : 'Signature Captured'}</p>
                            </div>
                        </div>
                    </div>
                `;
            }

            window.addEventListener('beforeunload', cleanupVisitingCardCamera);
        });
    </script>
    @endpush
</x-reception-layout>
