@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Risk Analysis for {{ $employee->employee_name }}</h1>
            <p class="text-gray-600">Employee ID: {{ $employee->employee_id }} | Department: {{ $employee->employee_department }}</p>
        </div>
        <form method="POST" action="{{ route('resignations.predictions.analyze', $employee->employee_id) }}" class="inline">
            @csrf
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ $employee->resignation ? 'Update Analysis' : 'Analyze Risk' }}
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($employee->resignation)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Risk Level Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Risk Level</h2>
                <div class="flex items-center justify-center">
                    <div class="text-4xl font-bold {{ 
                        $employee->resignation->risk_level === 'High' ? 'text-red-600' : 
                        ($employee->resignation->risk_level === 'Medium' ? 'text-yellow-600' : 'text-green-600') 
                    }}">
                        {{ $employee->resignation->risk_level }}
                    </div>
                </div>
                <div class="text-sm text-gray-500 text-center mt-2">
                    Last updated: {{ $employee->resignation->last_prediction_at->diffForHumans() }}
                </div>
            </div>

            <!-- Risk Factors Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Risk Factors</h2>
                <ul class="list-disc list-inside space-y-2">
                    @foreach(json_decode($employee->resignation->risk_factors, true) as $factor)
                        <li class="text-gray-700">{{ $factor }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Recommendations Card -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold mb-4">Recommended Actions</h2>
                <ul class="list-disc list-inside space-y-2">
                    @foreach(json_decode($employee->resignation->recommendations, true) as $recommendation)
                        <li class="text-gray-700">{{ $recommendation }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <div class="bg-gray-100 rounded-lg shadow-lg p-6 text-center">
            <p class="text-gray-700">No risk analysis has been performed yet. Click "Analyze Risk" to analyze this employee's resignation risk.</p>
        </div>
    @endif

    <!-- Historical Data -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
        
        <!-- Attendance Patterns -->
        <div class="mb-6">
            <h3 class="text-md font-medium mb-2">Attendance Patterns (Last 30 Days)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Work Hours</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employee->attendances()->latest()->take(30)->get() as $attendance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $attendance->date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                           ($attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $attendance->actual_work_hours ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Leave History -->
        <div>
            <h3 class="text-md font-medium mb-2">Leave History (Last 30 Days)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employee->leaves()->latest()->take(30)->get() as $leave)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($leave->type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $leave->start_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $leave->end_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('resignations.predictions.index') }}" class="text-indigo-600 hover:text-indigo-900">
            &larr; Back to All Employees
        </a>
    </div>
</div>
@endsection
