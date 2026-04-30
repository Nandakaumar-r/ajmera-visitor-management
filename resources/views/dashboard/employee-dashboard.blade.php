@extends('layouts.dashboard')

@section('content')
@php
$currentHour = \Carbon\Carbon::now()->format('H');
if ($currentHour < 12) {
    $greeting='Good morning' ;
    } elseif ($currentHour < 18) {
    $greeting='Good afternoon' ;
    } else {
    $greeting='Good evening' ;
    }
    @endphp

    <div class="p-6 space-y-6 bg-gray-50">
    <!-- Greeting Section -->
    <div class="bg-white rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $greeting }}, {{ ucfirst(Auth::user()->name) }}
                </h1>
                <p class="text-sm text-gray-600">Welcome to your dashboard</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">{{ now()->format('l, F j, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Hours -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Hours</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $dashboardStats['totalHours'] ?? '0' }} hrs</p>
                </div>
            </div>
        </div>

        <!-- Leave Balance -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Leave Balance</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $totalLeaveBalance }} days</p>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Attendance Rate</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $dashboardStats['attendanceRate'] ?? '0' }}%</p>
                </div>
            </div>
        </div>

        <!-- Next Holiday -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-yellow-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Next Holiday</p>
                    <p class="text-lg font-semibold text-gray-900">
                        @if($upcomingHolidays->isNotEmpty())
                        {{ $upcomingHolidays->first()->date->format('M j') }}
                        @else
                        -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Attendance Card -->
        <div class="bg-white rounded-lg shadow-sm relative overflow-hidden group">
            <div class="absolute inset-y-0 left-0 w-1 bg-[#ff6d1f]"></div>
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#ff6d1f]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#ff6d1f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Today's Attendance</h3>
                        <p class="text-sm text-gray-500">{{ $dashboardStats['todayAttendance'] ?? 'Not Marked' }}</p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="text-sm text-[#ff6d1f] hover:text-[#ff6d1f]/80 flex items-center justify-between group-hover:underline">
                    Mark Attendance
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>


        <!-- Modal Overlay -->
        <!-- Card with Sign In / Sign Out Button -->
        <div class="p-4 border bg-white rounded-lg relative overflow-hidden shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1 bg-[#f5a524]"></div>
            <p id="currentDate" class="font-semibold"></p>
            <p id="dayName" class="text-gray-500"></p>
            <p id="clock" class="text-xl font-bold mt-2"></p>

            <!-- Buttons side by side -->
            <div class="w-full flex justify-between mt-2 gap-1">
                <button id="viewSamplesBtn"
                    class="bg-green-600 text-white px-1 py-1 rounded-lg hover:bg-green-700 hidden flex-1">
                    View Swipes
                </button>

                <button id="openModalBtn"
                    class="bg-blue-600 text-white px-1 py-1 rounded-lg hover:bg-blue-700 flex-1">
                    Sign In
                </button>

                <button id="signOutBtn"
                    class="hidden bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex-1">
                    Sign Out
                </button>
            </div>
        </div>

        <!-- Samples Modal -->
        <div id="samplesModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white w-[700px] rounded-xl shadow-lg relative p-6">
                <!-- Close Button -->
                <button id="closeSamplesBtn"
                    class="absolute top-4 right-4 text-gray-500 hover:text-red-600 text-3xl font-bold">&times;</button>

                <h3 class="text-l font-semibold mb-2">
                    Date: {{ \Carbon\Carbon::now()->format('d M, Y') }}
                </h3>

                <!-- <h3 class="text-l font-semibold mb-4">
                    Shift Time 09:30 to 18:30
                </h3> -->

                <table class="w-full border text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">Sign In Time</th>
                            <th class="border px-3 py-2">Sign Out Time</th>
                            <th class="border px-3 py-2">Work Type</th>
                            <th class="border px-3 py-2">Client Name</th>
                        </tr>
                    </thead>
                    <tbody id="samplesTableBody">
                        <!-- Filled dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =======================
 Main Sign-In Modal
