@extends('layouts.app')

@section('content')
<div class="max-w-2 mx-8 p-8 bg-white rounded-xl shadow-md mt-16">
    <h2 class="mb-4 text-2xl font-bold text-center">External Reimbursement Details</h2>
    <!-- @if(is_null($reimbursement->hr_status))
    <a href="{{ url('/external-reimbursements/'.$reimbursement->id.'/approve-hr') }}" class="btn btn-success">HR Approve</a>
    @elseif(is_null($reimbursement->finance_status))
    <a href="{{ url('/external-reimbursements/'.$reimbursement->id.'/approve-finance') }}" class="btn btn-success">Finance Approve</a>
    @elseif(is_null($reimbursement->cfo_status))
    <a href="{{ url('/external-reimbursements/'.$reimbursement->id.'/approve-cfo') }}" class="btn btn-success">CFO Approve</a>
    @elseif(is_null($reimbursement->final_status))
    <a href="{{ url('/external-reimbursements/'.$reimbursement->id.'/mark-processed') }}" class="btn btn-primary">Mark as Processed</a>
    @endif -->
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Name</th>
                <td>{{ $reimbursement->name }}</td>
                <th>Manager Name</th>
                <td>{{ $reimbursement->manager_name }}</td>
            </tr>
            <tr>
                <th>Employee ID</th>
                <td>{{ $reimbursement->emp_id }}</td>
                <th>Department</th>
                <td>{{ $reimbursement->department }}</td>
            </tr>
            <tr>
                <th>Designation</th>
                <td>{{ $reimbursement->designation }}</td>
                <th>Business Purpose</th>
                <td>{{ $reimbursement->business_purpose }}</td>
            </tr>
            <tr>
                <th>From</th>
                <td>{{ $reimbursement->from->format('M d, Y') }}</td>
                <th>To</th>
                <td>{{ $reimbursement->to->format('M d, Y') }}</td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td>{{ $reimbursement->amount }}</td>
                <th>Status</th>
                <td>
                    @php
                    $statusColors = [
                    'pending' => 'secondary',
                    'finance_approved' => 'primary',
                    'cfo_approved' => 'success',
                    'rejected' => 'danger',
                    'processed' => 'info',
                    ];
                    $color = $statusColors[$reimbursement->status] ?? 'dark';
                    @endphp

                    <span class="badge bg-{{ $color }} text-uppercase">
                        {{ str_replace('_', ' ', $reimbursement->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Submitted by</th>
                <td>{{ $reimbursement->submitted_by }}</td>
                <th>Approved by</th>
                <td>{{ $reimbursement->approved_by }}</td>
            </tr>
            @if ($reimbursement->manager_approval_attachment)
            <tr>
                <th>Manager Approval Attachment</th>
                <td colspan="3">
                    <a href="{{ route('reimbursement.download.attachments', $reimbursement->id) }}" class="btn btn-sm btn-warning">
                        Download All Attachments
                    </a>
                    <a href="{{ asset('/' . $reimbursement->manager_approval_attachment) }}" class="btn btn-sm btn-info" target="_blank">View Attachment</a>
                    {{-- Bills Attachments --}}
                    @php
                    $bills = is_array($reimbursement->bills_attachment)
                    ? $reimbursement->bills_attachment
                    : json_decode($reimbursement->bills_attachment, true);
                    @endphp

                    @if (!empty($bills))
                    @foreach ($bills as $index => $bill)

                    <a href="{{ asset('/' . $bill) }}" class="btn btn-sm btn-info" target="_blank">View Bill {{ $index + 1 }}</a>
                    <!-- <a href="{{ asset('/' . $bill) }}" class="btn btn-sm btn-success" download>Download Bill {{ $index + 1 }}</a> -->

                    @endforeach
                    @else
                    <!-- <p class="text-muted font-bold">No bills uploaded.</p> -->
                    @endif
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    @php
    $details = $reimbursement->reimbursement_details;
    @endphp

    {{-- Other Expenses --}}
    @if (!empty($details['otherExpenses']))
    <h4 class="text-lg font-semibold mt-6 mb-2">Mobile, Internet, Laptop and Others Reimbursement</h4>
    <table class="min-w-full border divide-y divide-gray-200 mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-left">Description</th>
                <th class="px-4 py-2 text-left">Bills</th>
                <th class="px-4 py-2 text-left">Cost Center</th>
                <th class="px-4 py-2 text-left">Amount (₹)</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($details['otherExpenses'] as $item)
            <tr>
                <td class="px-4 py-2">{{ $item['date'] }}</td>
                <td class="px-4 py-2">{{ $item['description'] }}</td>
                <td class="px-4 py-2">{{ $item['bills'] }}</td>
                <td class="px-4 py-2">{{ $item['cost'] }}</td>
                <td class="px-4 py-2">{{ $item['amount'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Travel Expenses --}}
    @if (!empty($details['travelExpenses']))
    <h4 class="text-lg font-semibold mt-6 mb-2">Travel Reimbursement</h4>
    <table class="min-w-full border divide-y divide-gray-200 mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-left">Description</th>
                <th class="px-4 py-2 text-left">Transport Mode</th>
                <th class="px-4 py-2 text-left">Total KM</th>
                <th class="px-4 py-2 text-left">Bills</th>
                <th class="px-4 py-2 text-left">Cost Center</th>
                <th class="px-4 py-2 text-left">Amount (₹)</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($details['travelExpenses'] as $item)
            <tr>
                <td class="px-4 py-2">{{ $item['date'] }}</td>
                <td class="px-4 py-2">{{ $item['description'] }}</td>
                <td class="px-4 py-2">{{ $item['transport_mode'] }}</td>
                <td class="px-4 py-2">{{ $item['total_km'] }}</td>
                <td class="px-4 py-2">{{ $item['bills'] }}</td>
                <td class="px-4 py-2">{{ $item['cost'] }}</td>
                <td class="px-4 py-2">{{ $item['amount'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Food Expenses --}}
    @if (!empty($details['foodExpenses']))
    <h4 class="text-lg font-semibold mt-6 mb-2">Food Reimbursement</h4>
    <table class="min-w-full border divide-y divide-gray-200 mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-left">Description</th>
                <th class="px-4 py-2 text-left">Event</th>
                <th class="px-4 py-2 text-left">Bills</th>
                <th class="px-4 py-2 text-left">Cost Center</th>
                <th class="px-4 py-2 text-left">Amount (₹)</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($details['foodExpenses'] as $item)
            <tr>
                <td class="px-4 py-2">{{ $item['date'] }}</td>
                <td class="px-4 py-2">{{ $item['description'] }}</td>
                <td class="px-4 py-2">{{ $item['event'] }}</td>
                <td class="px-4 py-2">{{ $item['bills'] }}</td>
                <td class="px-4 py-2">{{ $item['cost'] }}</td>
                <td class="px-4 py-2">{{ $item['amount'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</div>
@endsection