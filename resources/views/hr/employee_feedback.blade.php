@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-10">

    <h2 class="text-2xl font-bold mb-6">Exit Interview Feedback - {{ $resignation->employee->employee_name }}</h2>
    <div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow-md">
        <div class="text-right mb-4">
            <a href="{{ route('resignations.interview.process', $resignation->id) }}"
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 inline-block">
                Back to Process
            </a>
        </div>

        @php
        $hasResponses = $questions->pluck('responses')->flatten()->isNotEmpty();
        @endphp

        @if(!$hasResponses)
        <div class="alert alert-info shadow-sm">
            <strong>No feedback provided by employee.</strong>
        </div>
        @else
        @foreach($questions as $question)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title font-bold">{{ $question->question }}</h5>

                @if($question->responses->isNotEmpty())
                @foreach($question->responses as $response)
                <p class="mb-1 text-dark">- {{ $response->answer }}</p>
                @endforeach
                @else
                <p class="text-muted mb-0">No response</p>
                @endif
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection