@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Resignation Risk Analysis</h1>
        <form method="POST" action="{{ route('resignations.predictions.analyze-all') }}" class="inline">
            @csrf
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Analyze All Employees
            </button>
        </form>
    </div>

    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Employee ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Department
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Risk Level
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Last Analysis
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($employees as $employee)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $employee->employee_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $employee->employee_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $employee->employee_department }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($employee->resignation && $employee->resignation->risk_level)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $employee->resignation->risk_level === 'High' ? 'bg-red-100 text-red-800' : 
                                   ($employee->resignation->risk_level === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-green-100 text-green-800') }}">
                                {{ $employee->resignation->risk_level }}
                            </span>
                        @else
                            <span class="text-gray-400">Not analyzed</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $employee->resignation && $employee->resignation->last_prediction_at ? 
                           $employee->resignation->last_prediction_at->diffForHumans() : 'Never' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <form method="POST" action="{{ route('resignations.predictions.analyze', $employee->employee_id) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 bg-transparent border-0 p-0 cursor-pointer">
                                Analyze
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
