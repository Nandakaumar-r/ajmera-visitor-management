<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />

    <!-- App CSS/JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 1s ease-out;
        }
    </style>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
                    console.log('Service Worker registered with scope:', registration.scope);
                }).catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
            });
        }
    </script>


</head>

<body class="font-sans text-gray-900 antialiased">
    <section class="min-h-screen bg-white">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-12">
            <!-- Left side with slider -->
            <!-- Inside the left section -->
            <section class="relative lg:col-span-8 lg:h-full xl:col-span-8 bg-gray-900 overflow-hidden">

                <!-- Desktop Section -->
                <div class="relative hidden md:block h-screen overflow-hidden">
                    <div id="desktopSlider" class="h-full">
                        <!-- Video Slide -->
                        <!-- <div class="relative" style="height: 100vh;">
                            <video class="w-full h-full object-cover" autoplay muted loop playsinline>
                                <source src="{{ asset('video-desk.mp4') }}" type="video/mp4">
                            </video>
                        </div> -->
                        <!-- Banner Slide -->
                        <div class="relative h-full">
                            <img src="{{ asset('Ajmera.jpg') }}" style="height: 100vh;" class="w-full object-cover" alt="Desktop Banner 1">
                        </div>
                    </div>
                </div>

                <!-- Mobile Section -->
                <div class="relative block md:hidden h-auto overflow-hidden">
                    <div id="mobileSlider" class="w-full">
                        <!-- Video Slide -->
                        <div class="relative w-full" style="height: 100%">
                            <video class="w-full object-cover" style="height: 100%" autoplay muted loop playsinline>
                                <source src="{{ asset('video-mob.mp4') }}" type="video/mp4">
                            </video>
                        </div>
                        <!-- Banner Slide -->
                        <div class="relative w-full" style="height: 100%">
                            <img src="{{ asset('img-mob-1.jpeg') }}" style="height: 100%"  class="w-full object-cover" alt="Mobile Banner 1">
                        </div>
                    </div>
                </div>

            </section>


            <!-- Right side with form -->
            <main class="flex items-center justify-center px-6 py-8 sm:px-12 lg:col-span-4 lg:px-12 lg:py-12 xl:col-span-4">
                <div class="w-full max-w-xl lg:max-w-3xl">
                    <!-- Mobile logo and title -->
                    <div class="relative -mt-16 block lg:hidden">
                        <a class="inline-flex md:h-16 md:w-16 items-center justify-center rounded-full bg-white text-blue-600 p-2 w-24" href="/">
                            <span class="sr-only">Home</span>
                            <img src="{{ asset('ajmera_logo.png') }}" alt="Fidelis Logo" class="h-10 md:h-16">
                        </a>
                        <h1 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl md:text-4xl">Welcome Back</h1>
                        <p class="mt-2 text-sm text-gray-500 sm:text-base">Please sign in to continue</p>
                    </div>

                    <div class="relative -mt-16 hidden lg:block">
                        <a class="h-16 w-16 items-center justify-center flex w-full rounded-full bg-white text-blue-600 sm:h-20 sm:w-30" href="/">
                            <span class="sr-only">Home</span>
                            <img src="{{ asset('ajmera_logo.png') }}" alt="Fidelis Logo" class="h-10 w-10 sm:h-20 sm:w-20">
                        </a>
                    </div>

                    <!-- Form content -->
                    <div class="mt-8 lg:mt-0">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </section>
    <!-- PWA Install Prompt Popup -->
    <div id="installPrompt" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full text-center">
            <h2 class="text-lg font-semibold mb-4">Install this app?</h2>
            <div class="flex justify-center space-x-4">
                <button id="installBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Install</button>
                <button id="cancelBtn" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    @push('scripts')
    <style>
        /* Make video cover the entire section */
        video.fullscreen-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }
        .slick-dotted.slick-slider{
            margin-bottom: 0;
        }
    </style>

    <script>
        $(document).ready(function() {
            function initSlider(selector) {
                $(selector).slick({
                    autoplay: true,
                    autoplaySpeed: 5000, // 4s per slide
                    arrows: false,
                    dots: false,
                    infinite: true,
                    pauseOnHover: false,
                    fade: true,
                });
            }

            initSlider("#desktopSlider");
            initSlider("#mobileSlider");
        });
    </script>


    <script>
        let deferredPrompt;

        // Check if user already installed or dismissed
        const installStatus = localStorage.getItem('pwaInstallStatus');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent default prompt
            e.preventDefault();
            deferredPrompt = e;

            // Only show if not already installed/dismissed
            if (installStatus !== 'installed' && installStatus !== 'dismissed') {
                document.getElementById('installPrompt').classList.remove('hidden');
            }
        });

        document.getElementById('installBtn').addEventListener('click', () => {
            document.getElementById('installPrompt').classList.add('hidden');

            if (deferredPrompt) {
                deferredPrompt.prompt();

                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                        localStorage.setItem('pwaInstallStatus', 'installed');
                    } else {
                        console.log('User dismissed the install prompt');
                        localStorage.setItem('pwaInstallStatus', 'dismissed');
                    }
                    deferredPrompt = null;
                });
            }
        });

        document.getElementById('cancelBtn').addEventListener('click', () => {
            document.getElementById('installPrompt').classList.add('hidden');
            localStorage.setItem('pwaInstallStatus', 'dismissed');
        });
    </script>

    @endpush

    @stack('scripts')
</body>

</html>