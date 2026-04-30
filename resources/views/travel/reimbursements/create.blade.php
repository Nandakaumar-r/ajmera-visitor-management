@extends('layouts.app')

@section('content')
<div class="container mt-16">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Submit Travel Reimbursement</h4>
        </div>
        <div class="card-body">

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the following issues:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="uploadForm" action="{{ route('travel.reimbursements.tableCreation') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <!-- Receipts Upload -->
                <div class="mb-3">
                    <label for="receipts" class="form-label fw-bold">Upload Receipts</label>
                    <input type="file" name="receipts[]" id="receipts"
                        class="form-control @error('receipts.*') is-invalid @enderror" multiple
                        accept=".pdf, .jpg, .jpeg, .png">
                    <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG. Max size: 10MB each.</small>
                    @error('receipts.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 d-flex gap-3 flex-wrap">
                    <!-- Company Selection Dropdown -->
                    <div class="flex-fill">
                        <label for="company" class="form-label fw-bold">Select Company</label>
                        <select name="company" id="companyField" class="form-control @error('company') is-invalid @enderror" required>
                            <option value="" disabled selected>Select your Company</option>
                            <option value="Fidelis Technology Services Private Limited">Fidelis Technology Services Private Limited</option>
                            <option value="Fidelis Business Services Private Limited">Fidelis Business Services Private Limited</option>
                            <option value="Paylink Financial Services Private Limited">Paylink Financial Services Private Limited</option>
                            <option value="Fidelis Technologies LLC - (Dubai & Saudi)">Fidelis Technologies LLC - (Dubai & Saudi)</option>
                            <option value="Fidelis Technologies PTE LTD - Singapore">Fidelis Technologies PTE LTD - Singapore</option>
                            <option value="Incube Information Technology Consultancy LLC">Incube Information Technology Consultancy LLC</option>
                            <option value="Aseuro Technologies Private Limited">Aseuro Technologies Private Limited</option>
                            <option value="SunSmart Technologies Private Limited">SunSmart Technologies Private Limited</option>
                        </select>
                        @error('company')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Manager Email -->
                    <div class="flex-fill">
                        <label for="manager_email" class="form-label fw-bold">Manager Email</label>
                        <input type="email" name="manager_email" id="managerEmailField"
                            class="form-control @error('manager_email') is-invalid @enderror"
                            placeholder="Please Enter Manager Email ID" required>
                        @error('manager_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Excel Upload -->
                <div class="mb-3">
                    <label for="reimbursement_excel" class="form-label fw-bold">Reimbursement Excel Sheet (.xlsx or .xls)</label>
                    <input type="file" name="reimbursement_excel" id="reimbursement_excel"
                        class="form-control @error('reimbursement_excel') is-invalid @enderror"
                        accept=".xls,.xlsx">
                    @error('reimbursement_excel')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Accepted formats: XLS, XLSX only.</small>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success" id="processButton">
                        Process
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- Reimbursement Table --}}
    <div class="card shadow mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Reimbursement Details</h5>
        </div>
        <div class="card-body">
            <table id="resultsTable" class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Expense Description</th>
                        <th>Event</th>
                        <th>Bills</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Total Reimbursement Amount</td>
                        <td id="totalAmount" class="fw-bold"> 0.00</td>
                    </tr>
                </tfoot>
            </table>

            <button type="button" class="btn btn-success mt-3" id="submitReimbursement">
                Submit
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        console.log('✅ Form handler attached');

        $('#uploadForm').submit(function(e) {
            e.preventDefault();
            console.log('✅ AJAX submit triggered');

            const form = $(this);
            const submitButton = form.find('#processButton');
            const originalText = submitButton.html();

            submitButton.prop('disabled', true).html('Processing... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

            const formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log('✅ AJAX response:', response);

                    let combinedData = [];

                    if (response.receipts && response.receipts.length > 0) {
                        combinedData = combinedData.concat(response.receipts);
                    }

                    if (response.excel && response.excel.raw) {
                        const rows = response.excel.raw;
                        let i = 0;
                        while (i < rows.length) {
                            const row = rows[i];
                            if (row[0] && typeof row[0] === 'string' && row[0].trim().toLowerCase() === 'date') {
                                i++;
                                while (i < rows.length) {
                                    const dataRow = rows[i];
                                    const isEmpty = dataRow.every(cell => !cell);
                                    const isNewBlock = typeof dataRow[0] === 'string' && dataRow[0].toLowerCase().includes('reimbursement');
                                    if (isEmpty || isNewBlock) break;

                                    let excelDate = dataRow[0];
                                    let formattedDate = '';
                                    if (typeof excelDate === 'number') {
                                        const epoch = new Date((excelDate - 25569) * 86400 * 1000);
                                        formattedDate = epoch.toISOString().split('T')[0];
                                    }

                                    const description = dataRow[2] || '';
                                    const event = dataRow[9] || '';
                                    const amount = dataRow[12] || 0;

                                    if (description && amount) {
                                        combinedData.push({
                                            type: 'excel',
                                            date: formattedDate,
                                            description: description,
                                            event: event,
                                            amount: amount,
                                            currency: 'INR',
                                            filePath: ''
                                        });
                                    }

                                    i++;
                                }
                            } else {
                                i++;
                            }
                        }
                    }

                    console.log('✅ Final Combined Data:', combinedData);

                    const tbody = $('#resultsTable tbody');
                    tbody.empty();
                    let totalAmount = 0;

                    combinedData.forEach(item => {
                        totalAmount += parseFloat(item.amount);
                        tbody.append(`
                        <tr>
                            <td>${item.date || ''}</td>
                            <td>${item.description}</td>
                            <td>${item.event || ''}</td>
                            <td>${item.filePath ? `<a href="{{ asset('') }}${item.filePath}" target="_blank">View</a>` : 'N/A'}</td>
                            <td>
                                <select class="form-select form-select-sm currency">
                                    <option value="INR" ${item.currency === 'INR' ? 'selected' : ''}>INR</option>
                                    <option value="AED" ${item.currency === 'AED' ? 'selected' : ''}>AED</option>
                                </select>
                            </td>
                            <td> ${item.amount}</td>
                            <td>
                                <button class="btn btn-sm btn-primary editRow">Edit</button>
                                <button class="btn btn-sm btn-danger deleteRow">Remove</button>
                            </td>
                        </tr>
                    `);
                    });

                    $('#totalAmount').text(` ${totalAmount.toFixed(2)}`);
                },
                error: function(error) {
                    console.error('❌ AJAX error:', error);
                    alert('An error occurred during upload.');
                },
                complete: function() {
                    submitButton.prop('disabled', false).html(originalText);
                }
            });
        });

        // Remove row handler
        $('#resultsTable').on('click', '.deleteRow', function() {
            $(this).closest('tr').remove();
            updateTotal();
        });

        // Edit row handler
        $('#resultsTable').on('click', '.editRow', function() {
            const row = $(this).closest('tr');
            const cells = row.find('td');

            const currentCurrency = cells.eq(4).find('select').val();
            const amountValue = cells.eq(5).text().replace(/[,\s]/g, '');

            cells.eq(0).html(`<input type="date" class="form-control" value="${cells.eq(0).text().trim()}">`);
            cells.eq(1).html(`<input type="text" class="form-control" value="${cells.eq(1).text().trim()}">`);
            cells.eq(2).html(`<input type="text" class="form-control" value="${cells.eq(2).text().trim()}">`);

            // currency input
            cells.eq(4).html(`
        <select class="form-select form-select-sm currency">
            <option value="INR" ${currentCurrency === 'INR' ? 'selected' : ''}>INR</option>
            <option value="AED" ${currentCurrency === 'AED' ? 'selected' : ''}>AED</option>
        </select>
    `);

            // amount input
            cells.eq(5).html(`<input type="number" step="0.01" class="form-control" value="${amountValue}">`);

            cells.eq(6).html(`
        <button class="btn btn-sm btn-success saveRow">Save</button>
        <button class="btn btn-sm btn-secondary cancelEdit">Cancel</button>
    `);
        });

        // Save row handler
        $('#resultsTable').on('click', '.saveRow', function() {
            const row = $(this).closest('tr');
            const cells = row.find('td');

            const newCurrency = cells.eq(4).find('select').val();
            const newAmount = parseFloat(cells.eq(5).find('input').val() || 0).toFixed(2);

            cells.eq(0).text(cells.eq(0).find('input').val());
            cells.eq(1).text(cells.eq(1).find('input').val());
            cells.eq(2).text(cells.eq(2).find('input').val());

            cells.eq(4).html(`
        <select class="form-select form-select-sm currency">
            <option value="INR" ${newCurrency === 'INR' ? 'selected' : ''}>INR</option>
            <option value="AED" ${newCurrency === 'AED' ? 'selected' : ''}>AED</option>
        </select>
    `);

            cells.eq(5).text(` ${newAmount}`);

            cells.eq(6).html(`
        <button class="btn btn-sm btn-primary editRow">Edit</button>
        <button class="btn btn-sm btn-danger deleteRow">Remove</button>
    `);

            updateTotal();
        });


        // Cancel edit handler
        $('#resultsTable').on('click', '.cancelEdit', function() {

            const row = $(this).closest('tr');
            const cells = row.find('td');

            const date = cells.eq(0).find('input').attr('value');
            const description = cells.eq(1).find('input').attr('value');
            const event = cells.eq(2).find('input').attr('value');

            const currency = cells.eq(4).find('select').attr('value');
            const amount = parseFloat(cells.eq(5).find('input').attr('value')).toFixed(2);

            // restore original values
            cells.eq(0).text(date);
            cells.eq(1).text(description);
            cells.eq(2).text(event);

            // restore currency dropdown
            cells.eq(4).html(`
        <select class="form-select form-select-sm currency">
            <option value="INR" ${currency === 'INR' ? 'selected' : ''}>INR</option>
            <option value="AED" ${currency === 'AED' ? 'selected' : ''}>AED</option>
        </select>
    `);

            // restore amount
            cells.eq(5).text(`₹ ${amount}`);

            // restore buttons
            cells.eq(6).html(`
        <button class="btn btn-sm btn-primary editRow">Edit</button>
        <button class="btn btn-sm btn-danger deleteRow">Remove</button>
    `);
        });


        // Recalculate total
        function updateTotal() {
            let newTotal = 0;
            $('#resultsTable tbody tr').each(function() {
                newTotal += parseFloat($(this).find('td').eq(5).text().replace(/[,\s]/g, '') || 0);
            });
            $('#totalAmount').text(` ${newTotal.toFixed(2)}`);
        }

        // Submit handler
        $('#submitReimbursement').click(function() {
            const $submitBtn = $(this);
            const originalBtnText = $submitBtn.text();

            const rows = $('#resultsTable tbody tr');
            const reimbursementData = [];
            const managerEmail = $('#managerEmailField').val();
            const companyValue = $('#companyField').val();

            if (rows.length === 0) {
                alert('⚠️ Please process and add at least one reimbursement item before submitting.');
                return;
            }

            // Disable button and show loading text
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...');

            rows.each(function() {
                const cells = $(this).find('td');
                reimbursementData.push({
                    date: cells.eq(0).text().trim(),
                    description: cells.eq(1).text().trim(),
                    event: cells.eq(2).text().trim(),
                    bill: (cells.eq(3).find('a').attr('href') || ''),
                     currency: cells.eq(4).find('select').val() || 'INR',
                    amount: parseFloat(cells.eq(5).text().replace(/[,]/g, '').trim()),
                    manager_email: managerEmail,
                    company: companyValue

                });
            });

            const formData = new FormData();
            formData.append('reimbursementData', JSON.stringify(reimbursementData));
            formData.append('manager_email', managerEmail);
            formData.append('company', companyValue);

            fetch(`{{ route('travel.reimbursements.store') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error('❌ Submit error:', error);
                    alert('Failed to submit reimbursement data.');
                })
                .finally(() => {
                    // Re-enable the button and restore original text
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                });
        });

    });
</script>

@endpush