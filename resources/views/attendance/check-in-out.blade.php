@push('styles')
<style>
    #webcam-container {
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
    }
    #webcam {
        width: 100%;
        border-radius: 0.5rem;
    }
    .webcam-overlay {
        position: relative;
    }
    .face-guide {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        border: 2px solid #4f46e5;
        border-radius: 50%;
        pointer-events: none;
    }
</style>
@endpush

<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        {{ $todayAttendance && $todayAttendance->check_in ? 'Check Out' : 'Check In' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        @if(!$todayAttendance || !$todayAttendance->check_in)
                            Please select your work mode and verify your identity to check in.
                        @else
                            @if($todayAttendance->work_mode === 'wfh')
                                Please verify your identity to check out.
                            @else
                                You can check out now.
                            @endif
                        @endif
                    </p>
                </div>

                @if(!$todayAttendance || !$todayAttendance->check_in)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Work Mode</label>
                        <div class="mt-2 space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="work_mode" value="office" class="form-radio" checked>
                                <span class="ml-2">Office</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="work_mode" value="wfh" class="form-radio">
                                <span class="ml-2">Work From Home</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div id="webcam-section" class="{{ (!$todayAttendance || !$todayAttendance->check_in) ? 'hidden' : '' }} mb-6">
                    <div id="webcam-container">
                        <div class="webcam-overlay">
                            <video id="webcam" autoplay playsinline></video>
                            <div class="face-guide"></div>
                        </div>
                        <canvas id="canvas" class="hidden"></canvas>
                    </div>
                </div>

                <div class="flex justify-end">
                    @if(!$todayAttendance || !$todayAttendance->check_in)
                        <x-primary-button id="check-in-btn" class="ml-3">
                            Check In
                        </x-primary-button>
                    @else
                        <x-primary-button id="check-out-btn" class="ml-3">
                            Check Out
                        </x-primary-button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let webcam = null;
let canvas = null;
let streaming = false;

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#4f46e5'
    });
}

function initWebcam() {
    const workMode = document.querySelector('input[name="work_mode"]:checked')?.value || '{{ $todayAttendance->work_mode ?? "" }}';
    const webcamSection = document.getElementById('webcam-section');
    
    if (workMode === 'wfh') {
        webcamSection.classList.remove('hidden');
        if (!streaming) {
            startWebcam();
        }
    } else {
        webcamSection.classList.add('hidden');
        if (streaming) {
            stopWebcam();
        }
    }
}

function startWebcam() {
    webcam = document.getElementById('webcam');
    canvas = document.getElementById('canvas');
    
    navigator.mediaDevices.getUserMedia({
        video: {
            width: 640,
            height: 480,
            facingMode: 'user'
        },
        audio: false
    })
    .then(function(stream) {
        webcam.srcObject = stream;
        streaming = true;
    })
    .catch(function(err) {
        showError('Unable to access webcam: ' + err.message);
    });
}

function stopWebcam() {
    if (webcam && webcam.srcObject) {
        webcam.srcObject.getTracks().forEach(track => track.stop());
        webcam.srcObject = null;
        streaming = false;
    }
}

function captureImage() {
    if (!streaming) return null;
    
    canvas.width = webcam.videoWidth;
    canvas.height = webcam.videoHeight;
    canvas.getContext('2d').drawImage(webcam, 0, 0);
    return canvas.toDataURL('image/jpeg');
}

// Initialize webcam based on work mode selection
document.querySelectorAll('input[name="work_mode"]').forEach(radio => {
    radio.addEventListener('change', initWebcam);
});

// Check-in handler
document.getElementById('check-in-btn')?.addEventListener('click', function() {
    const workMode = document.querySelector('input[name="work_mode"]:checked').value;
    const data = {
        work_mode: workMode
    };

    if (workMode === 'wfh') {
        const image = captureImage();
        if (!image) {
            showError('Please enable your webcam for WFH check-in.');
            return;
        }
        data.webcam_image = image;
    }

    axios.post('{{ route("attendance.check-in") }}', data)
        .then(response => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Check-in successful!',
                confirmButtonColor: '#4f46e5'
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            showError(error.response?.data?.error || 'An error occurred during check-in.');
        });
});

// Check-out handler
document.getElementById('check-out-btn')?.addEventListener('click', function() {
    const workMode = '{{ $todayAttendance->work_mode ?? "" }}';
    const data = {};

    if (workMode === 'wfh') {
        const image = captureImage();
        if (!image) {
            showError('Please enable your webcam for WFH check-out.');
            return;
        }
        data.webcam_image = image;
    }

    axios.post('{{ route("attendance.check-out") }}', data)
        .then(response => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Check-out successful!',
                confirmButtonColor: '#4f46e5'
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            showError(error.response?.data?.error || 'An error occurred during check-out.');
        });
});

// Initialize webcam if needed
initWebcam();

// Cleanup webcam on page unload
window.addEventListener('unload', stopWebcam);
</script>
@endpush
