@extends('layouts.app')

@section('title', 'Verify Vendor Document')

@section('content')
<div class="container-fluid">
    <div class="row mt-16">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Verify Document for {{ $vendor->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.vendors.documents.index', $vendor) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Documents
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Document Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Document Type:</strong> {{ str_replace('_', ' ', ucfirst($document->document_type)) }}</p>
                                    <p><strong>File Name:</strong> {{ $document->file_name }}</p>
                                    <p><strong>Upload Date:</strong> {{ $document->created_at->format('d M Y, h:i A') }}</p>
                                    <p>
                                        <strong>Required:</strong>
                                        @if($document->required)
                                            <span class="badge bg-primary">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </p>
                                    <p>
                                        <strong>Current Status:</strong>
                                        @if($document->verified)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Pending Verification</span>
                                        @endif
                                    </p>
                                    @if($document->verified)
                                        <p><strong>Verified By:</strong> {{ $document->verifiedByUser->name ?? 'N/A' }}</p>
                                        <p><strong>Verified At:</strong> {{ $document->verified_at ? $document->verified_at->format('d M Y, h:i A') : 'N/A' }}</p>
                                        @if($document->verification_notes)
                                            <p><strong>Verification Notes:</strong> {{ $document->verification_notes }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Document Preview</h5>
                                </div>
                                <div class="card-body text-center">
                                    @php
                                        $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($extension) === 'pdf';
                                    @endphp

                                    @if($isPdf)
                                        <div class="mb-3">
                                            <i class="far fa-file-pdf fa-5x text-danger"></i>
                                            <p class="mt-2">PDF Document</p>
                                        </div>
                                    @else
                                        <img src="{{ asset('storage/' . $document->file_path) }}" alt="Document Preview" class="img-fluid mb-3" style="max-height: 300px;">
                                    @endif

                                    <a href="{{ route('admin.vendors.documents.download', [$vendor, $document]) }}" class="btn btn-info">
                                        <i class="fas fa-download"></i> Download Document
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Verification Action</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.vendors.documents.process-verification', [$vendor, $document]) }}" method="POST">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label>Verification Decision <span class="text-danger">*</span></label>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="verification_status_1" name="verification_status" value="1" class="custom-control-input" required>
                                                <label class="custom-control-label" for="verification_status_1">Verify Document</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="verification_status_0" name="verification_status" value="0" class="custom-control-input" required>
                                                <label class="custom-control-label" for="verification_status_0">Reject Document</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="verification_notes">Verification Notes</label>
                                            <textarea name="verification_notes" id="verification_notes" rows="3" class="form-control" placeholder="Enter any notes or reasons for verification decision"></textarea>
                                            <small class="form-text text-muted">Required if rejecting the document. Optional if verifying.</small>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Submit Verification</button>
                                            <a href="{{ route('admin.vendors.documents.index', $vendor) }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Show/hide verification notes based on verification status
        $('input[name="verification_status"]').change(function() {
            if ($(this).val() == '0') {
                $('#verification_notes').attr('required', true);
            } else {
                $('#verification_notes').attr('required', false);
            }
        });
    });
</script>
@endsection
