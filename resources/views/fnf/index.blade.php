@extends('layouts.dashboard')

@section('content')

<div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Full & Final Settlement</h2>

            @foreach($resignations as $resignation)
                <!-- Employee Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold mb-3">Employee Details</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-600">Employee Name</p>
                            <p class="font-medium">{{ $resignation->employee->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Employee ID</p>
                            <p class="font-medium">{{ $resignation->employee->employee_id }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Last Working Day</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <p class="text-gray-600">Joining Date</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Resignation Date</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Resignation Reason</p>
                            <p class="font-medium">{{ $resignation->reason }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4 mt-4">
                        <a href="{{ route('fnf.show', $resignation->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"> Calculate </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


@endsection