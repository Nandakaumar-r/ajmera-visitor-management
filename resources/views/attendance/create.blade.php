<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Monthly Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Available Leaves</div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ 24 - $stats['leave'] }}</div>
                            <div class="text-xs text-gray-400">Out of 24 annual leaves</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Leaves Taken</div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['leave'] }}</div>
                            <div class="text-xs text-gray-400">This month</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Half Days</div>
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['half_day'] }}</div>
                            <div class="text-xs text-gray-400">This month</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Work From Home</div>
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['wfh'] }}</div>
                            <div class="text-xs text-gray-400">This month</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('attendance.store') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
                                <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                    @foreach(range(1, 12) as $month)
                                        <option value="{{ $month }}" {{ date('n') == $month ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                                <select name="year" id="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                    @foreach(range(date('Y'), date('Y')-1) as $year)
                                        <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="shift" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shift</label>
                                <select name="shift" id="shift" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                    <option value="morning">Morning Shift (9:30 AM - 6:30 PM)</option>
                                    <option value="evening">Evening Shift (12:30 PM - 9:30 PM)</option>
                                </select>
                            </div>
                            <div>
                                <label for="work_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Work Type</label>
                                <select name="work_type" id="work_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                    <option value="office">Office</option>
                                    <option value="wfh">Work From Home</option>
                                </select>
                            </div>
                        </div>

                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Date</th>
                                        <th scope="col" class="px-6 py-3">Day</th>
                                        <th scope="col" class="px-6 py-3">Shift</th>
                                        <th scope="col" class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentMonth = date('n');
                                        $currentYear = date('Y');
                                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
                                    @endphp

                                    @for($day = 1; $day <= $daysInMonth; $day++)
                                        @php
                                            $date = \Carbon\Carbon::create($currentYear, $currentMonth, $day);
                                            $isWeekend = $date->isWeekend();
                                        @endphp
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 {{ $isWeekend ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
                                            <td class="px-6 py-4">
                                                {{ $date->format('d-m-Y') }}
                                                <input type="hidden" name="dates[]" value="{{ $date->format('Y-m-d') }}">
                                            </td>
                                            <td class="px-6 py-4">{{ $date->format('l') }}</td>
                                            <td class="px-6 py-4">
                                                <select name="shifts[]" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" {{ $isWeekend ? 'disabled' : '' }}>
                                                    <option value="general" selected>General (9:30 - 18:30)</option>
                                                    <option value="morning">Morning (6:00 - 14:00)</option>
                                                    <option value="evening">Evening (14:00 - 22:00)</option>
                                                    <option value="night">Night (22:00 - 6:00)</option>
                                                </select>
                                                @if($isWeekend)
                                                    <input type="hidden" name="shifts[]" value="general">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <select name="actions[]" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" {{ $isWeekend ? 'disabled' : '' }}>
                                                    <option value="present" {{ $isWeekend ? '' : 'selected' }}>Present</option>
                                                    <option value="leave">Leave</option>
                                                    <option value="half_day">Half Day</option>
                                                    <option value="comp_off">Comp Off</option>
                                                    <option value="wfh">Work From Home</option>
                                                </select>
                                                @if($isWeekend)
                                                    <input type="hidden" name="actions[]" value="weekend">
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>{{ __('Submit Monthly Attendance') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthSelect = document.getElementById('month');
            const yearSelect = document.getElementById('year');
            
            monthSelect.addEventListener('change', updateDates);
            yearSelect.addEventListener('change', updateDates);
            
            function updateDates() {
                // Reload the page with new month/year parameters
                const month = monthSelect.value;
                const year = yearSelect.value;
                window.location.href = `{{ route('attendance.create') }}?month=${month}&year=${year}`;
            }
        });
    </script>
    @endpush
</x-app-layout>
