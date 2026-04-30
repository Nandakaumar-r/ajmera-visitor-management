@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Pending Employee Resignations</h1>
    @forelse ($resignations as $resignation)
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="font-bold">Employee: {{ $resignation->employee->employee_name }}</h2>
            <p class="mt-2">Reason: {{ $resignation->reason }}</p>
            <p class="mt-2">Additional Details: {{ $resignation->additional_details }}</p>
            <p class="mt-2">Resignation Date: {{ $resignation->resignation_date }}</p>
           <p class="mt-2">Official Notice Period: {{ $resignation->notice_period }}</p>

            <!-- Action Buttons -->
            <div class="flex space-x-4 mt-4">
                <!-- Accept Button - Opens Modal -->
                <button data-modal-target="acceptModal-{{ $resignation->id }}" 
                        data-modal-toggle="acceptModal-{{ $resignation->id }}"
                        class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Accept
                </button>

                <!-- Reject Button - Opens Modal -->
                <button data-modal-target="rejectModal-{{ $resignation->id }}" 
                        data-modal-toggle="rejectModal-{{ $resignation->id }}"
                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Reject
                </button>
            </div>

            <!-- Accept Modal -->
            <div id="acceptModal-{{ $resignation->id }}" tabindex="-1" aria-hidden="true" 
                 class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow">
                        <!-- Modal header -->
                        <div class="flex items-start justify-between p-4 border-b rounded-t">
                            <h3 class="text-xl font-semibold">
                                Accept Resignation
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" 
                                    data-modal-hide="acceptModal-{{ $resignation->id }}">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <form action="{{ route('resignations.accept', $resignation->id) }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="p-6 space-y-6">
                                <div>
                                    <label for="manager_last_working_day" class="block mb-2 text-sm font-medium text-gray-900">
                                        Last Working Day
                                    </label>
                                    <input type="date" name="manager_last_working_day" id="manager_last_working_day-{{ $resignation->id }}"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                           required>
                                    @error('manager_last_working_day')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b">
                                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    Confirm Acceptance
                                </button>
                                <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                        data-modal-hide="acceptModal-{{ $resignation->id }}">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div id="rejectModal-{{ $resignation->id }}" tabindex="-1" aria-hidden="true" 
                 class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow">
                        <!-- Modal header -->
                        <div class="flex items-start justify-between p-4 border-b rounded-t">
                            <h3 class="text-xl font-semibold">
                                Reject Resignation
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                                    data-modal-hide="rejectModal-{{ $resignation->id }}">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <form action="{{ route('resignations.decline', $resignation->id) }}" method="POST">
                            @csrf
                            @method('POST')
                            <div class="p-6 space-y-6">
                                <div>
                                    <label for="rejection_reason" class="block mb-2 text-sm font-medium text-gray-900">
                                        Reason for Rejection
                                    </label>
                                    <textarea name="resignation_reason" id="resignation_reason-{{ $resignation->id }}"
                                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                              rows="4" required></textarea>
                                    @error('rejection_reason')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b">
                                <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    Confirm Rejection
                                </button>
                                <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"
                                        data-modal-hide="rejectModal-{{ $resignation->id }}">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500 text-2xl">No Pending Resignations</p>
    @endforelse
</div>
@endsection