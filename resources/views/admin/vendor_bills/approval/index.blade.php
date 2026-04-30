@extends('layouts.app')

@section('title', 'Bill Approval Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bill Approval Dashboard</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Bills Pending Your Approval</h6>
        </div>
        <div class="card-body">
            @if($pendingBills->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Bill #</th>
                            <th>Contact Person</th>
                            <th>Vendor</th>
                            <th>Amount</th>
                            <th>Payable Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingBills as $bill)
                        <tr>
                            <td>{{ $bill->bill_number }}</td>
                            <td>{{ $bill->vendor->contact_person ?? 'N/A' }}</td>
                            <td>{{ $bill->vendor->name }}</td>
                            @php
                                $tds = \App\Models\TdsDeduction::where('bill_id', $bill->id)->first();
                            @endphp
                           @php
                                $amount = $tds && $tds->paid_amount ? $tds->paid_amount : $bill->total_amount;
                                $fmt = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
                                 $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
                                $formattedAmount = $fmt->format($amount);
                            @endphp

                            <td>₹{{ $formattedAmount }}</td>
                            <td>{{ $bill->payable_date?->format('d M, Y') ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    @if($bill->status == 'uploaded') bg-info
                                    @elseif($bill->status == 'under_review') bg-warning
                                    @elseif($bill->status == 'hr_approved') bg-primary
                                    @elseif($bill->status == 'cfo_approved') bg-success
                                    @elseif($bill->status == 'rejected') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ ucwords(str_replace('_', ' ', $bill->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.bills.approval.show', $bill->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <p class="lead">No bills are pending your approval at this time.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[3, "desc"]]
        });
    });
</script>
@endsection
