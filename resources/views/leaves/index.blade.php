@extends('layouts.dashboard')

@section('content')

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Leave Applications</h2>
                        <a href="{{ route('leaves.create') }}" class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Apply for Leave</a>
                    </div>

                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Leave Type</th>
                                    <th scope="col" class="px-6 py-3">From</th>
                                    <th scope="col" class="px-6 py-3">To</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaves as $leave)
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">{{ ucfirst($leave->leave_type) }}</td>
                                        <td class="px-6 py-4">{{ $leave->from_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4">{{ $leave->to_date->format('d M Y') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($leave->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('leaves.show', $leave->id) }}" class="text-blue-600 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="5" class="px-6 py-4 text-center">No leave applications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection