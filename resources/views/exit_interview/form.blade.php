@extends('layouts.dashboard')

@section('content')
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
<div class="container mx-auto py-10">

    <div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-xl font-bold mb-4">Exit Interview Form</h1>
        <p class="mb-4 text-sm text-gray-600">It would be appreciated if you complete this exit interview questionnaire. Your responses will be kept confidential, so please feel free to describe your experience.</p>
        <p class="mb-4 text-sm text-gray-600">In collecting this information, we are looking to identify trends that may assist in the development of future HR Policies & Procedures.</p>

        <form method="POST" action="{{ route('exit_interview.submit') }}" enctype="multipart/form-data">
            @csrf

            @foreach ($questions as $question)
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">{{ $question->question }}</label>

                @if ($question->field_type == 'text')
                <input type="text" name="answers[{{ $question->id }}]" class="w-full border border-gray-300 rounded-lg p-2" required>
                @elseif ($question->field_type == 'file')
                <input type="file" name="answers[{{ $question->id }}]" class="w-full border border-gray-300 rounded-lg p-2" accept=".pdf,.png,.jpg,.jpeg">
                @elseif ($question->field_type == 'radio' && !empty($question->options) && is_array(json_decode($question->options)))
                <div class="space-y-2">
                    @foreach (json_decode($question->options) as $option)
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" class="text-blue-600 focus:ring-blue-500">
                        <span>{{ $option }}</span>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach

            <!-- Pagination controls -->
            <div class="flex justify-between items-center mt-6">
                @if ($questions->onFirstPage())
                <span></span>
                @else
                <a href="{{ $questions->previousPageUrl() }}" class="text-blue-500 hover:underline">Previous</a>
                @endif

                @if ($questions->hasMorePages())
                <a href="{{ $questions->nextPageUrl() }}" class="text-blue-500 hover:underline">Next</a>
                @else
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Submit</button>
                @endif
            </div>
        </form>

        <!-- Display pagination links at bottom using Flowbite style -->
        <div class="mt-4">
            {{ $questions->links('vendor.pagination.tailwind') }}
        </div>
    </div>

</div>

@endsection