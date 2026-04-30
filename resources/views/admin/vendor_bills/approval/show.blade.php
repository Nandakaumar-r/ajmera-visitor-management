@extends('layouts.app')

@section('title', 'Review Bill')

@section('content')
<div class="container py-4 mt-16">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Review Bill #{{ $bill->bill_number }}</h2>
        <a href="{{ route('admin.bills.approval.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Approval Dashboard
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Bill Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Bill Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Vendor:</strong>
                                <a href="{{ route('admin.vendors.show', $bill->vendor_id) }}">
                                    {{ $bill->vendor->name }}
                                </a>
                            </p>
                            <p class = "truncate"><strong>Company:</strong> {{ $bill->company ?? 'N/A' }}</p>
                            <p><strong>Bill Number:</strong> {{ $bill->bill_number }}</p>
                            <p><strong>Bill Date:</strong> {{ $bill->bill_date->format('d M, Y') }}</p>
                            <p><strong>Due Date:</strong>
                                {{ $bill->due_date ? $bill->due_date->format('d M, Y') : 'N/A' }}
                            </p>
                            <form action="{{ route('admin.bills.updateDate', $bill->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                                <div class="flex gap-3 items-center mt-3">
                                    <p><strong>Payable Date:</strong></p>
                                    <input type="date"
                                        id="payable_date"
                                        name="payable_date"
                                        class="form-control w-50"
                                        value="{{ $bill->payable_date ? $bill->payable_date->format('Y-m-d') : '' }}"
                                        onchange="this.form.submit()">
                                </div>
                            </form>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    //const today = new Date().toISOString().split('T')[0]; // e.g., "2025-11-05"
                                    const billDateInput = document.getElementById('payable_date');

                                    // Set today's date as the minimum
                                    billDateInput.setAttribute('min');

                                    // If existing value is before today, reset it
                                    // if (billDateInput.value && billDateInput.value < today) {
                                    //     alert("Backdated entries are not allowed. Please select a valid date.");
                                    //     billDateInput.value = today;
                                    // }
                                });
                            </script>
                        </div>
                        <div class="col-md-6">
                            @php
                                $fmt = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
                                $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
                            @endphp
                            <p><strong>Base Amount:</strong> ₹{{ $fmt->format($bill->amount) }}</p>
                            <p><strong>Tax Amount:</strong> ₹{{ $fmt->format($bill->tax_amount) }}</p>
                            <p><strong>Total Amount:</strong> ₹{{ $fmt->format($bill->total_amount) }}</p>
                            <p><strong>Billing Period:</strong>
                                @if($bill->billing_period_start && $bill->billing_period_end)
                                {{ $bill->billing_period_start->format('d M, Y') }} -
                                {{ $bill->billing_period_end->format('d M, Y') }}
                                @else
                                N/A
                                @endif
                            </p>
                            <p><strong>PO Number:</strong> {{ $bill->po_number ?? 'N/A' }}</p>
                            <p><strong>Invoice Type:</strong> {{ $bill->invoice_type ?? 'N/A' }}</p>
                            @if($bill->description)
                            <p><strong>Description:</strong></p>
                            <p>{{ $bill->description }}</p>
                            @endif
                        </div>
                        <form action="{{ route('admin.bills.tds.save', $bill->id) }}" method="POST">
                            @csrf
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">TDS Calculation</h5>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="selected_credit_notes" id="selected_credit_notes">
                                    <div class="row mb-3 align-items-end">
                                        <div class="col-md-3">
                                            <label for="tds_percentage" class="form-label"><strong>TDS %</strong></label>
                                            <input type="number" id="tds_percentage" name="tds_percentage" class="form-control"
                                                placeholder="Enter %" step="0.01" min="0" max="100"
                                                value="{{ $tds->deduction_percentage ?? '' }}" oninput="calculateTDS()">
                                        </div>

                                        <div class="col-md-3">
                                            <label><strong>TDS Amount (₹)</strong></label>
                                            <input type="text" id="tds_amount" name="tds_amount" class="form-control"
                                                readonly value="{{ number_format($tds->deduction_amount ?? 0, 2) }}">
                                        </div>

                                        <div class="col-md-3">
                                            <label><strong>After TDS (₹)</strong></label>
                                            <input type="text" id="after_tds" name="after_tds" class="form-control"
                                                readonly value="{{ number_format($tds->after_tds ?? 0, 2) }}">
                                        </div>

                                        <div class="col-md-3">
                                            <label><strong>Payable Amount (₹)</strong></label>
                                            <input type="text" id="paid_amount" name="paid_amount" class="form-control"
                                                value="{{ number_format($tds->paid_amount ?? 0, 2) }}">
                                        </div>
                                    </div>

                                    <div class="row mt-2 align-items-center">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="include_tax" name="include_tax"
                                                    onchange="calculateTDS()"
                                                    {{ $bill->include_tax_checked ? 'checked' : '' }}>
                                                <label class="form-check-label" for="include_tax">
                                                    <strong>Include Tax Amount (₹{{ number_format($bill->tax_amount, 2) }})</strong>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-save me-1"></i> Save TDS
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Credit Notes Section -->
                    @php
                    $creditNotes = [];
                    if (!empty($bill->credit_note)) {
                    $creditNotes = is_array($bill->credit_note)
                    ? $bill->credit_note
                    : (json_decode($bill->credit_note, true) ?: []);
                    }
                    @endphp

                    @if(!empty($creditNotes) && count($creditNotes) > 0)
                    <div class="card mb-4 mt-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Related Credit Notes</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Select</th>
                                            <th>Credit Note ID</th>
                                            <th>Original Bill ID</th>
                                            <th>Date</th>
                                            <th>Base (₹)</th>
                                            <th>GST(₹)</th>
                                            <th>Total(₹)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($creditNotes as $index => $note)
                                        @php
                                        $base = (float) ($note['credit_note_amount'] ?? 0);
                                        $gst = (float) ($note['credit_note_gst_amount'] ?? 0);
                                        $totalCredit = $base + $gst;
                                        @endphp
                                        <tr>
                                            <td>
                                                @php
                                                $selectedNotes = json_decode($bill->selected_credit_notes ?? '[]', true);
                                                @endphp
                                                <input type="checkbox"
                                                    class="form-check-input credit-note-checkbox"
                                                    data-credit="{{ $totalCredit }}"
                                                    onchange="calculateTDS()"
                                                    {{ in_array($totalCredit, $selectedNotes ?? []) ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $note['credit_note_number'] ?? '-' }}</td>
                                            <td>{{ $note['original_bill_id'] ?? '-' }}</td>
                                            <td>
                                                @if(!empty($note['credit_note_date']))
                                                {{ \Carbon\Carbon::parse($note['credit_note_date'])->format('d M Y') }}
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>₹{{ number_format($base, 2) }}</td>
                                            <td>₹{{ number_format($gst, 2) }}</td>
                                            <td><strong>₹{{ number_format($totalCredit, 2) }}</strong></td>
                                            <td>
                                                @if(!empty($note['credit_note_file_path']))
                                                <a href="{{ asset('storage/' . ltrim($note['credit_note_file_path'], '/')) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i> View
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

                    <!-- Download/View Buttons -->
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.bills.download', $bill->id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-download me-1"></i> Download Bill
                        </a>
                        <a href="{{ asset('storage/' . ltrim($bill['file_path'], '/')) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> View Bill
                        </a>
                    </div>
                    <!-- <p><strong>Total Amount:</strong> ₹{{ number_format($bill->amount, 2) }}</p> -->
                </div>
            </div>

            <!-- Approval History -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Approval History</h5>
                </div>
                <div class="card-body p-0">
                    @if($bill->approvalHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Approver</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bill->approvalHistory->sortByDesc('action_date') as $history)
                                <tr>
                                    <td>{{ $history->action_date->format('d M, Y H:i') }}</td>
                                    <td>{{ $history->approver->name ?? 'System' }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $history->approver_role)) }}</td>
                                    <td>
                                        <span class="badge {{ $history->status == 'approved' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($history->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $history->comments ?? 'No comments' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-3 text-center">No approval history available yet.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Approval Actions -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Approval Actions</h5>
                </div>
                <div class="card-body">
                   @if($currentUserRole == 'payment_processor' && $bill->status == 'cfo_approved')
                    <form action="{{ route('admin.bills.approval.process-payment', $bill->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="payment_status">Payment Status</label>
                            <select class="form-control" id="payment_status" name="payment_status" required>
                                <option value="">Select Status</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="payment_notes">Payment Notes</label>
                            <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3"></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="amount">Payment Amount (₹)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required
                                placeholder="Enter Payment Amount">
                            <small id="amountError" class="text-danger d-none">⚠ Payment amount cannot exceed Payable Amount.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="transaction_id">UTR Number</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" required
                                placeholder="Enter Transaction ID (if applicable)">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Payment Status</button>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const payableAmount = parseFloat("{{ $tds->paid_amount ?? 0 }}") || 0;
                            const amountInput = document.getElementById('amount');
                            const errorText = document.getElementById('amountError');

                            amountInput.addEventListener('input', function() {
                                const enteredValue = parseFloat(amountInput.value) || 0;

                                if (enteredValue > payableAmount) {
                                    errorText.classList.remove('d-none');
                                    amountInput.classList.add('is-invalid');
                                    amountInput.value = payableAmount.toFixed(2); // optional: auto-limit to payable
                                } else {
                                    errorText.classList.add('d-none');
                                    amountInput.classList.remove('is-invalid');
                                }
                            });
                        });
                    </script>

                    @elseif(in_array($currentUserRole, ['initial_approver', 'hr_approver', 'finance_approver', 'cfo_approver']))
                    <form action="{{ route('admin.bills.approval.update', $bill->id) }}" method="POST">
                        @csrf
                       <div class="form-group mb-3">
                            <label for="comments">Comments</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    name="status"
                                    value="approved"
                                    class="btn btn-success w-50"
                                    onclick="setCommentRequired(false)">
                                Approve
                            </button>

                            <button type="submit"
                                    name="status"
                                    value="rejected"
                                    class="btn btn-danger w-50"
                                    onclick="setCommentRequired(true)">
                                Reject
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-info">This bill is currently being reviewed by another team member.</div>
                    @endif
                </div>
            </div>

            <!-- Approval Workflow -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Approval Workflow</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Initial Approver:
                            <strong>{{ $approvalWorkflow->initialApprover->name ?? 'Not assigned' }}</strong>
                        </li>
                        <li class="list-group-item">HR Approver:
                            <strong>{{ $approvalWorkflow->hrApprover->name ?? 'Not assigned' }}</strong>
                        </li>
                        <li class="list-group-item">Finance Approver:
                            <strong>{{ $approvalWorkflow->financeApprover->name ?? 'Not assigned' }}</strong>
                        </li>
                        <li class="list-group-item">CFO Approver:
                            <strong>{{ $approvalWorkflow->cfoApprover->name ?? 'Not assigned' }}</strong>
                        </li>
                        <li class="list-group-item">Payment Processor:
                            <strong>{{ $approvalWorkflow->paymentProcessor->name ?? 'Not assigned' }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setCommentRequired(isRequired) {
        const comments = document.getElementById('comments');
        comments.required = isRequired;

        if (isRequired && comments.value.trim() === '') {
            comments.focus();
        }
    }
    function calculateTDS() {
        const totalAmount = {{ $bill->amount ?? 0 }};
        const taxAmount = {{ $bill->tax_amount ?? 0 }};
        const tdsPercent = parseFloat(document.getElementById('tds_percentage').value) || 0;

         // 🔒 Prevent value > 100%
        if (tdsPercent > 100) {
            alert("TDS percentage cannot exceed 100%");
            tdsInput.value = 100;
            tdsPercent = 100;
        } else if (tdsPercent < 0) {
            alert("TDS percentage cannot be negative");
            tdsInput.value = 0;
            tdsPercent = 0;
        }

        // Get checkbox value
        const includeTax = document.getElementById('include_tax')?.checked || false;

        // --- Step 1: Calculate TDS ---
        const tdsAmount = (totalAmount * tdsPercent) / 100;
        const afterTDS = totalAmount - tdsAmount;

        // --- Step 2: Add Tax if checkbox checked ---
        let payableAmount = includeTax ? afterTDS + taxAmount : afterTDS;

        // --- Step 3: Deduct selected credit notes ---
        const creditCheckboxes = document.querySelectorAll('.credit-note-checkbox:checked');
        let selectedNotes = [];

        creditCheckboxes.forEach(cb => {
            const creditValue = parseFloat(cb.dataset.credit) || 0;
            payableAmount -= creditValue;

            // Track selected credit note for backend (if needed)
            selectedNotes.push(creditValue);
        });

        // Store selected notes in hidden field
        document.getElementById('selected_credit_notes').value = JSON.stringify(selectedNotes);

        // --- Step 4: Update fields ---
        document.getElementById('tds_amount').value = tdsAmount.toFixed(2);
        document.getElementById('after_tds').value = afterTDS.toFixed(2);
        document.getElementById('paid_amount').value = payableAmount.toFixed(2);
    }
    document.addEventListener('DOMContentLoaded', function() {
        calculateTDS();
    });
    
</script>

@endsection