@extends('layouts.dashboard')

@section('content')

<div class="py-12">
    <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                <div class="mb-4 p-4 rounded">
                    <!-- Show Error and Success Messages -->
                    @if(session('error'))
                        <div class="p-4 mb-6 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="p-4 mb-6 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Leave Balance Overview</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Current Balance -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-600">Current Balance</h3>
                            <p class="text-2xl font-bold text-blue-600">{{ $leaveBreakdown['year_to_date_balance'] }} days</p>
                            <p class="text-sm text-gray-500">As of {{ $leaveBreakdown['current_month'] }}</p>
                        </div>

                        <!-- Monthly Accrual -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-600">Monthly Accrual</h3>
                            <p class="text-2xl font-bold text-green-600">{{ $leaveBreakdown['monthly_accrual'] }} days</p>
                            <p class="text-sm text-gray-500">Next credit on {{ $leaveBreakdown['next_credit_date'] }}</p>
                            <p class="text-xs text-gray-400">{{ $leaveBreakdown['days_until_next_credit'] }} days until next credit</p>
                        </div>

                        <!-- Year-End Projection -->
                        <div class="bg-purple-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-600">Year-End Projection</h3>
                            <p class="text-2xl font-bold text-purple-600">{{ $leaveBreakdown['year_end_projection'] }} days</p>
                            <p class="text-sm text-gray-500">Estimated by December</p>
                        </div>
                    </div>

                    <!-- Detailed Breakdown -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Detailed Breakdown</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total Leaves Credited</p>
                                    <p class="text-lg font-semibold text-gray-800">{{ $leaveBreakdown['total_credited'] }} days</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Leaves Taken</p>
                                    <p class="text-lg font-semibold text-gray-800">{{ $leaveBreakdown['leaves_taken'] }} days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid gap-6 mb-6">
                        <div>
                            <label for="leave_type" class="block mb-2 text-sm font-medium text-gray-900">
                                Leave type<span class="text-red-500">*</span>
                            </label>
                            <select required="true" id="leave_type" name="leave_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="">Select type</option>
                                <option value="LOP">Loss of Pay</option>
                                <option value="EL">Earned Leave</option>
                                <option value="ML">Marriage Leave</option>
                                <option value="CO">Compensatory Off</option>
                                <option value="CV">Client Visit</option>
                            </select>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="from_date" class="block mb-2 text-sm font-medium text-gray-900">
                                    From date<span class="text-red-500">*</span>
                                </label>
                                <!-- On change of from date, to date will be set to the same -->
                                <input required="true" value="{{ date('Y-m-d') }}" type="date" id="from_date" name="from_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" onchange="updateToDate()">

                                <label for="session_1" class="block mb-2 mt-4 text-sm font-medium text-gray-900">Session</label>
                                <select id="session_1" name="session_1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="full">Full Day</option>
                                    <option value="first">First Half</option>
                                    <option value="second">Second Half</option>
                                </select>
                            </div>

                            <div>
                                <label for="to_date" class="block mb-2 text-sm font-medium text-gray-900">
                                    To date<span class="text-red-500">*</span>
                                </label>
                                <input required="true" value="{{ date('Y-m-d') }}" type="date" id="to_date" name="to_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">

                                <label for="session_2" class="block mb-2 mt-4 text-sm font-medium text-gray-900">Session</label>
                                <select id="session_2" name="session_2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="full">Full Day</option>
                                    <option value="first">First Half</option>
                                    <option value="second">Second Half</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="contact_details" class="block mb-2 text-sm font-medium text-gray-900">Contact details</label>
                            <input required="true" type="text" id="contact_details" name="contact_details" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        </div>

                        <div>
                            <label for="reason" class="block mb-2 text-sm font-medium text-gray-900">Reason</label>
                            <textarea required="true" id="reason" name="reason" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div>
                            <label for="attachment" class="block mb-2 text-sm font-medium text-gray-900">Attach File</label>
                            <input type="file" id="attachment" name="attachment" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                            <p class="mt-1 text-sm text-gray-500">File Types: pdf, xls, xlsx, doc, docx, txt, ppt, pptx, gif, jpg, jpeg, png</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Submit</button>
                        <a href="{{ route('leaves.index') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function updateToDate() {
        // Set minimum to_date value to from_date value
        document.getElementById('to_date').value = document.getElementById('from_date').value;
        document.getElementById('to_date').min = document.getElementById('from_date').value;
    }

    document.getElementById('from_date').addEventListener('input', updateToDate);
</script>

@endsection