@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto py-10">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold">Preview Relieving Letter</h1>
            <p class="text-gray-600">For {{ $letter->resignation->employee->employee_name }}</p>
        </div>
        <div class="space-x-4">
            <a href="{{ route('relieving_letter.download', $letter) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('resignations.show', $letter->resignation) }}" class="text-gray-600 hover:text-gray-800">
                Back to Resignation
            </a>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold">FIDELIS GROUP</h2>
        </div>

        <div class="text-right mb-8">
            <p>Date: {{ $letter->letter_date->format('F d, Y') }}</p>
        </div>

        <div class="mb-12 whitespace-pre-line">
            {!! nl2br(e($letter->content)) !!}
        </div>

        <div class="mt-12">
            <p>For Fidelis Group,</p>
            <div class="mt-8">
                <p>_______________________</p>
                <p>{{ $letter->generatedBy->name }}</p>
                <p>HR Manager</p>
            </div>
        </div>

        <div class="mt-16 text-center text-sm text-gray-500">
            <p>This is a computer-generated document. No signature is required.</p>
        </div>
    </div>
</div>
@endsection
