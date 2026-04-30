@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold">ID Card Submission</h1>

    @if(session('error'))
        <div class="bg-red-500 text-white p-2 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-500 text-white p-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('idcard.store') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf

        <div class="mb-4">
            <label for="employee_id" class="block text-gray-700">Employee ID:</label>
            <select name="employee_id" id="employee_id" class="mt-1 block w-full border rounded" required>
                <option value="">Select Employee</option>
                @foreach($resignation as $employee)
                    <option value="{{ $employee->employee_id }}">{{ $employee->employee->employee_name }} | {{ $employee->employee_id }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="id_card_file" class="block text-gray-700">Upload ID Card:</label>
            <input type="file" name="id_card_file" id="id_card_file" class="mt-1 block w-full border rounded">
        </div>

        <!-- Webcam capture section -->
        <div class="mb-4">
            <label for="webcam_capture" class="block text-gray-700">Capture Image (Optional):</label>
            <div class="flex justify-center">
                <video id="webcam" width="320" height="240" autoplay></video>
                <button type="button" id="start-camera" class="bg-blue-500 text-white py-2 px-4 rounded ml-4">Start Camera</button>
                <button type="button" id="capture" class="bg-green-500 text-white py-2 px-4 rounded ml-4" disabled>Capture</button>
            </div>
            <canvas id="canvas" style="height: 500px; width: 460px; display:none;"></canvas>
            <input type="hidden" name="captured_image" id="captured_image">
        </div>

        <div class="mb-4">
            <label for="remarks" class="block text-gray-700">Remarks:</label>
            <textarea name="remarks" id="remarks" class="mt-1 block w-full border rounded"></textarea>
        </div>

        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Submit</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const webcam = document.getElementById("webcam");
        const startCameraButton = document.getElementById("start-camera");
        const captureButton = document.getElementById("capture");
        const canvas = document.getElementById("canvas");
        const capturedImageInput = document.getElementById("captured_image");

        let cameraStream = null;

        // Start camera on button click
        startCameraButton.addEventListener("click", function() {
            if (cameraStream) {
                // If the camera is already started, stop it
                cameraStream.getTracks().forEach(track => track.stop());
                webcam.srcObject = null;
                captureButton.disabled = true;
                startCameraButton.textContent = "Start Camera";
                cameraStream = null;
            } else {
                // Start the camera
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        webcam.srcObject = stream;
                        cameraStream = stream;
                        captureButton.disabled = false;
                        startCameraButton.textContent = "Stop Camera";
                    })
                    .catch(function(error) {
                        console.error("Error accessing webcam: ", error);
                        alert("Unable to access webcam.");
                    });
            }
        });

        // Capture image from webcam when button is clicked
        captureButton.addEventListener("click", function() {
            const context = canvas.getContext("2d");
            context.drawImage(webcam, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL("image/png"); // Capture image as base64
            capturedImageInput.value = imageData; // Set base64 image in hidden input
        });
    });
</script>

@endsection
