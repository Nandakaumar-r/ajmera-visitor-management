@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Error & Success Messages -->
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg shadow-sm">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm">
        ✅ {{ session('success') }}
    </div>
    @endif

    <!-- Page Heading -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">📝 Resignations</h2>
        <a href="{{ url()->previous() }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            ⬅ Back
        </a>
    </div>

    <!-- Table Wrapper -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full border border-gray-200 rounded-lg">
            <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 text-sm">
                <tr>
                    <th class="px-4 py-3 text-center font-bold text-gray-900">Employee ID</th>
                    <!-- <th class="px-4 py-3 text-left font-semibold text-gray-900">Email</th> -->
                    <th class="px-4 py-3 text-left font-semibold text-gray-900">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-900">Designation</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-900">Department</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-900">DOJ</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-900">LWD</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-900">Manager</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-900">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                @if(empty($resignations) || $resignations->isEmpty())
                <tr>
                    <td colspan="9" class="px-6 py-6 text-center text-gray-500">
                        No Resignations Found 🙅
                    </td>
                </tr>
                @else
                @foreach($resignations as $resignation)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium">{{ $resignation->employee->employee_id }}</td>
                    <!-- <td class="px-4 py-3">{{ $resignation->employee->employee_email }}</td> -->
                    <td class="px-4 py-3">{{ $resignation->employee->employee_name }}</td>
                    <td class="px-4 py-3">{{ $resignation->employee->employee_designation }}</td>
                    <td class="px-4 py-3">{{ $resignation->employee->employee_department }}</td>
                    <td class="px-4 py-3 text-center">{{ $resignation->employee->employee_date_of_joining }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $resignation->manager_last_working_day ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-600' }}">
                            {{ $resignation->manager_last_working_day ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $resignation->employee->manager->manager_name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('asset-collection.show', ['resignation_id' => $resignation->id]) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-md transition">
                            📝 Prepare NOC
                        </a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
