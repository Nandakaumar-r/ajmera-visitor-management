@extends('layouts.vendor')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Bills</h2>
                <div>
                    <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Dashboard
                    </a>
                    <a href="{{ route('vendor.bills.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Upload New Bill
                    </a>
                </div>
            </div>

            @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">All Bills</h5>
                </div>
                <div class="card-body">
                    @if(count($bills) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Bill #</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Amount (₹)</th>
                                    <th>Payable Amount (₹)</th>
                                    <th>TDS (%)</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Uploaded On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bills as $bill)
                                <tr class="{{ $bill->is_credit_note ? 'table-warning' : '' }}">
                                    <td>
                                        @if($bill->is_credit_note)
                                        <span class="badge bg-warning">Credit Note</span>
                                        @if($bill->originalBill)
                                        <div class="small text-muted">For: {{ $bill->originalBill->bill_number }}</div>
                                        @endif
                                        @else
                                        <span class="badge bg-primary">Invoice</span>
                                        @if($bill->creditNotes->count() > 0)
                                        <div class="small text-muted">Has {{ $bill->creditNotes->count() }} credit note(s)</div>
                                        @endif
                                        @endif
                                    </td>
                                    <td>
                                        {{ $bill->bill_number }}
                                        @if($bill->is_credit_note && $bill->credit_note_number)
                                        <div class="small text-muted">CN #: {{ $bill->credit_note_number }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}
                                        @if($bill->is_credit_note && $bill->credit_note_date)
                                        <div class="small text-muted">CN Date: {{ \Carbon\Carbon::parse($bill->credit_note_date)->format('d M Y') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}
                                    </td>
                                    @php
                                    $fmt = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
                                    $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
                                    @endphp
                                    <td>
                                        @if($bill->is_credit_note)
                                        <span class="text-danger">-{{ $fmt->format($bill->total_amount) }}</span>
                                        @else
                                        {{ $fmt->format($bill->total_amount) }}
                                        @endif
                                    </td>
                                    @php
                                    $tds = \App\Models\TdsDeduction::where('bill_id', $bill->id)->first();
                                    @endphp
                                    <td>₹{{ $fmt->format($tds?->paid_amount ?? $bill->total_amount) }}</td>
                                    <td>{{ number_format($tds?->deduction_percentage ?? 0, 2) }}%</td>
                                    <td class="truncate">{{ $bill->rejection_reason ?? 'N/A' }}</td>
                                    <td>
                                        @if($bill->status == 'uploaded')
                                        <span class="badge bg-info">Uploaded</span>
                                        @elseif($bill->status == 'under_review')
                                        <span class="badge bg-warning">Under Review</span>
                                        @elseif($bill->status == 'hr_approved')
                                        <span class="badge bg-primary">HR Approved</span>
                                        @elseif($bill->status == 'cfo_approved')
                                        <span class="badge bg-primary">CFO Approved</span>
                                        @elseif($bill->status == 'in_transfer')
                                        <span class="badge bg-info">In Transfer</span>
                                        @elseif($bill->status == 'transferred')
                                        <span class="badge bg-success">Transferred</span>
                                        @elseif($bill->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                        @else
                                        <span class="badge bg-secondary">{{ ucfirst($bill->status) }}</span>
                                        @endif

                                        @if($bill->is_credit_note && $bill->credit_note_reason)
                                        <div class="small text-muted mt-1">Reason: {{ $bill->credit_note_reason }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $bill->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vendor.bills.show', $bill->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                                View
                                            </a>
                                            <a href="{{ route('vendor.bills.download', $bill->id) }}" class="btn btn-sm btn-outline-secondary" title="Download">
                                                <i class="fas fa-download"></i>
                                                Download
                                            </a>
                                            <!-- @if(!$bill->is_credit_note && in_array($bill->status, ['transferred', 'cfo_approved']))
                                                        <a href="{{ route('vendor.bills.create-credit-note', $bill->id) }}" class="btn btn-sm btn-outline-warning" title="Create Credit Note">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </a>
                                                    @endif -->
                                            @if($bill->status === 'rejected')
                                            <a href="{{ route('vendor.bills.edit', $bill->id) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            @endif
                                        </div>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $bills->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                        <h4>No Bills Found</h4>
                        <p class="text-muted">You haven't uploaded any bills yet.</p>
                        <a href="{{ route('vendor.bills.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i> Upload Your First Bill
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection