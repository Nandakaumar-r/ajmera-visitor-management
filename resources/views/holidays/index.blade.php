@extends('layouts.dashboard')

@section('content')

<div class="container mx-auto py-10">

    <div class="p-6 bg-white rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Holiday Calendar {{ $year }}</h1>
            @role('Admin|HR')
            <a href="{{ route('holidays.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Add Holiday
            </a>
            @endrole
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div id="alert-success" class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
        @endif

        <!-- Calendar Grid -->
        <div class="bg-white rounded-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 py-6">
                @foreach(['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                <div class="md:col-span-3">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-lg text-gray-900 mb-3">{{ $month }}</h3>
                        @php
                        $monthHolidays = $holidays->filter(function($holiday) use ($index) {
                        return $holiday->date->format('n') === strval($index + 1);
                        });
                        @endphp

                        @if($monthHolidays->count() > 0)
                        @foreach($monthHolidays as $holiday)
                        <div class="mb-2 p-2 rounded-md {{ 
                                        $holiday->type === 'public' ? 'bg-blue-100' :
                                        ($holiday->type === 'optional' ? 'bg-green-100' : 'bg-yellow-100') 
                                    }}">
                            <div class="text-sm font-medium">{{ $holiday->title }}</div>
                            <div class="text-xs text-gray-600">
                                {{ $holiday->date->format('d M, Y') }}
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p class="text-sm text-gray-500">No holidays</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection