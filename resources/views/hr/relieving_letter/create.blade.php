@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-10">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Generate Relieving Letter</h1>
        <p class="text-gray-600">For {{ $employee->employee_name }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('relieving_letter.store', $resignation) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="letter_date">
                Letter Date
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('letter_date') border-red-500 @enderror"
                id="letter_date"
                type="date"
                name="letter_date"
                value="{{ old('letter_date', now()->format('Y-m-d')) }}">
            @error('letter_date')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="content">
                Letter Content
            </label>
            <textarea
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-64 @error('content') border-red-500 @enderror"
                id="content"
                name="content"
                placeholder="Enter letter content">{{ old('content', "TO WHOMSOEVER IT MAY CONCERN

This is to certify that {$employee->employee_name} (Employee ID: {$employee->employee_id}) was employed with Fidelis Group from {$employee->employee_date_of_joining} to {$resignation->last_working_date} as {$employee->employee_designation} in the {$employee->employee_department} department.

During their tenure with us, {$employee->employee_name} demonstrated professionalism and commitment to their responsibilities. Their performance and conduct were satisfactory.

We wish them the very best in their future endeavors.

For Fidelis Group,

[HR Manager Name]
HR Manager") }}</textarea>
            @error('content')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Generate Letter
            </button>
            <a href="{{ route('resignations.show', $resignation) }}" class="text-blue-500 hover:text-blue-800">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
