@extends('layouts.app')

@section('title', 'Upload Vendor Document')

@section('content')
<div class="container-fluid">
    <div class="row mt-16">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload Document for {{ $vendor->name }}</h3>
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

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Required Documents</h5>
                                </div>
                                <div class="card-body">
                                    @if(count($missingDocuments) > 0)
                                        <div class="alert alert-warning">
                                            <strong>Missing Required Documents:</strong>
                                            <ul class="mb-0">
                                                @foreach($missingDocuments as $docType)
                                                    <li>{{ str_replace('_', ' ', ucfirst($docType)) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="alert alert-success">
                                            All required documents have been uploaded.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">Vendor Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> {{ $vendor->name }}</p>
                                    <p><strong>Type:</strong> {{ ucfirst($vendor->type) }}</p>
                                    <p><strong>Onboarding Status:</strong> 
                                        <span class="bg-secondary text-white rounded px-2 py-1
                                            @if($vendor->onboarding_status == 'pending_documents') bg-warning
                                            @elseif($vendor->onboarding_status == 'documents_uploaded') bg-info
                                            @elseif($vendor->onboarding_status == 'documents_verified') bg-success
                                            @elseif($vendor->onboarding_status == 'approval_pending') bg-primary
                                            @elseif($vendor->onboarding_status == 'approved') bg-success
                                            @elseif($vendor->onboarding_status == 'rejected') bg-danger
                                            @endif">
                                            {{ str_replace('_', ' ', ucfirst($vendor->onboarding_status)) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.vendors.documents.store', $vendor) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="document_type">Document Type <span class="text-danger">*</span></label>
                            <select name="document_type" id="document_type" class="form-control" required>
                                <option value="">Select Document Type</option>
                                
                                <!-- Required Documents Group -->
                                <optgroup label="Required Documents">
                                    @foreach($requiredDocuments as $docType => $required)
                                        @if($required && in_array($docType, $missingDocuments))
                                            <option value="{{ $docType }}">{{ str_replace('_', ' ', ucfirst($docType)) }}</option>
                                        @endif
                                    @endforeach
                                </optgroup>
                                
                                <!-- All Document Types -->
                                <optgroup label="All Document Types">
                                    <option value="pan">PAN Card</option>
                                    @if($vendor->type == 'company')
                                        <option value="gst_certificate">GST Certificate</option>
                                        <option value="incorporation_certificate">Incorporation Certificate</option>
                                    @endif
                                    <option value="cancelled_cheque">Cancelled Cheque</option>
                                    <option value="msme_certificate">MSME Certificate</option>
                                    <option value="address_proof">Address Proof</option>
                                    <option value="other">Other</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="document_file">Document File <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="document_file" name="document_file" required>
                                <label class="custom-file-label" for="document_file">Choose file</label>
                            </div>
                            <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG. Max size: 10MB</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Upload Document</button>
                            <a href="{{ route('admin.vendors.documents.index', $vendor) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Show file name when selected
        $('input[type="file"]').change(function(e) {
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });
    });
</script>
@endsection
