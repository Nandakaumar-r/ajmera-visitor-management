@extends('layouts.dashboard')

@section('content')
<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Employee Resignation Form</h1>
    </div>

    @if (session('success'))
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
        <div class="flex items-center">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if ($resignation && !in_array($resignation->status, ['cancelled', 'transferred', 'Declined']))
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
        <div class="p-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Notice Period Status</h3>
                    @if ($resignation->status == 'Accepted' && $resignation->manager_last_working_day)
                    <p class="text-gray-700 dark:text-gray-300">
                        You are currently serving your notice period until
                        <span class="font-semibold">
                            {{ \Carbon\Carbon::parse($resignation->manager_last_working_day)->format('d F, Y') }}
                        </span>.
                    </p>
                    @elseif ($resignation->status == 'completed' && $resignation->exitProcess && $resignation->exitProcess->last_working_day)
                    <p class="text-gray-700 dark:text-gray-300">
                        Your final last working day as approved by HR is
                        <span class="font-semibold">
                            {{ \Carbon\Carbon::parse($resignation->exitProcess->last_working_day)->format('d F, Y') }}
                        </span>.
                    </p>
                    @else
                    <p class="text-gray-700 dark:text-gray-300">
                        You have applied for resignation on
                        <span class="font-semibold">{{ $resignation->created_at->format('d F, Y') }}</span> & it's currently under review.
                    </p>
                    @endif

                </div>
            </div>
        </div>
    </div>
    @else
    <form action="{{ route('resignations.store') }}" method="POST" class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Employee ID Field -->
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Employee ID <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                        id="employee_id"
                        name="employee_id"
                        value="{{ $employee->employee_id }}"
                        readonly
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('employee_id') border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                @error('employee_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Last Working Day Field -->
            <div>
                <label for="resignation_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Preferred Last Working Day <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="date"
                        id="resignation_date"
                        name="resignation_date"
                        min="{{ date('Y-m-d') }}"
                        value="{{ old('resignation_date') }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('resignation_date') border-red-500 @enderror">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Please note that the final LWD will be subject to management approval</p>
                @error('resignation_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reason Field -->
            <div class="lg:col-span-2">
                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Reason for Resignation <span class="text-red-500">*</span>
                </label>
                <select id="reason"
                    name="reason"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('reason') border-red-500 @enderror">
                    <option value="">Select a reason</option>
                    <optgroup label="Career Related">
                        <option value="Career Growth">Career Growth</option>
                        <option value="New Opportunity">New Opportunity</option>
                        <option value="Lack of Advancement Opportunities">Lack of Advancement Opportunities</option>
                        <option value="Change in Career Path">Change in Career Path</option>
                        <option value="Freelance or Self-Employment">Freelance or Self-Employment</option>
                    </optgroup>
                    <optgroup label="Personal Reasons">
                        <option value="Personal Reasons">Personal Reasons</option>
                        <option value="Relocation">Relocation</option>
                        <option value="Better Work-Life Balance">Better Work-Life Balance</option>
                        <option value="Higher Education">Higher Education</option>
                        <option value="Health Reasons">Health Reasons</option>
                    </optgroup>
                    <optgroup label="Work Related">
                        <option value="Company Restructuring">Company Restructuring</option>
                        <option value="Contract Ending">Contract Ending</option>
                        <option value="Job Misalignment">Job Misalignment</option>
                        <option value="Workplace Culture">Workplace Culture</option>
                        <option value="Dissatisfaction with Role">Dissatisfaction with Role</option>
                        <option value="Financial Reasons">Financial Reasons</option>
                    </optgroup>
                </select>
                @error('reason')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Details Field -->
            <div class="lg:col-span-2">
                <label for="additional_details" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Additional Details
                </label>
                <div class="mt-1">
                    <textarea id="additional_details"
                        name="additional_details"
                        rows="4"
                        placeholder="Please provide any additional information that would be helpful..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('additional_details') border-red-500 @enderror">{{ old('additional_details') }}</textarea>
                </div>
                <p class="mt-2 text-sm text-gray-500">Brief explanation of your resignation reason (optional)</p>
                @error('additional_details')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="lg:col-span-2 pt-4">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Submit Resignation
                </button>
                <p class="mt-3 text-center text-sm text-gray-500">
                    This action cannot be undone. Please make sure all details are correct.
                </p>
            </div>
        </div>
    </form>
    @endif
</div>
@endsection