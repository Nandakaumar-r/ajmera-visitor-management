@extends('layouts.dashboard')

@section('content')
    <div class="container mx-auto py-10">
        <div class="flex justify-start gap-4 items-center">
            <h1 class="text-3xl font-bold">Edit Employee</h1>
            <a href="{{ route('employees.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Employees List
            </a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded mt-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('employees.update', $employee) }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="employee_id" class="block text-lg font-medium text-gray-900">Employee Code</label>
                <input type="text" name="employee_id" id="employee_id" 
                       class="w-full p-3 border border-gray-300 rounded-lg" 
                       value="{{ old('employee_id', $employee->employee_id) }}" required>
            </div>

            <div class="mb-4">
                <label for="employee_name" class="block text-lg font-medium text-gray-900">Employee Name</label>
                <input type="text" name="employee_name" id="employee_name" 
                       class="w-full p-3 border border-gray-300 rounded-lg" 
                       value="{{ old('employee_name', $employee->employee_name) }}" required>
            </div>

            <div class="mb-4">
                <label for="employee_email" class="block text-lg font-medium text-gray-900">Email</label>
                <input type="email" name="employee_email" id="employee_email" 
                       class="w-full p-3 border border-gray-300 rounded-lg" 
                       value="{{ old('employee_email', $employee->employee_email) }}" required>
            </div>

            <div class="mb-4">
                <label for="employee_designation" class="block text-lg font-medium text-gray-900">Designation</label>
                <select name="employee_designation" id="employee_designation" class="w-full p-3 border border-gray-300 rounded-lg">
                    <option value="">Select Designation</option>
                    @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" 
                                {{ $employee->employee_designation == $designation->id ? 'selected' : '' }}>
                            {{ $designation->designation_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="employee_department" class="block text-lg font-medium text-gray-900">Department</label>
                <select name="employee_department" id="employee_department" class="w-full p-3 border border-gray-300 rounded-lg">
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" 
                                {{ $employee->employee_department == $department->id ? 'selected' : '' }}>
                            {{ $department->department_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="employee_date_of_joining" class="block text-lg font-medium text-gray-900">Date of Joining</label>
                <input type="date" name="employee_date_of_joining" id="employee_date_of_joining" 
                       class="w-full p-3 border border-gray-300 rounded-lg" 
                       value="{{ old('employee_date_of_joining', $employee->employee_date_of_joining) }}" required>
            </div>

            <div class="mb-4">
                <label for="manager_id" class="block text-lg font-medium text-gray-900">Manager</label>
                <select name="manager_id" id="manager_id" class="w-full p-3 border border-gray-300 rounded-lg">
                    <option value="">Select Manager</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" 
                                {{ $employee->manager_id == $manager->manager_id ? 'selected' : '' }}>
                            {{ $manager->manager_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Update Employee
            </button>
        </form>
    </div>
@endsection
