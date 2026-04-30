@extends('layouts.vendor')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Bill Details</h2>
                <a href="{{ route('vendor.bills') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Bills
                </a>
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

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $bill->is_credit_note ? 'Credit Note' : 'Bill' }} Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>{{ $bill->is_credit_note ? 'Invoice Number:' : 'Bill/Invoice Number:' }}</strong> {{ $bill->bill_number }}</p>
                                    <p><strong>{{ $bill->is_credit_note ? 'Invoice Date:' : 'Bill Date:' }}</strong> {{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}</p>
                                    @if($bill->is_credit_note)
                                    <p><strong>Credit Note Number:</strong> {{ $bill->credit_note_number ?? 'Not specified' }}</p>
                                    <p><strong>Credit Note Date:</strong>
                                        @if($bill->credit_note_date)
                                        {{ \Carbon\Carbon::parse($bill->credit_note_date)->format('d M Y') }}
                                        @else
                                        Not specified
                                        @endif
                                    </p>
                                    @else
                                    <p><strong>Due Date:</strong>
                                        @if($bill->due_date)
                                        {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}
                                        @else
                                        Not specified
                                        @endif
                                    </p>
                                    @endif
                                    <p class="truncate"><strong>Company:</strong> {{ $bill->company ?? 'N/A' }}</p>
                                    @if($bill->billing_period_start || $bill->billing_period_end)
                                            <p><strong>Billing Period:</strong>
                                                @if($bill->billing_period_start)
                                                {{ \Carbon\Carbon::parse($bill->billing_period_start)->format('d M Y') }}
                                                @endif
                                                @if($bill->billing_period_end)
                                                to {{ \Carbon\Carbon::parse($bill->billing_period_end)->format('d M Y') }}
                                                @endif
                                            </p>
                                    @endif
                                    @if($bill->description)
                                        <p><strong>Description:</strong></p>
                                        <p>{{ $bill->description }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Base Amount:</strong>
                                        @if($bill->is_credit_note)
                                        <span class="text-danger">-₹{{ number_format($bill->amount, 2) }}</span>
                                        @else
                                        ₹{{ number_format($bill->amount, 2) }}
                                        @endif
                                    </p>
                                    <p><strong>Tax Amount:</strong>
                                        @if($bill->is_credit_note)
                                        <span class="text-danger">-₹{{ number_format($bill->tax_amount, 2) }}</span>
                                        @else
                                        ₹{{ number_format($bill->tax_amount, 2) }}
                                        @endif
                                    </p>
                                    <p><strong>Total Amount:</strong>
                                        @if($bill->is_credit_note)
                                        <span class="text-danger">-₹{{ number_format($bill->total_amount, 2) }}</span>
                                        @else
                                        ₹{{ number_format($bill->total_amount, 2) }}
                                        @endif
                                    </p>
                                    <p><strong>PO Number:</strong> {{ $bill->po_number ?? 'N/A' }}</p>
                                    <p><strong>Invoice Type:</strong> {{ $bill->invoice_type ?? 'N/A' }}</p>
                                    @if($bill->status == 'rejected')
                                    <p class="text-danger fw-bold"><strong>Reason:</strong> {{ $bill->rejection_reason ?? 'N/A' }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Add this block to display related credit notes -->
                            @php
                            $creditNotes = [];
                            if (!empty($bill->credit_note)) {
                            $creditNotes = is_array($bill->credit_note)
                            ? $bill->credit_note
                            : (json_decode($bill->credit_note, true) ?: []);
                            }
                            @endphp

                            @if(!empty($creditNotes) && count($creditNotes) > 0)
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Related Credit Notes</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Credit Note Number</th>
                                                    <th>Original Bill ID</th>
                                                    <th>Date</th>
                                                    <th>Base Amount (₹)</th>
                                                    <th>GST Amount (₹)</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($creditNotes as $note)
                                                <tr>
                                                    <td>{{ $note['credit_note_number'] ?? '-' }}</td>
                                                    <td>{{ $note['original_bill_id'] ?? '-' }}</td>
                                                    <td>
                                                        @if(!empty($note['credit_note_date']))
                                                        {{ \Carbon\Carbon::parse($note['credit_note_date'])->format('d M Y') }}
                                                        @else
                                                        -
                                                        @endif
                                                    </td>
                                                    <td>₹{{ number_format((float) ($note['credit_note_amount'] ?? 0), 2) }}</td>
                                                    <td>₹{{ number_format((float) ($note['credit_note_gst_amount'] ?? 0), 2) }}</td>
                                                    <td>
                                                        @if(!empty($note['credit_note_file_path']))
                                                        <a href="{{ asset('storage/' . ltrim($note['credit_note_file_path'], '/')) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-download me-1"></i> View
                                                        </a>
                                                        @else
                                                        -
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif


                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p><strong>Uploaded On:</strong> {{ $bill->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <a href="{{ route('vendor.bills.download', $bill->id) }}" class="btn btn-primary">
                                        <i class="fas fa-download me-2"></i> Download Invoice Bill
                                    </a>
                                    <a href="{{ asset('storage/' . ltrim($bill['file_path'], '/')) }}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-download me-2"></i> View Invoice Bill
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Status</h5>
                        </div>
                        <div class="card-body text-center">
                            @if($bill->status == 'uploaded')
                            <div class="mb-3">
                                <span class="badge bg-info p-2 fs-6">Uploaded</span>
                            </div>
                            <p>Your bill has been uploaded and is pending review by our HR team.</p>
                            @elseif($bill->status == 'under_review')
                            <div class="mb-3">
                                <span class="badge bg-warning p-2 fs-6">Under Review</span>
                            </div>
                            <p>Your bill is currently being reviewed by our HR team.</p>
                            @elseif($bill->status == 'hr_approved')
                            <div class="mb-3">
                                <span class="badge bg-primary p-2 fs-6">HR Approved</span>
                            </div>
                            <p>Your bill has been approved by HR and is awaiting CFO approval.</p>
                            @elseif($bill->status == 'cfo_approved')
                            <div class="mb-3">
                                <span class="badge bg-primary p-2 fs-6">CFO Approved</span>
                            </div>
                            <p>Your bill has been approved by the CFO and is awaiting payment processing.</p>
                            @elseif($bill->status == 'in_transfer')
                            <div class="mb-3">
                                <span class="badge bg-info p-2 fs-6">In Transfer</span>
                            </div>
                            <p>Your payment is being processed by our finance team.</p>
                            @elseif($bill->status == 'transferred')
                            <div class="mb-3">
                                <span class="badge bg-success p-2 fs-6">Transferred</span>
                            </div>
                            <p>Payment has been successfully transferred to your account.</p>
                            @elseif($bill->status == 'rejected')
                            <div class="mb-3">
                                <span class="badge bg-danger p-2 fs-6">Rejected</span>
                            </div>
                            <p>Your bill has been rejected. Please check the comments below for details.</p>
                            <p class="text-danger fw-bold"><strong>Reason:</strong> {{ $bill->rejection_reason ?? 'N/A' }}</p>
                            @else
                            <div class="mb-3">
                                <span class="badge bg-secondary p-2 fs-6">{{ ucfirst($bill->status) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Status History</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($bill->statusHistory as $history)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            @if($history->status == 'uploaded')
                                            <span class="badge bg-info">Uploaded</span>
                                            @elseif($history->status == 'under_review')
                                            <span class="badge bg-warning">Under Review</span>
                                            @elseif($history->status == 'hr_approved')
                                            <span class="badge bg-primary">HR Approved</span>
                                            @elseif($history->status == 'cfo_approved')
                                            <span class="badge bg-primary">CFO Approved</span>
                                            @elseif($history->status == 'in_transfer')
                                            <span class="badge bg-info">In Transfer</span>
                                            @elseif($history->status == 'transferred')
                                            <span class="badge bg-success">Transferred</span>
                                            @elseif($history->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                            @else
                                            <span class="badge bg-secondary">{{ ucfirst($history->status) }}</span>
                                            @endif
                                        </span>
                                        <small class="text-muted">{{ $history->created_at->format('d M Y, h:i A') }}</small>
                                    </div>
                                    @if($history->comments)
                                    <p class="mt-2 mb-0">{{ $history->comments }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection