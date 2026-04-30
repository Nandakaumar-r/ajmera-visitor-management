@extends('layouts.vendor')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        @if(isset($isEditMode))
                        Edit Bill #{{ $originalBill->bill_number }}
                        @elseif(isset($originalBill))
                        Create Credit Note
                        @else
                        Upload New Bill
                        @endif
                    </h5>
                </div>

                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ isset($isEditMode) ? route('vendor.bills.update', $originalBill->id) : route('vendor.bills.store') }}"
                        enctype="multipart/form-data"
                        id="billForm">

                        @csrf

                        @if(isset($isEditMode))
                        <input type="hidden" name="is_edit" value="1">
                        @endif

                        @if(isset($originalBill) && !isset($isEditMode))
                        <input type="hidden" name="is_credit_note" value="1">
                        <input type="hidden" name="original_bill_id" value="{{ $originalBill->id }}">
                        <div class="alert alert-warning mb-4">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i> Creating Credit Note</h5>
                            <p class="mb-0">You are creating a credit note for Bill #{{ $originalBill->bill_number }}
                                ({{ $originalBill->bill_date->format('d M Y') }}). The maximum credit amount is ₹{{ number_format($originalBill->total_amount, 2) }}.</p>
                        </div>
                        @endif

                        {{-- Bill fields --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Bill Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="bill_number"
                                    value="{{ old('bill_number', $originalBill->bill_number ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label>Bill Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="bill_date"
                                    value="{{ old('bill_date', isset($originalBill->bill_date) ? $originalBill->bill_date->format('Y-m-d') : '') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>{{ isset($originalBill) ? 'Credit Amount' : 'Amount' }} (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="amount"
                                    value="{{ old('amount', $originalBill->amount ?? '') }}" required {{ isset($originalBill) ? 'max=' . $originalBill->total_amount : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label>Tax Amount (₹)</label>
                                <input type="number" step="0.01" class="form-control" name="tax_amount"
                                    value="{{ old('tax_amount', $originalBill->tax_amount ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>GST Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="gst_type" required>
                                    <option value="">Select GST Type</option>
                                    @foreach($gstTypes as $type)
                                    <option value="{{ $type }}" {{ old('gst_type', $originalBill->gst_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Invoice Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="invoice_type" required>
                                    <option value="" disabled selected>Select Invoice Type</option>
                                    @foreach(['One-time','Monthly','Quarterly','Half-Yearly','Yearly'] as $type)
                                    <option value="{{ $type }}" {{ old('invoice_type', $originalBill->invoice_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="due_date"
                                    value="{{ old('due_date', isset($originalBill->due_date) ? $originalBill->due_date->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label>PO Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="po_number"
                                    value="{{ old('po_number', $originalBill->po_number ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Billing Period Start <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="billing_period_start"
                                    value="{{ old('billing_period_start', isset($originalBill->billing_period_start) ? $originalBill->billing_period_start->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label>Billing Period End <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="billing_period_end"
                                    value="{{ old('billing_period_end', isset($originalBill->billing_period_end) ? $originalBill->billing_period_end->format('Y-m-d') : '') }}" required>
                            </div>
                        </div>

                        {{-- Company Dropdown --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="company">Select Company <span class="text-danger">*</span></label>
                                <select name="company" id="companyField" class="form-control @error('company') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select your Company</option>
                                    @foreach([
                                    'Fidelis Technology Services Private Limited',
                                    'Fidelis Business Services Private Limited',
                                    'Paylink Financial Services Private Limited',
                                    'Fidelis Technologies LLC - (Dubai & Saudi)',
                                    'Fidelis Technologies PTE LTD - Singapore',
                                    'Incube Information Technology Consultancy LLC',
                                    'Aseuro Technologies Private Limited',
                                    'SunSmart Technologies Private Limited'
                                    ] as $company)
                                    <option value="{{ $company }}"
                                        {{ old('company', $originalBill->company ?? '') == $company ? 'selected' : '' }}>
                                        {{ $company }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('company')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" rows="3" required>{{ old('description', $originalBill->description ?? '') }}</textarea>
                        </div>

                        {{-- File upload --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6>Document Upload <span class="text-danger">*</span></h6>
                            </div>
                            <div class="card-body">
                                @if(isset($isEditMode) && $originalBill->file_path)
                                <div class="alert alert-secondary d-flex align-items-center justify-content-between py-2 px-3 mb-3 border-start border-3 border-primary shadow-sm rounded">
                                    <div>
                                        <strong class="text-dark">
                                            <i class="bi bi-file-earmark-text me-2 text-primary"></i> Existing File:
                                        </strong>
                                        <span class="text-muted">{{ basename($originalBill->file_path) }}</span>
                                    </div>
                                    <a href="{{ asset('storage/' . $originalBill->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View File
                                    </a>
                                </div>
                                @endif
                                <input type="file" class="form-control" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png" {{ isset($isEditMode) ? '' : 'required' }}>
                                <small class="text-muted">Allowed formats: PDF, JPG, JPEG, PNG (Max 5MB)</small>
                            </div>
                        </div>

                        {{-- Credit Note Section --}}
                        <div class="credit-note-section mt-4">
                            <button type="button" class="btn btn-primary mb-3" id="show-credit-note-form">
                                + Add / Edit Credit Note
                            </button>

                            <div id="credit-notes-wrapper" style="display:none;">
                                @php
                                $existingNotes = isset($originalBill->credit_note) ? json_decode($originalBill->credit_note, true) : [];
                                @endphp

                                @forelse($existingNotes as $index => $note)
                                <div class="credit-note-form border rounded-3 shadow-sm p-4 mb-4 bg-light" data-index="{{ $index }}">
                                    <h5 class="fw-bold text-primary mb-3">Credit Note #{{ $index + 1 }}</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Original Bill ID</label>
                                            <input type="text" class="form-control" name="credit_notes[{{ $index }}][original_bill_id]" value="{{ $note['original_bill_id'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Credit Note Number</label>
                                            <input type="text" class="form-control" name="credit_notes[{{ $index }}][credit_note_number]" value="{{ $note['credit_note_number'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Credit Amount (₹)</label>
                                            <input type="number" step="0.01" class="form-control" name="credit_notes[{{ $index }}][credit_note_amount]" value="{{ $note['credit_note_amount'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Credit GST (₹)</label>
                                            <input type="number" step="0.01" class="form-control" name="credit_notes[{{ $index }}][credit_note_gst_amount]" value="{{ $note['credit_note_gst_amount'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Credit Note Date</label>
                                            <input type="date" class="form-control" name="credit_notes[{{ $index }}][credit_note_date]" value="{{ $note['credit_note_date'] ?? '' }}">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label>Upload Credit Note File</label>
                                            <input type="file" class="form-control" name="credit_notes[{{ $index }}][credit_note_file]" accept=".pdf,.jpg,.jpeg,.png">
                                            @if(!empty($note['credit_note_file_path']))
                                            <p>Existing File: <a href="{{ asset('storage/' . $note['credit_note_file_path']) }}"  class = "text-primary btn-link" target="_blank">View</a></p>
                                            <input type="hidden" name="credit_notes[{{ $index }}][existing_credit_note_file]" value="{{ $note['credit_note_file_path'] }}">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-outline-danger remove-credit-note mt-2">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                                @empty
                                {{-- Blank Credit Note Template --}}
                                <div class="credit-note-form border rounded-3 shadow-sm p-4 mb-4 bg-light" data-index="0">
                                    <h5 class="fw-bold text-primary mb-3">Credit Note Details</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Original Bill ID</label>
                                            <input type="text" class="form-control" name="credit_notes[0][original_bill_id]" placeholder="Enter Original Bill ID">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Credit Note Number</label>
                                            <input type="text" class="form-control" name="credit_notes[0][credit_note_number]" placeholder="Enter Credit Note Number">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Credit Amount (₹)</label>
                                            <input type="number" step="0.01" class="form-control" name="credit_notes[0][credit_note_amount]" placeholder="Enter Credit Amount">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Credit GST (₹)</label>
                                            <input type="number" step="0.01" class="form-control" name="credit_notes[0][credit_note_gst_amount]" placeholder="Enter Credit GST">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Credit Note Date</label>
                                            <input type="date" class="form-control" name="credit_notes[0][credit_note_date]">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label>Upload Credit Note File</label>
                                            <input type="file" class="form-control" name="credit_notes[0][credit_note_file]" accept=".pdf,.jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-outline-danger remove-credit-note mt-2">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                                @endforelse

                                {{-- Add Another Credit Note Button --}}
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary mb-3" id="add-another-credit-note">
                                        + Add Another Credit Note
                                    </button>
                                </div>
                            </div>
                        </div>


                        {{-- Info --}}
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i> Please ensure all bill details are accurate before submission.
                        </div>

                        {{-- Submit --}}
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save me-2"></i> {{ isset($isEditMode) ? 'Update Bill' : 'Submit Bill' }}
                            </button>
                            <a href="{{ route('vendor.bills') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showBtn = document.getElementById('show-credit-note-form');
        const wrapper = document.getElementById('credit-notes-wrapper');
        const addBtn = document.getElementById('add-another-credit-note');

        // Toggle visibility of credit note section
        if (showBtn) {
            showBtn.addEventListener('click', () => {
                wrapper.style.display = wrapper.style.display === 'block' ? 'none' : 'block';
                showBtn.textContent = wrapper.style.display === 'block' ?
                    '− Hide Credit Notes' :
                    '+ Add / Edit Credit Note';
            });
        }

        // Add new blank credit note form
        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const forms = wrapper.querySelectorAll('.credit-note-form');
                const lastForm = forms[forms.length - 1];
                const newIndex = forms.length;

                // Clone without copying file info or old values
                const clone = lastForm.cloneNode(true);
                clone.setAttribute('data-index', newIndex);

                // Reset input values
                clone.querySelectorAll('input').forEach(input => {
                    input.value = '';
                    const name = input.getAttribute('name');
                    if (name) input.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
                });

                // Remove "Existing File" sections from the cloned form
                clone.querySelectorAll('p, input[type="hidden"]').forEach(el => el.remove());

                // Reinsert clone before Add button
                wrapper.insertBefore(clone, addBtn.parentElement);
            });
        }

        // Handle remove credit note button
        wrapper.addEventListener('click', (e) => {
            if (e.target.closest('.remove-credit-note')) {
                const form = e.target.closest('.credit-note-form');
                const allForms = wrapper.querySelectorAll('.credit-note-form');

                if (allForms.length > 1) {
                    form.remove();
                } else {
                    alert('At least one credit note is required.');
                }
            }
        });
    });
</script>

@endsection