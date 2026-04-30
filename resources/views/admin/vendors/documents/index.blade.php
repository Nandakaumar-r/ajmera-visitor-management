@extends('layouts.app')

@section('title', 'Vendor Documents')

@section('content')
<div class="container-fluid py-16">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Documents for {{ $vendor->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.vendors.documents.create', $vendor) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Upload New Document
                        </a>
                        <a href="{{ route('admin.vendors.show', $vendor) }}" class="btn btn-secondary btn-sm ml-2">
                            <i class="fas fa-arrow-left"></i> Back to Vendor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Vendor Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> {{ $vendor->name }}</p>
                                    <p><strong>Type:</strong> {{ ucfirst($vendor->type) }}</p>
                                    <p><strong>Status:</strong> {{ ucfirst($vendor->status) }}</p>
                                    <p><strong>Onboarding Status:</strong> 
                                        <span class="badge bg-success
                                            @if($vendor->onboarding_status == 'pending_documents') badge-warning
                                            @elseif($vendor->onboarding_status == 'documents_uploaded') badge-info
                                            @elseif($vendor->onboarding_status == 'documents_verified') badge-success
                                            @elseif($vendor->onboarding_status == 'approval_pending') badge-primary
                                            @elseif($vendor->onboarding_status == 'approved') badge-success
                                            @elseif($vendor->onboarding_status == 'rejected') badge-danger
                                            @endif">
                                            {{ str_replace('_', ' ', ucfirst($vendor->onboarding_status)) }}
                                        </span>
                                    </p>
                                    @if($vendor->type == 'company')
                                        <p>
                                            <strong>GST Number:</strong> {{ $vendor->gst_number ?? 'Not provided' }}
                                            @if($vendor->gst_number)
                                                @if($vendor->gst_verified)
                                                    <span class="badge bg-success">Verified</span>
                                                @else
                                                    <span class="badge bg-warning">Not Verified</span>
                                                @endif
                                            @endif
                                        </p>
                                    @endif
                                    <p>
                                        <strong>PAN Number:</strong> {{ $vendor->pan_number ?? 'Not provided' }}
                                        @if($vendor->pan_number)
                                            @if($vendor->pan_verified)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-warning">Not Verified</span>
                                            @endif
                                        @endif
                                    </p>
                                    <a href="https://services.gst.gov.in/services/searchtp" target="_blank" class="btn btn-sm btn-info mt-2">
                                        <i class="fas fa-external-link-alt"></i> Verify GST Information
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Required Documents</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($requiredDocuments as $docType => $required)
                                            @if($required)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ str_replace('_', ' ', ucfirst($docType)) }}
                                                    @if(in_array($docType, $missingDocuments))
                                                        <span class="badge bg-danger">Missing</span>
                                                    @else
                                                        <span class="badge bg-success">Uploaded</span>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <!-- <th>File Name</th> -->
                                        <th>Required</th>
                                        <th>Verification Status</th>
                                        <th>Verified By</th>
                                        <th>Verified At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $document)
                                        <tr>
                                            <td>{{ str_replace('_', ' ', ucfirst($document->document_type)) }}</td>
                                            <!-- <td>{{ $document->file_name }}</td> -->
                                            <td>
                                                @if($document->required)
                                                    <span class="badge bg-primary">Required</span>
                                                @else
                                                    <span class="badge bg-secondary">Optional</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($document->verified)
                                                    <span class="badge bg-success">Verified</span>
                                                @else
                                                    <span class="badge bg-warning">Pending Verification</span>
                                                @endif
                                                @if($document->verification_notes)
                                                    <i class="fas fa-info-circle" data-toggle="tooltip" title="{{ $document->verification_notes }}"></i>
                                                @endif
                                            </td>
                                            <td>{{ $document->verifiedByUser->name ?? 'N/A' }}</td>
                                            <td>{{ $document->verified_at ? $document->verified_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group gap-2">
                                                    <a href="{{ route('admin.vendors.documents.download', [$vendor, $document]) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-download">Download</i>
                                                    </a>
                                                    @if(!$document->verified)
                                                        <a href="{{ route('admin.vendors.documents.verify', [$vendor, $document]) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-check-circle"></i> Verify
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('admin.vendors.documents.destroy', [$vendor, $document]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash">Delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No documents uploaded yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
