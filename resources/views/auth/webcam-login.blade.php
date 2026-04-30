<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 p-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="text-center mb-4">
                <h2 class="text-2xl font-bold">Webcam Login</h2>
                <p class="text-gray-600 mt-1">Please position your face in the camera</p>
            </div>

            <div class="mb-4">
                <div id="webcam-container" class="relative">
                    <video id="webcam" autoplay playsinline class="w-full rounded-lg"></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    <div id="loading-overlay" class="hidden absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg">
                        <div class="text-white">
                            <svg class="animate-spin h-8 w-8 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="mt-2">Authenticating...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button id="capture" type="button" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Authenticate
                </button>
            </div>

            <div class="mt-4">
                <p class="text-center text-sm text-gray-600">
                    Or <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700">login with password</a>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');
            const captureButton = document.getElementById('capture');
            const loadingOverlay = document.getElementById('loading-overlay');
            let stream = null;

            // Start webcam
            async function startWebcam() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            width: 640,
                            height: 480,
                            facingMode: 'user'
                        } 
                    });
                    video.srcObject = stream;
                } catch (err) {
                    console.error('Error accessing webcam:', err);
                    alert('Unable to access webcam. Please ensure you have granted camera permissions.');
                }
            }

            // Capture image and authenticate
            async function authenticate() {
                if (!stream) {
                    alert('Webcam not initialized');
                    return;
                }

                loadingOverlay.classList.remove('hidden');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                // Draw current video frame to canvas
                canvas.getContext('2d').drawImage(video, 0, 0);
                
                // Get base64 image data
                const imageData = canvas.toDataURL('image/jpeg');

                try {
                    const response = await fetch('{{ route("webcam.login.process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            webcam_image: imageData
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message);
                    }
                } catch (err) {
                    console.error('Authentication error:', err);
                    alert('Authentication failed. Please try again.');
                } finally {
                    loadingOverlay.classList.add('hidden');
                }
            }

            // Event listeners
            captureButton.addEventListener('click', authenticate);

            // Start webcam when page loads
            startWebcam();

            // Cleanup when page unloads
            window.addEventListener('beforeunload', () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>
