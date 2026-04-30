@extends('layouts.vendor')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Vendor Dashboard</h2>

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

            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Account Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Vendor Name:</strong> {{ $vendor->name }}</p>
                            <p><strong>Fidelis Contact Person:</strong> {{ $vendor->contact_person }}</p>
                            <p><strong>Email:</strong> {{ $vendor->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong>
                                @if($vendor->status == 'active')
                                <span class="badge bg-success">Active</span>
                                @elseif($vendor->status == 'pending_verification')
                                <span class="badge bg-warning">Pending Verification</span>
                                @elseif($vendor->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst($vendor->status) }}</span>
                                @endif
                            </p>
                            <p><strong>Vendor Type:</strong> {{ ucfirst($vendor->type) }}</p>
                            <p><strong>Registered On:</strong> {{ $vendor->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h1 class="display-4">{{ $pendingBills }}</h1>
                            <p class="text-muted">Pending Bills</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h1 class="display-4">{{ $approvedBills }}</h1>
                            <p class="text-muted">Paid Bills</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h1 class="display-4">{{ $rejectedBills }}</h1>
                            <p class="text-muted">Rejected Bills</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('vendor.bills.create') }}" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-file-invoice me-2"></i> Upload New Bill
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('vendor.bills') }}" class="btn btn-secondary btn-lg w-100">
                                        <i class="fas fa-list me-2"></i> View All Bills
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('vendor.profile') }}" class="btn btn-info btn-lg w-100">
                                        <i class="fas fa-user-cog me-2"></i> Manage Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bills -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Bills</h5>
                    <a href="{{ route('vendor.bills') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if(count($recentBills) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Bill #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payable Amount</th>
                                    <th>TDS</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBills as $bill)
                                <tr>
                                    <td>{{ $bill->bill_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}</td>
                                    @php
                                    $fmt = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
                                    $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
                                    @endphp
                                    <td>₹{{ $fmt->format($bill->amount) }}</td>
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
                                    </td>
                                    <td>
                                        <a href="{{ route('vendor.bills.show', $bill->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('vendor.bills.download', $bill->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-download"></i> Download
                                        </a>

                                        @if($bill->status === 'rejected')
                                        <a href="{{ route('vendor.bills.edit', $bill->id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-muted">No bills uploaded yet.</p>
                        <a href="{{ route('vendor.bills.create') }}" class="btn btn-primary">Upload Your First Bill</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection