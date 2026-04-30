<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Visitor Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <!-- Today's Visitors -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Visitors</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $todayVisitors }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- This Week's Visitors -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">This Week</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $weekVisitors }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Visitors -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $pendingVisitors }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Visitors -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Visitors</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalVisitors }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 mb-6">
                        <form action="{{ route('visitors.hr-dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Date Filter -->
                            <div>
                                <label for="date_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Range</label>
                                <select name="date_filter" id="date_filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="week" {{ request('date_filter') === 'week' ? 'selected' : '' }}>This Week</option>
                                    <option value="month" {{ request('date_filter') === 'month' ? 'selected' : '' }}>This Month</option>
                                    <option value="all" {{ request('date_filter') === 'all' ? 'selected' : '' }}>All Time</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="Search name or purpose..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-400">
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-end">
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Graph Section -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Visitor Trends - Last 7 Days</h3>
                        <div class="h-64">
                            <canvas id="visitorsChart"></canvas>
                        </div>
                    </div>

                    <!-- Visitors List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($visitors as $visitor)
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1 min-w-0">
                                        <!-- Photo -->
                                        <a target="_blank" href="{{ env('APP_URL') }}storage/{{ $visitor->photo_path }}" class="block mb-3 float-end">
                                            <img src="{{ env('APP_URL') }}storage/{{ $visitor->photo_path }}"
                                                alt="{{ ucfirst($visitor->first_name) }} {{ ucfirst($visitor->last_name) }}"
                                                class="w-16 h-16 rounded-full">
                                        </a>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                            {{ ucfirst($visitor->first_name) }} {{ ucfirst($visitor->last_name) }}
                                        </p>

                                        <!-- Contact Info -->
                                        @if($visitor->phone_number)
                                            <div class="flex items-center mt-1">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $visitor->phone_number }}
                                                </span>
                                            </div>
                                        @endif

                                        <!-- ID Information -->
                                        <div class="flex items-center mt-1">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                            </svg>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $visitor->government_id_type }} (*{{ $visitor->government_id_last_digits }})
                                            </span>
                                        </div>

                                        <!-- Purpose of Visit -->
                                        <div class="flex items-center mt-2">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $visitor->purpose_of_visit }}
                                            </span>
                                        </div>

                                        <!-- Status Badge -->
                                        <!-- <div class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($visitor->status === 'approved') bg-green-100 text-green-800
                                                @elseif($visitor->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($visitor->status) }}
                                            </span>
                                        </div> -->
                                    </div>
                                </div>

                                <!-- Time and Creator Info -->
                                <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <div class="space-y-3">
                                        <!-- Check-in Date -->
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($visitor->check_in_time)->format('M d, Y') }}
                                            </span>
                                        </div>

                                        <!-- Check-in Time -->
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($visitor->check_in_time)->format('h:i A') }}
                                            </span>
                                        </div>

                                        <!-- Created By -->
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                Created by: {{ $visitor->creator->name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('visitorsChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [{
                        label: 'Number of Visitors',
                        data: {!! json_encode($chartData['visitors']) !!},
                        borderColor: 'rgb(59, 130, 246)', // Blue color
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#374151'
                            },
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                            }
                        },
                        x: {
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#374151'
                            },
                            grid: {
                                color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#374151'
                            }
                        }
                    }
                }
            });

            // Auto-submit form when filters change
            document.getElementById('date_filter').addEventListener('change', function() {
                this.form.submit();
            });

            document.getElementById('status').addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
    @endpush
</x-app-layout>
