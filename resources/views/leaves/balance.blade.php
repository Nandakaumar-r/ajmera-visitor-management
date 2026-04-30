@extends('layouts.dashboard')

@section('content')
    <div class="py-6">
        <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Leave Balance</h2>
                    <p class="mt-1 text-sm text-gray-600">View and manage your leave balances for {{ request('year', date('Y')) }}</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <select 
                        class="block w-full sm:w-auto rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6"
                        onchange="window.location.href = this.value"
                    >
                        @for($year = date('Y'); $year >= date('Y')-2; $year--)
                            <option value="?year={{ $year }}" {{ request('year', date('Y')) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                    <a href="{{ route('leaves.create') }}" 
                       class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-sm font-semibold text-white rounded-md shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 5a1 1 0 0 1 1 1v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H6a1 1 0 1 1 0-2h3V6a1 1 0 0 1 1-1Z" />
                        </svg>
                        Apply for Leave
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($leaveBalances as $leave)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $leave['title'] }}</h3>
                                    <p class="text-sm text-gray-500">Granted: {{ $leave['granted'] }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    {{ $leave['code'] }}
                                </span>
                            </div>
                            
                            <div class="flex flex-col items-center justify-center space-y-2 my-6">
                                <span class="text-5xl font-bold text-gray-900">{{ $leave['balance'] }}</span>
                                <span class="text-sm font-medium text-gray-500">Available Balance</span>
                            </div>

                            <div class="mt-6">
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span>{{ $leave['consumed'] }} Used</span>
                                    <span>{{ $leave['granted'] }} Total</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $leave['percentage'] }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection