@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-10">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Schedule Farewell Email</h1>
        <p class="text-gray-600">For {{ $employee->employee_name }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('farewell.store', $resignation) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="subject">
                Email Subject
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('subject') border-red-500 @enderror"
                id="subject"
                type="text"
                name="subject"
                value="{{ old('subject', 'Farewell - ' . $employee->employee_name) }}"
                placeholder="Enter email subject">
            @error('subject')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="message">
                Email Message
            </label>
            <textarea
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-64 @error('message') border-red-500 @enderror"
                id="message"
                name="message"
                placeholder="Enter farewell message">{{ old('message', "Dear All,\n\nAs many of you know, " . $employee->employee_name . " will be leaving us on " . $resignation->last_working_date . ".\n\nPlease join us in wishing them the very best for their future endeavors.\n\nBest regards,\nHR Team") }}</textarea>
            @error('message')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="send_date">
                Send Date
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('send_date') border-red-500 @enderror"
                id="send_date"
                type="date"
                name="send_date"
                value="{{ old('send_date', $resignation->last_working_date) }}">
            @error('send_date')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Schedule Email
            </button>
            <a href="{{ route('resignations.show', $resignation) }}" class="text-blue-500 hover:text-blue-800">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
