@extends('layouts.dashboard')

@section('content')
    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-bold mb-6">Create Employee</h1>

        <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="employee_name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="employee_name" id="employee_name" class="block w-full px-4 py-2 mt-1 border rounded-md" value="{{ old('employee_name') }}" required>
                </div>

                <div>
                    <label for="employee_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="employee_email" id="employee_email" class="block w-full px-4 py-2 mt-1 border rounded-md" value="{{ old('employee_email') }}" required>
                </div>

                <div>
                    <label for="employee_designation" class="block text-sm font-medium text-gray-700">Designation</label>
                    <select name="employee_designation" id="employee_designation" class="block w-full px-4 py-2 mt-1 border rounded-md" required>
                        <option value="">Select Option</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->id }}">{{ $designation->designation_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="employee_department" class="block text-sm font-medium text-gray-700">Department</label>
                    <select name="employee_department" id="employee_department" class="block w-full px-4 py-2 mt-1 border rounded-md" required>
                        <option value="">Select Option</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="employee_date_of_joining" class="block text-sm font-medium text-gray-700">Date of Joining</label>
                    <input type="date" name="employee_date_of_joining" id="employee_date_of_joining" class="block w-full px-4 py-2 mt-1 border rounded-md" value="{{ old('employee_date_of_joining') }}" required>
                </div>

                <div>
                    <label for="manager_id" class="block text-sm font-medium text-gray-700">Manager</label>
                    <select name="manager_id" id="manager_id" class="block w-full px-4 py-2 mt-1 border rounded-md">
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->manager_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                    Create
                </button>
            </div>
        </form>
    </div>
@endsection
