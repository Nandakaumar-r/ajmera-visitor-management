@extends('layouts.app')

@section('content')
<div class="container py-4 mt-16">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Bill Details</h2>
                <a href="{{ route('admin.bills.index') }}" class="btn btn-outline-secondary">
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
                            <h5 class="mb-0">Bill Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Vendor:</strong>
                                        <a href="{{ route('admin.vendors.show', $bill->vendor_id) }}">
                                            {{ $bill->vendor->name }}
                                        </a>
                                    </p>
                                    <p><strong>Bill/Invoice Number:</strong> {{ $bill->bill_number }}</p>
                                    <p><strong>Bill Date:</strong> {{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}</p>
                                    <p><strong>Due Date:</strong>
                                        @if($bill->due_date)
                                        {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}
                                        @else
                                        Not specified
                                        @endif
                                    </p>
                                </div>
                                @php
                                    $fmt = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
                                    $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
                                @endphp
                                <div class="col-md-6">
                                    <p><strong>Base Amount:</strong> ₹{{ $fmt->format($bill->amount) }}</p>
                                    <p><strong>Tax Amount:</strong> ₹{{ $fmt->format($bill->tax_amount) }}</p>
                                    <p><strong>Total Amount:</strong> ₹{{ $fmt->format($bill->total_amount) }}</p>
                                    <p><strong>Uploaded On:</strong> {{ $bill->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>

                            @if($bill->billing_period_start || $bill->billing_period_end)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p><strong>Billing Period:</strong>
                                        @if($bill->billing_period_start)
                                        {{ \Carbon\Carbon::parse($bill->billing_period_start)->format('d M Y') }}
                                        @endif
                                        @if($bill->billing_period_end)
                                        to {{ \Carbon\Carbon::parse($bill->billing_period_end)->format('d M Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif

                            @if($bill->description)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p><strong>Description:</strong></p>
                                    <p>{{ $bill->description }}</p>
                                </div>
                            </div>
                            @endif

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
                                                    <th class="truncate">Credit Note Number</th>
                                                    <th class="truncate">Original Bill ID</th>
                                                    <th>Date</th>
                                                    <th class="truncate">Base Amount (₹)</th>
                                                    <th class="truncate">GST Amount (₹)</th>
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

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <a href="{{ route('admin.bills.download', $bill->id) }}" class="btn btn-primary">
                                        <i class="fas fa-download me-2"></i> Download Invoice Bill
                                    </a>
                                    <a href="{{ asset('storage/' . ltrim($bill['file_path'], '/')) }}" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-download me-2"></i> View Invoice Bill
                                    </a>
                                </div>
                            </div>
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
                                        <div>
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

                                            @if($history->updatedBy)
                                            by <strong>{{ $history->updatedBy->name }}</strong>
                                            @endif
                                        </div>
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

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Current Status</h5>
                        </div>
                        <div class="card-body text-center">
                            @if($bill->status == 'uploaded')
                            <div class="mb-3">
                                <span class="badge bg-info p-2 fs-6">Uploaded</span>
                            </div>
                            <p>This bill has been uploaded by the vendor and is pending initial review.</p>
                            @elseif($bill->status == 'under_review')
                            <div class="mb-3">
                                <span class="badge bg-warning p-2 fs-6">Under Review</span>
                            </div>
                            <p>This bill is currently being reviewed by the HR team.</p>
                            @elseif($bill->status == 'hr_approved')
                            <div class="mb-3">
                                <span class="badge bg-primary p-2 fs-6">HR Approved</span>
                            </div>
                            <p>This bill has been approved by HR and is awaiting CFO approval.</p>
                            @elseif($bill->status == 'cfo_approved')
                            <div class="mb-3">
                                <span class="badge bg-primary p-2 fs-6">CFO Approved</span>
                            </div>
                            <p>This bill has been approved by the CFO and is awaiting payment processing.</p>
                            @elseif($bill->status == 'in_transfer')
                            <div class="mb-3">
                                <span class="badge bg-info p-2 fs-6">In Transfer</span>
                            </div>
                            <p>Payment for this bill is currently being processed.</p>
                            @elseif($bill->status == 'transferred')
                            <div class="mb-3">
                                <span class="badge bg-success p-2 fs-6">Transferred</span>
                            </div>
                            <p>Payment has been successfully transferred to the vendor's account.</p>
                            @elseif($bill->status == 'rejected')
                            <div class="mb-3">
                                <span class="badge bg-danger p-2 fs-6">Rejected</span>
                            </div>
                            <p>This bill has been rejected. See comments for details.</p>
                            @else
                            <div class="mb-3">
                                <span class="badge bg-secondary p-2 fs-6">{{ ucfirst($bill->status) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    @php
                    $canUpdateStatus = false;
                    $nextStatuses = [];

                    if ($userRole === 'hr' && in_array($bill->status, ['uploaded', 'under_review'])) {
                    $canUpdateStatus = true;
                    $nextStatuses = [
                    'under_review' => 'Mark as Under Review',
                    'hr_approved' => 'Approve',
                    'rejected' => 'Reject'
                    ];

                    // Remove current status from options
                    if ($bill->status === 'under_review') {
                    unset($nextStatuses['under_review']);
                    }
                    } elseif ($userRole === 'cfo' && $bill->status === 'hr_approved') {
                    $canUpdateStatus = true;
                    $nextStatuses = [
                    'cfo_approved' => 'Approve',
                    'rejected' => 'Reject'
                    ];
                    } elseif ($userRole === 'finance' && in_array($bill->status, ['cfo_approved', 'in_transfer'])) {
                    $canUpdateStatus = true;
                    if ($bill->status === 'cfo_approved') {
                    $nextStatuses = [
                    'in_transfer' => 'Mark as In Transfer',
                    'transferred' => 'Mark as Transferred'
                    ];
                    } else {
                    $nextStatuses = [
                    'transferred' => 'Mark as Transferred'
                    ];
                    }
                    } elseif ($userRole === 'admin') {
                    $canUpdateStatus = true;
                    $nextStatuses = [
                    'uploaded' => 'Mark as Uploaded',
                    'under_review' => 'Mark as Under Review',
                    'hr_approved' => 'Mark as HR Approved',
                    'cfo_approved' => 'Mark as CFO Approved',
                    'in_transfer' => 'Mark as In Transfer',
                    'transferred' => 'Mark as Transferred',
                    'rejected' => 'Mark as Rejected'
                    ];

                    // Remove current status from options
                    unset($nextStatuses[$bill->status]);
                    }
                    @endphp

                    @if($canUpdateStatus)
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Update Status</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.bills.update-status', $bill->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group mb-3">
                                    <label for="status">New Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        @foreach($nextStatuses as $status => $label)
                                        <option value="{{ $status }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="comments">Comments</label>
                                    <textarea name="comments" id="comments" rows="4" class="form-control"></textarea>
                                    <small class="form-text text-muted">
                                        @if(in_array('rejected', array_keys($nextStatuses)))
                                        If rejecting, please provide a reason.
                                        @else
                                        Optional comments about this status change.
                                        @endif
                                    </small>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Status
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection