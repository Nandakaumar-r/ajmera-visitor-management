@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Complete Your Vendor Profile</h4>
                </div>
                <div class="card-body">
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

                    <form method="POST" action="{{ route('vendor.complete-profile') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Business Information</h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pan_number">PAN Number <span class="text-danger">*</span></label>
                                    <input id="pan_number" type="text" class="form-control @error('pan_number') is-invalid @enderror" name="pan_number" value="{{ old('pan_number') }}" required>
                                    @error('pan_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gst_number">GST Number</label>
                                    <input id="gst_number" type="text" class="form-control @error('gst_number') is-invalid @enderror" name="gst_number" value="{{ old('gst_number') }}">
                                    <small class="form-text text-muted">Optional for individuals</small>
                                    @error('gst_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" required>
                                    @error('city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State <span class="text-danger">*</span></label>
                                    <input id="state" type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="{{ old('state') }}" required>
                                    @error('state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pincode">Pincode <span class="text-danger">*</span></label>
                                    <input id="pincode" type="text" class="form-control @error('pincode') is-invalid @enderror" name="pincode" value="{{ old('pincode') }}" required>
                                    @error('pincode')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nature_of_work">Nature of Work/Services <span class="text-danger">*</span></label>
                                    <input id="nature_of_work" type="text" class="form-control @error('nature_of_work') is-invalid @enderror" name="nature_of_work" value="{{ old('nature_of_work') }}" required>
                                    <small class="form-text text-muted">E.g., IT Services, Stationery Supplier, Payroll Services, etc.</small>
                                    @error('nature_of_work')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="website">Website</label>
                                    <input id="website" type="url" class="form-control @error('website') is-invalid @enderror" name="website" value="{{ old('website') }}">
                                    <small class="form-text text-muted">Optional. Please enter a full URL (e.g., https://example.com)</small>
                                    @error('website')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 mt-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Bank Details</h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_name">Bank Name <span class="text-danger">*</span></label>
                                    <input id="bank_name" type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" value="{{ old('bank_name') }}" required>
                                    @error('bank_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account_holder_name">Account Holder Name <span class="text-danger">*</span></label>
                                    <input id="account_holder_name" type="text" class="form-control @error('account_holder_name') is-invalid @enderror" name="account_holder_name" value="{{ old('account_holder_name') }}" required>
                                    @error('account_holder_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account_number">Account Number <span class="text-danger">*</span></label>
                                    <input id="account_number" type="text" class="form-control @error('account_number') is-invalid @enderror" name="account_number" value="{{ old('account_number') }}" required>
                                    @error('account_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ifsc_code">IFSC Code <span class="text-danger">*</span></label>
                                    <input id="ifsc_code" type="text" class="form-control @error('ifsc_code') is-invalid @enderror" name="ifsc_code" value="{{ old('ifsc_code') }}" required>
                                    @error('ifsc_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="upi_id">UPI ID</label>
                                    <input id="upi_id" type="text" class="form-control @error('upi_id') is-invalid @enderror" name="upi_id" value="{{ old('upi_id') }}">
                                    <small class="form-text text-muted">Optional</small>
                                    @error('upi_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4 mt-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Required Documents</h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pan_document">PAN Card <span class="text-danger">*</span></label>
                                    <input id="pan_document" type="file" class="form-control @error('pan_document') is-invalid @enderror" name="pan_document" required>
                                    <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('pan_document')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gst_document">GST Certificate</label>
                                    <input id="gst_document" type="file" class="form-control @error('gst_document') is-invalid @enderror" name="gst_document">
                                    <small class="form-text text-muted">Optional for individuals. Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('gst_document')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cheque_document">Cancelled Cheque <span class="text-danger">*</span></label>
                                    <input id="cheque_document" type="file" class="form-control @error('cheque_document') is-invalid @enderror" name="cheque_document" required>
                                    <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('cheque_document')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="msme_certificate">MSME Certificate</label>
                                    <input id="msme_certificate" type="file" class="form-control @error('msme_certificate') is-invalid @enderror" name="msme_certificate">
                                    <small class="form-text text-muted">Optional. Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    @error('msme_certificate')
                                        <span class="invalid-feedback" role="alert">
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let contactIndex = 1;
        const contactsContainer = document.getElementById('contacts-container');
        const addContactBtn = document.getElementById('add-contact-btn');

        addContactBtn.addEventListener('click', function () {
            const newContactEntry = document.createElement('div');
            newContactEntry.className = 'contact-entry mb-3 p-3 border rounded';
            newContactEntry.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6>Contact Person ${contactIndex + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-contact-btn">Remove</button>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact_name_${contactIndex}">Full Name <span class="text-danger">*</span></label>
                            <input id="contact_name_${contactIndex}" type="text" class="form-control" name="contacts[${contactIndex}][name]" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact_designation_${contactIndex}">Designation</label>
                            <input id="contact_designation_${contactIndex}" type="text" class="form-control" name="contacts[${contactIndex}][designation]">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact_email_${contactIndex}">Email</label>
                            <input id="contact_email_${contactIndex}" type="email" class="form-control" name="contacts[${contactIndex}][email]">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact_phone_${contactIndex}">Phone</label>
                            <input id="contact_phone_${contactIndex}" type="text" class="form-control" name="contacts[${contactIndex}][phone]">
                        </div>
                    </div>
                </div>
            `;
            contactsContainer.appendChild(newContactEntry);
            contactIndex++;
        });

        contactsContainer.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-contact-btn')) {
                e.target.closest('.contact-entry').remove();
            }
        });
    });
</script>
@endpush
