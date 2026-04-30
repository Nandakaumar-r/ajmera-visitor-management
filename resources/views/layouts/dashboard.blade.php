<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="font-sans antialiased h-full">
    <div id="loading" class="fixed inset-0 bg-white flex items-center justify-center z-50">
        <img src="{{ asset('loading.gif') }}" alt="Loading..." />
    </div>
    <div class="min-h-full" style="display: none;" id="main-content">
        @include('layouts.navigation')

        <div class="lg:pl-72">
            <main class="py-10 pt-20">
                <div class="px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        window.onload = function() {
            document.getElementById("loading").style.display = "none";
            document.getElementById("main-content").style.display = "block";
        };
    </script>
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {

            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }

                // if NOT set via local storage previously
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }

        });
    </script>
    <script>
        let serverTime="{{ now() }}",currentTime=new Date(serverTime);
        function updateTime() {
            currentTime.setSeconds(currentTime.getSeconds() + 1);
            let e = String(currentTime.getHours()).padStart(2, "0"),
                t = String(currentTime.getMinutes()).padStart(2, "0"),
                r = String(currentTime.getSeconds()).padStart(2, "0");
            let timeElement = document.getElementById("current-time");            
            if (timeElement) {
                timeElement.innerHTML = `${e}:${t}:${r}`;
            }
        }

        setInterval(updateTime, 1e3);
        updateTime();
    </script>

    <script>
        // Handle help modal submission
        document.getElementById('submitHelp').addEventListener('click', function () {
            const issueDescription = document.getElementById('issueDescription').value;

            if (!issueDescription.trim()) {
                alert('Please describe the issue.');
                return;
            }

            // Make AJAX request to submit the issue
            const data = {
                issue: issueDescription
            };

            fetch('/submit-help', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your feedback. We will look into it.');
                    // Close the modal
                    const modal = document.getElementById('issue-modal');
                    modal.classList.add('hidden');
                    // Clear the textarea
                    document.getElementById('issueDescription').value = '';
                } else {
                    alert(data.message || 'There was an error submitting your request.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting your request.');
            });
        });

        // Handle modal close button
        document.getElementById('closeHelpModal').addEventListener('click', function() {
            document.getElementById('issue-modal').classList.add('hidden');
        });

        // Handle modal cancel button
        document.getElementById('cancelHelpModal').addEventListener('click', function() {
            document.getElementById('issue-modal').classList.add('hidden');
            document.getElementById('issueDescription').value = '';
        });

        // Close modal when clicking outside
        document.getElementById('issue-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>

    @livewireScripts
    @yield('scripts')
</body>

</html>