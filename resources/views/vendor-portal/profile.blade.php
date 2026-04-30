@extends('layouts.vendor')


@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-700 text-center">Vendor Profile</h2>
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
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
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Business Information</h5>
                    <a href="{{ route('vendor.profile.edit') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Business Name:</strong> {{ $vendor->name }}</p>
                            <p><strong>Type:</strong> {{ ucfirst($vendor->type) }}</p>
                            <p><strong>Contact Person:</strong> {{ $vendor->contact_person }}</p>
                            <p><strong>Email:</strong> {{ $vendor->email }}</p>
                            <p><strong>Phone:</strong> {{ $vendor->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>PAN Number:</strong> {{ $vendor->pan_number }}</p>
                            <p><strong>GST Number:</strong> {{ $vendor->gst_number ?: 'Not provided' }}</p>
                            <p><strong>Address:</strong> {{ $vendor->address }}</p>
                            <p><strong>City/State/Pincode:</strong> {{ $vendor->city }}, {{ $vendor->state }}, {{ $vendor->pincode }}</p>
                            <p><strong>Nature of Work:</strong> {{ $vendor->nature_of_work }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bank Details</h5>
                    <a href="{{ route('vendor.bank-details.add') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New
                    </a>
                </div>
                <div class="card-body">
                    @if(count($vendor->bankDetails) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bank Name</th>
                                        <th>Account Holder</th>
                                        <th>Account Number</th>
                                        <th>IFSC Code</th>
                                        <th>UPI ID</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendor->bankDetails as $bankDetail)
                                        <tr>
                                            <td>{{ $bankDetail->bank_name }}</td>
                                            <td>{{ $bankDetail->account_holder_name }}</td>
                                            <td>{{ substr($bankDetail->account_number, 0, 4) . '****' . substr($bankDetail->account_number, -4) }}</td>
                                            <td>{{ $bankDetail->ifsc_code }}</td>
                                            <td>{{ $bankDetail->upi_id ?: 'Not provided' }}</td>
                                            <td>
                                                @if($bankDetail->is_primary)
                                                    <span class="badge bg-success">Primary</span>
                                                @else
                                                    <span class="badge bg-secondary">Secondary</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No bank details added yet.</p>
                            <a href="{{ route('vendor.bank-details.add') }}" class="btn btn-primary">Add Bank Details</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Uploaded Documents</h5>
                </div>
                <div class="card-body">
                    @if(count($vendor->documents) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>File Name</th>
                                        <th>Uploaded On</th>
                                        <th>Verification Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendor->documents as $document)
                                        <tr>
                                            <td>
                                                @if($document->document_type == 'pan')
                                                    PAN Card
                                                @elseif($document->document_type == 'gst')
                                                    GST Certificate
                                                @elseif($document->document_type == 'cancelled_cheque')
                                                    Cancelled Cheque
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}
                                                @endif
                                            </td>
                                            <td>{{ $document->file_name }}</td>
                                            <td>{{ $document->created_at->format('d M Y') }}</td>
                                            <td>
                                                @if($document->verified)
                                                    <span class="badge bg-success">Verified</span>
                                                @elseif($document->verified)
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning">Pending Verification</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No documents uploaded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
