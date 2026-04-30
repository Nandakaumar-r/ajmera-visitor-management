<footer class="bg-white border-t border-gray-200 dark:border-gray-700 sm:flex sm:items-center sm:justify-between p-4 sm:p-6 xl:p-8 dark:bg-gray-800 antialiased shadow-lg">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full">
        <div class="flex items-center space-x-3 mb-2 sm:mb-0">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} <a href="https://fidelisgroup.in/" class="hover:underline" target="_blank">{{ config('app.name') }}</a>
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
            <span class="text-sm text-gray-500 dark:text-gray-400">Version {{ config('app.version', '1.0.0') }}</span>
        </div>

        <div class="flex items-center space-x-4">
            @if(isset($ip))
                <span class="text-sm text-gray-500 dark:text-gray-400">IP: {{ $ip }}</span>
            @endif
            @if(isset($weather))
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $weather }}</span>
            @endif
            <span class="text-sm text-gray-500 dark:text-gray-400">All rights reserved.</span>
        </div>
    </div>
</footer>