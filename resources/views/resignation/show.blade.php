@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Resignation Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold mb-4">Employee Information</h3>
                <div class="space-y-3">
                    <p><span class="font-medium">Name:</span> {{ $resignation->employee->employee_name }}</p>
                    <p><span class="font-medium">Email:</span> {{ $resignation->employee->employee_email }}</p>
                    <p><span class="font-medium">Department:</span> {{ $resignation->employee->employee_department }}</p>
                    <p><span class="font-medium">Designation:</span> {{ $resignation->employee->employee_designation }}</p>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4">Resignation Information</h3>
                <div class="space-y-3">
                    <p><span class="font-medium">Resignation Date:</span> {{ $resignation->resignation_date }}</p>
                    <p><span class="font-medium">Status:</span> 
                        <span class="px-2 py-1 rounded text-sm 
                            @if($resignation->status == 'Accepted') bg-green-100 text-green-800
                            @elseif($resignation->status == 'Declined') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ $resignation->status ?? 'Pending' }}
                        </span>
                    </p>
                    @if($resignation->manager_last_working_day)
                        <p><span class="font-medium">Last Working Day:</span> {{ $resignation->manager_last_working_day }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-4">Resignation Details</h3>
            <div class="bg-gray-50 p-4 rounded">
                <p><span class="font-medium">Reason:</span></p>
                <p class="mt-2">{{ $resignation->reason }}</p>
                @if($resignation->additional_details)
                    <p class="mt-4"><span class="font-medium">Additional Details:</span></p>
                    <p class="mt-2">{{ $resignation->additional_details }}</p>
                @endif
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('resignations.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection
