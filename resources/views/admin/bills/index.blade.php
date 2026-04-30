@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 mt-16">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Bill Management</h2>
                <div>
                    <a href="{{ route('admin.bills.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-file-export me-1"></i> Export CSV
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
                    <h5 class="mb-0">Bills</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form action="{{ route('admin.bills.index') }}" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search bills..." name="search" value="{{ $search }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="vendor_id" class="form-select">
                                        <option value="">All Vendors</option>
                                        @foreach($vendors as $v)
                                        <option value="{{ $v->id }}" {{ $vendor_id == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>

                                        @if($userRole == 'admin' || $userRole == 'hr')
                                        <option value="uploaded" {{ $status == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
                                        <option value="under_review" {{ $status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                        @endif

                                        @if($userRole == 'admin' || $userRole == 'hr' || $userRole == 'cfo')
                                        <option value="hr_approved" {{ $status == 'hr_approved' ? 'selected' : '' }}>HR Approved</option>
                                        @endif

                                        @if($userRole == 'admin' || $userRole == 'cfo' || $userRole == 'finance')
                                        <option value="cfo_approved" {{ $status == 'cfo_approved' ? 'selected' : '' }}>CFO Approved</option>
                                        @endif

                                        @if($userRole == 'admin' || $userRole == 'finance')
                                        <option value="in_transfer" {{ $status == 'in_transfer' ? 'selected' : '' }}>In Transfer</option>
                                        <option value="transferred" {{ $status == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                        @endif

                                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(count($bills) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Bill #</th>
                                    <th>Vendor</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Amount (₹)</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Uploaded On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bills as $bill)
                                <tr>
                                    <td>{{ $bill->bill_number }}</td>
                                    <td>
                                        <a href="{{ route('admin.vendors.show', $bill->vendor_id) }}">
                                            {{ $bill->vendor->name }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}</td>
                                    <td>
                                        @if($bill->due_date)
                                        {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    @php
                                        $fmt = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
                                        $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
                                    @endphp
                                    <td>{{ $fmt->format($bill->total_amount) }}</td>
                                    <td>{{ Str::limit($bill->description, 50) }}</td>
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
                                    <td>{{ $bill->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.bills.show', $bill->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <!-- Download Bill -->
                                        <!-- <a href="{{ route('admin.bills.download', ['id' => $bill->id, 'type' => 'bill']) }}" class="btn btn-primary me-2">
                                            <i class="fas fa-download me-1"></i> Download Bill
                                        </a> -->
 
                                        <!-- Download Credit Note (only if exists) -->
                                        <!-- @if($bill->credit_note_file_path)
                                        <a href="{{ route('admin.bills.download', ['id' => $bill->id, 'type' => 'credit_note']) }}" class="btn btn-warning">
                                            <i class="fas fa-download me-1"></i> Download Credit Note
                                        </a>
                                        @endif -->
 
                                    </td>
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
                        <p class="text-muted">No bills match your search criteria.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection