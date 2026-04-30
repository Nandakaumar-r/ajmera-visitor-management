<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" disabled/>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" disabled/>
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

        <!-- Face Profile Section -->
        <div class="mt-10 pt-10 border-t border-gray-200">
        <!-- <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Face Profile') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __("Update your face profile for webcam login.") }}
            </p>
        </header> -->

        <div class="mt-6">
            <!-- <div id="webcam-container" class="relative">
                <video id="webcam" autoplay playsinline class="w-full max-w-md rounded-lg shadow-lg"></video>
                <canvas id="canvas" class="hidden"></canvas>
                <div id="loading-indicator" class="hidden absolute inset-0 bg-gray-900/50 flex items-center justify-center rounded-lg">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                </div>
            </div>

            <div class="mt-4 flex gap-4">
                <x-primary-button type="button" id="capture-btn" class="bg-blue-500">
                    {{ __('Capture Photo') }}
                </x-primary-button>
                <x-primary-button type="button" id="retry-btn" class="hidden">
                    {{ __('Retry') }}
                </x-primary-button>
            </div>

            <div id="status-message" class="mt-4"></div> -->
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let video = document.getElementById('webcam');
                let canvas = document.getElementById('canvas');
                let captureBtn = document.getElementById('capture-btn');
                let retryBtn = document.getElementById('retry-btn');
                let statusMessage = document.getElementById('status-message');
                let loadingIndicator = document.getElementById('loading-indicator');
                let stream = null;

                async function startWebcam() {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        video.srcObject = stream;
                    } catch (err) {
                        statusMessage.innerHTML = `<p class="text-red-500">Error accessing webcam: ${err.message}</p>`;
                    }
                }

                function stopWebcam() {
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                    video.srcObject = null;
                }

                function showLoading(show) {
                    loadingIndicator.classList.toggle('hidden', !show);
                }

                function showMessage(message, isError = false) {
                    statusMessage.innerHTML = `<p class="${isError ? 'text-red-500' : 'text-green-500'}">${message}</p>`;
                }

                captureBtn.addEventListener('click', async function() {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    
                    const imageData = canvas.toDataURL('image/jpeg');
                    showLoading(true);
                    
                    try {
                        const response = await fetch('{{ route('profile.face.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ webcam_image: imageData })
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            showMessage(result.message);
                            captureBtn.classList.add('hidden');
                            retryBtn.classList.remove('hidden');
                            stopWebcam();
                        } else {
                            showMessage(result.message, true);
                        }
                    } catch (err) {
                        showMessage('Error updating face profile: ' + err.message, true);
                    } finally {
                        showLoading(false);
                    }
                });

                retryBtn.addEventListener('click', function() {
                    startWebcam();
                    captureBtn.classList.remove('hidden');
                    retryBtn.classList.add('hidden');
                    statusMessage.innerHTML = '';
                });

                startWebcam();
            });
        </script>
    </div>
</section>