======================= -->
        <div id="signinModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white w-[95%] max-w-[750px] rounded-xl shadow-lg relative p-6 md:p-8 max-h-[90vh] overflow-y-auto">

                <!-- Close Button -->
                <button id="closeModalBtn"
                    class="absolute top-4 right-4 text-gray-500 hover:text-red-600 text-3xl md:text-4xl font-bold">&times;</button>

                <!-- Modal Content -->
                <div class="flex flex-col md:flex-row gap-6 md:gap-8">

                    <!-- Left Content -->
                    <div class="flex-1">
                        <p class="text-gray-500 mb-1 text-sm md:text-base">You are not signed in yet.</p>
                        <h2 class="text-xl md:text-2xl font-semibold mb-6">Tell us your work location.</h2>

                        <form id="signinForm" x-data="wfhForm()" class="space-y-4" method="POST">

                            <!-- Dropdown -->
                            <div>
                                <label for="location" class="block mb-2 font-medium">Enter Sign-In Location
                                    <span class="text-red-500">*</span></label>
                                <select id="location" name="work_location" class="w-full border rounded-lg p-2">
                                    <option value="">Select</option>
                                    <option value="client">Client Location</option>
                                    <option value="office">Office</option>
                                    <option value="onduty">On-Duty</option>
                                    <option value="wfh">Work from Home</option>
                                </select>
                            </div>

                            <!-- Camera Section -->
                            <div>
                                <label class="block mb-2 font-medium">Capture Photo <span class="text-red-500">*</span></label>
                                <button type="button"
                                    onclick="openCameraModal()"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    🎥 Open Camera
                                </button>

                                <!-- Preview -->
                                <img id="capturedImage" class="mt-2 hidden w-12 h-12 max-h-64 rounded-lg border" />

                                <!-- Hidden Input -->
                                <!-- Hidden Input for Image Path -->
                                <input type="hidden" name="captured_photo_path" id="capturedPhotoPath">
                            </div>

                            <!-- Remarks -->
                            <div>
                                <label for="remarks" class="block mb-2 font-medium">Client Name
                                    <span class="text-red-500">*</span></label>
                                <textarea id="remarks" name="remarks" rows="1" placeholder="Enter Reason" required
                                    class="w-full border rounded-lg p-2"></textarea>
                            </div>

                            <!-- Work Location -->
                            <div>
                                <label for="current_location" class="block text-sm font-medium text-gray-700 mb-2">Work Location Address <span class="text-red-500">*</span></label>
                                <input type="text"
                                    id="current_location"
                                    name="current_location"
                                    readonly
                                    value="{{ old('current_location') }}"
                                    placeholder="Enter your work from home address"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_location') border-red-500 @enderror">
                                @error('current_location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- Location Button -->
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

                                <!-- Errors -->
                                <div x-show="locationError" class="mt-2 text-sm text-red-600" x-text="locationError"></div>
                                <div x-show="locationSuccess" class="mt-2 text-sm text-green-600">✓ Location captured successfully</div>
                            </div>

                            <!-- Hidden Location Fields -->
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">


                            <!-- Sign In Button -->
                            <button id="submitBtn" type="submit" disabled
                                class="w-full mt-4 bg-blue-600 text-white py-2 rounded-lg opacity-50 cursor-not-allowed">
                                Sign In
                            </button>
                        </form>
                    </div>

                    <!-- Right Illustration (hidden on mobile) -->
                    <div class="flex-1 hidden md:flex justify-center items-center">
                        <img src="https://cdn-icons-png.flaticon.com/512/6393/6393161.png" alt="location"
                            class="w-40 h-40 md:w-56 md:h-56 opacity-80">
                    </div>
                </div>
            </div>
        </div>


        <!-- =======================
 Camera Modal
======================= -->
        <div id="cameraModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50">
            <div class="bg-white w-[95%] max-w-[500px] rounded-xl shadow-lg relative p-6">

                <!-- Close Button -->
                <button onclick="closeCameraModal()"
                    class="absolute top-4 right-4 text-gray-500 hover:text-red-600 text-3xl md:text-4xl font-bold">&times;</button>

                <h2 class="text-lg md:text-xl font-semibold mb-4">Capture Photo</h2>

                <!-- Video -->
                <video id="cameraStream" autoplay playsinline class="w-full h-64 object-cover border rounded-lg mb-3"></video>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="button" onclick="capturePhoto()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        📸 Take Snapshot
                    </button>
                    <button type="button" onclick="closeCameraModal()"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                </div>

                <!-- Hidden Canvas -->
                <canvas id="capturedCanvas" class="hidden"></canvas>
            </div>
        </div>


        <script>
            let stream;

            function openCameraModal() {
                document.getElementById("cameraModal").classList.remove("hidden");

                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(s => {
                        stream = s;
                        document.getElementById("cameraStream").srcObject = stream;
                    })
                    .catch(err => {
                        alert("Camera access denied: " + err.message);
                    });
            }

            function closeCameraModal() {
                document.getElementById("cameraModal").classList.add("hidden");
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            }

            function capturePhoto() {
                let video = document.getElementById("cameraStream");
                let canvas = document.getElementById("capturedCanvas");
                let img = document.getElementById("capturedImage");
                let inputPath = document.getElementById("capturedPhotoPath");

                // Set canvas size to video size
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Draw frame on canvas
                let context = canvas.getContext("2d");
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert to Blob
                canvas.toBlob(function(blob) {
                    let formData = new FormData();
                    formData.append("photo", blob, "signin.png");

                    // Send to backend
                    fetch("/upload-photo", {
                            method: "POST",
                            body: formData,
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.path) {
                                // Show preview in main modal
                                img.src = data.url;
                                img.classList.remove("hidden");

                                // Save path in hidden input
                                inputPath.value = data.path;

                                // Enable submit button
                                let submitBtn = document.getElementById("submitBtn");
                                submitBtn.disabled = false;
                                submitBtn.classList.remove("opacity-50", "cursor-not-allowed");

                                // ✅ Close camera modal after capture
                                closeCameraModal();
                            }
                        })
                        .catch(err => alert("Upload failed: " + err));
                }, "image/png");
            }
        </script>



        <script>
            const modal = document.getElementById("signinModal");
            const openModalBtn = document.getElementById("openModalBtn");
            const closeModalBtn = document.getElementById("closeModalBtn");
            const locationSelect = document.getElementById("location");
            const submitBtn = document.getElementById("submitBtn");
            const signOutBtn = document.getElementById("signOutBtn");
            const capturedPhotoPathInput = document.getElementById("capturedPhotoPath"); // <-- added


            // Open modal
            openModalBtn.addEventListener("click", () => {
                modal.classList.remove("hidden");
            });

            // Close modal
            closeModalBtn.addEventListener("click", () => {
                modal.classList.add("hidden");
            });

            // Enable Sign In only if location is selected
            locationSelect.addEventListener("change", () => {
                if (locationSelect.value) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add("opacity-50", "cursor-not-allowed");
                }
            });

            // Handle Sign In form submit
            document.getElementById("signinForm").addEventListener("submit", async function(e) {
                e.preventDefault();

                const formData = {
                    work_location: document.getElementById("location").value,
                    current_location: document.getElementById("current_location").value,
                    remarks: document.getElementById("remarks").value,
                    captured_photo_path: capturedPhotoPathInput.value,
                    latitude: document.getElementById("latitude")?.value || '', // NEW
                    longitude: document.getElementById("longitude")?.value || '' // NEW
                };

                try {
                    const response = await fetch("{{ route('workfromhome.signin') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(formData)
                    });

                    // ⚠️ if backend returns HTML (like a 419 CSRF error), this will fail
                    const text = await response.text();
                    let result;
                    try {
                        result = JSON.parse(text);
                    } catch (err) {
                        console.error("Non-JSON response:", text);
                        alert("Server error. Please check logs.");
                        return;
                    }

                    if (result.success) {
                        alert(result.message);
                        modal.classList.add("hidden");
                        openModalBtn.classList.add("hidden");
                        signOutBtn.classList.remove("hidden");
                        viewSamplesBtn.classList.remove("hidden");
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error(error);
                    alert("Error submitting data");
                }
            });

            // Handle Sign Out
            signOutBtn.addEventListener("click", async (e) => {
                e.preventDefault();

                if (!confirm("Are you sure you want to Sign Out ?")) {
                    return;
                }

                try {
                    const response = await fetch("{{ route('workfromhome.signout') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    });

                    const text = await response.text();
                    let result;
                    try {
                        result = JSON.parse(text);
                    } catch (err) {
                        console.error("Non-JSON response:", text);
                        alert("Server error. Please check logs.");
                        return;
                    }

                    if (result.success) {
                        alert(result.message);
                        signOutBtn.classList.add("hidden");
                        openModalBtn.classList.remove("hidden");
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error(error);
                    alert("Error signing out");
                }
            });

            // --- Dynamic Date & Time ---
            function updateDateTime() {
                const now = new Date();

                const options = {
                    day: "numeric",
                    month: "long",
                    year: "numeric"
                };
                document.getElementById("currentDate").textContent = now.toLocaleDateString("en-GB", options);

                const weekday = now.toLocaleDateString("en-GB", {
                    weekday: "long"
                });
                document.getElementById("dayName").textContent = `${weekday} | General Shift`;

                let hours = String(now.getHours()).padStart(2, "0");
                let minutes = String(now.getMinutes()).padStart(2, "0");
                let seconds = String(now.getSeconds()).padStart(2, "0");
                document.getElementById("clock").textContent = `${hours} : ${minutes} : ${seconds}`;
            }
            updateDateTime();
            setInterval(updateDateTime, 1000);

            const viewSamplesBtn = document.getElementById("viewSamplesBtn");
            const samplesModal = document.getElementById("samplesModal");
            const closeSamplesBtn = document.getElementById("closeSamplesBtn");
            const samplesTableBody = document.getElementById("samplesTableBody");

            async function checkUserRecords() {
                try {
                    const response = await fetch("{{ route('workfromhome.samples') }}", {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });

                    const result = await response.json();

                    if (result.success && result.data.length > 0) {
                        viewSamplesBtn.classList.remove("hidden");
                        const lastRecord = result.data[result.data.length - 1];
                        if (!lastRecord.sign_out_time) {
                            signOutBtn.classList.remove("hidden");
                            openModalBtn.classList.add("hidden");
                        }
                    } else {
                        viewSamplesBtn.classList.add("hidden");
                    }
                } catch (error) {
                    console.error("Error checking user records:", error);
                }
            }
            checkUserRecords();

            // --- Open Samples Modal ---
            viewSamplesBtn.addEventListener("click", async () => {
                try {
                    const response = await fetch("{{ route('workfromhome.samples') }}", {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    });

                    const result = await response.json();
                    samplesTableBody.innerHTML = "";

                    if (result.success && result.data.length > 0) {
                        const formatDateTime = (datetime) => {
                            if (!datetime) return '-';
                            const dt = new Date(datetime);
                            return dt.toLocaleString('en-US', {
                                month: 'short',
                                day: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                        };

                        result.data.forEach(row => {
                            samplesTableBody.innerHTML += `
                        <tr>
                            <td class="border px-3 py-2">${formatDateTime(row.sign_in_time)}</td>
                            <td class="border px-3 py-2">${formatDateTime(row.sign_out_time)}</td>
                            <td class="border px-3 py-2">${row.work_location}</td>
                            <td class="border px-3 py-2">${row.remarks}</td>
                        </tr>`;
                        });
                    } else {
                        samplesTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-3">No records found</td></tr>`;
                    }

                    samplesModal.classList.remove("hidden");
                } catch (error) {
                    console.error(error);
                    alert("Error fetching samples");
                }
            });

            closeSamplesBtn.addEventListener("click", () => {
                samplesModal.classList.add("hidden");
            });
        </script>


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
                                // update hidden inputs
                                document.getElementById("latitude").value = this.latitude;
                                document.getElementById("longitude").value = this.longitude;

                                // Call backend reverse geocode
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
                        // Call your Laravel proxy route instead of Nominatim directly
                        fetch(`/location/reverse?lat=${lat}&lon=${lng}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.display_name) {
                                    document.getElementById('current_location').value = data.display_name;
                                } else {
                                    // fallback
                                    document.getElementById('current_location').value =
                                        `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                                }
                            })
                            .catch(error => {
                                console.error('Reverse geocoding failed:', error);
                                document.getElementById('current_location').value =
                                    `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                            });
                    }
                }
            }

            // Disable location button if browser doesn’t support it
            document.addEventListener('DOMContentLoaded', function() {
                if (!navigator.geolocation) {
                    const locationButton = document.querySelector('[x-on\\:click="getLocation()"]');
                    if (locationButton) {
                        locationButton.style.display = 'none';
                    }

                    const workLocationDiv = document.getElementById('current_location').parentElement;
                    const warningDiv = document.createElement('div');
                    warningDiv.className = 'mt-2 text-sm text-amber-600';
                    warningDiv.innerHTML = '⚠️ Geolocation is not supported by this browser. Please enter your address manually.';
                    workLocationDiv.appendChild(warningDiv);
                }
            });
        </script>

        <!-- New Request Card -->
        <div class="bg-white rounded-lg shadow-sm relative overflow-hidden group">
            <div class="absolute inset-y-0 left-0 w-1 bg-[#4b91e2]"></div>
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#4b91e2]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#4b91e2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">New Request</h3>
                        <p class="text-sm text-gray-500">Submit resignation request</p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="text-sm text-[#4b91e2] hover:text-[#4b91e2]/80 flex items-center justify-between group-hover:underline">
                    Create request
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- My Requests Card -->
        <div class="bg-white rounded-lg shadow-sm relative overflow-hidden group">
            <div class="absolute inset-y-0 left-0 w-1 bg-[#50e2c3]"></div>
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#50e2c3]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#50e2c3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">My Requests</h3>
                        <p class="text-sm text-gray-500">View request history</p>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="text-sm text-[#50e2c3] hover:text-[#50e2c3]/80 flex items-center justify-between group-hover:underline">
                    View history
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Help Center Card -->
        <!-- <div class="bg-white rounded-lg shadow-sm relative overflow-hidden group">
            <div class="absolute inset-y-0 left-0 w-1 bg-[#f5a524]"></div>
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-lg bg-[#f5a524]/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#f5a524]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Help Center</h3>
                        <p class="text-sm text-gray-500">Get support & resources</p>
                    </div>
                </div>
                <a href="{{ route('help-requests.index') }}" class="text-sm text-[#f5a524] hover:text-[#f5a524]/80 flex items-center justify-between group-hover:underline">
                    View help
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div> -->
    </div>

    <!-- Additional Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's WFH Employees -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Today's WFH Employees</h3>
                <div class="space-y-3">
                    @forelse($wfhEmployees as $employee)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ substr($employee->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $employee->name }}</p>
                            <p class="text-xs text-gray-500">{{ $employee->department }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No employees working from home today</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Upcoming Holidays -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Holidays</h3>
                <div class="space-y-3">
                    @forelse($upcomingHolidays as $holiday)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $holiday->name }}</p>
                            <p class="text-xs text-gray-500">{{ $holiday->date->format('l, F j, Y') }}</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $holiday->date->diffForHumans() }}
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No upcoming holidays</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Posts</h3>
                <div class="space-y-4">
                    @forelse($posts->take(3) as $post)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ substr($post->user->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $post->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($post->content, 100) }}</p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                                <span class="text-xs text-gray-500">{{ $post->likes_count }} likes</span>
                                <span class="text-xs text-gray-500">{{ $post->comments_count }} comments</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No recent posts</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    </div>
    @endsection