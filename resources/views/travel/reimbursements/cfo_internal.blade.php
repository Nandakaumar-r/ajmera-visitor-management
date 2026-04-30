<div class="container py-4">
    <div style="position: fixed; top: 0; background: white; width:100%; z-index: 1000; display: flex; align-items: center; justify-content: space-between; padding: 1rem 0;  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Logo on the left -->
        <div style="flex-shrink: 0;">
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" style="height: 55px;">
        </div>

    </div>
</div>

<div style="padding-top: 1.5rem; padding-bottom: 1.5rem; margin-top: 70px;">
    <h2 style="margin-bottom: 0.3rem; text-align: center;">Internal Reimbursement</h2>

    <form method="GET" action="{{ url()->current() }}" style="margin-bottom: 1.5rem; text-align: right;">
        <label style="font-weight: 500px; font-size: 20px;" for="month_filter">Filter by Month:</label>
        <select name="month_filter" style=" padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" id="month_filter" class="form-control" onchange="this.form.submit()">
            <option value="current" {{ $monthFilter == 'current' ? 'selected' : '' }}>Current Month</option>
            <option value="last_1" {{ $monthFilter == 'last_1' ? 'selected' : '' }}>Last Month</option>
            <option value="last_2" {{ $monthFilter == 'last_2' ? 'selected' : '' }}>Last 2 Months</option>
            <option value="last_3" {{ $monthFilter == 'last_3' ? 'selected' : '' }}>Last 3 Months</option>
            <option value="all" {{ $monthFilter == 'all' ? 'selected' : '' }}>All Months</option>
        </select>
        <input type="text" name="emp_id" value="{{ request('emp_id') }}" placeholder="Search by EMP ID or Name"
            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
        <button type="submit" style="padding: 0.5rem 1rem; background-color: #198754; color: white; border: none; border-radius: 4px;">
            Search
        </button>
        <a href="{{ route('internal-reimbursements.export', request()->query()) }}"
            style="padding: 0.5rem 1rem; background-color: #198754; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 1rem;">
            Export
        </a>
        <a href="{{ route('bank-details.export') }}"
            style="padding: 0.5rem 1rem; background-color: #198754; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 1rem;">
            Export CSV with Bank Details
        </a>
        @if (session('success'))
        <div style="width: 40%; margin: 20px auto; padding: 12px 20px; background-color: #d4edda; color: #155724; border-radius: 6px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-size: 15px;">
            {{ session('success') }}
        </div>
        @endif

        @if (session('info'))
        <div style="margin: 10px 0; padding: 10px; background-color: #cce5ff; color: #004085; border-radius: 4px; text-align: center;">
            {{ session('info') }}
        </div>
        @endif

        @if (session('error'))
        <div style="margin: 10px 0; padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 4px; text-align: center;">
            {{ session('error') }}
        </div>
        @endif

        <!-- 👇 Send Emails Button -->
        <a href="{{ route('reimbursement.send-all-pending-emails') }}"
            onclick="return confirm('Are you sure you want to send email for approved reimbursements ?');"
            style="padding: 0.5rem 1rem; background-color: #0d6efd; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 1rem;">
            Send Email
        </a>
    </form>
    <form method="POST" action="{{ route('internal-reimbursements.bulk-approve') }}">
        @csrf
        <div style="overflow-x: auto;">
            @php
            use Illuminate\Support\Facades\Request;
            @endphp
            @if (Request::is('travel/reimbursements/cfo'))
            <div style="margin-bottom: 10px; text-align: left;">
                <button type="submit" style="padding: 0.5rem 1rem; cursor: pointer; background-color: #198754; color: white; border: none; border-radius: 4px;">
                    Approve Selected
                </button>
            </div>
            @endif
            <table style="width: 100%; border-collapse: collapse; table-layout: auto; text-align: center;">
                <thead style="background-color: #343a40; color: white;">
                    <tr>
                        @if (Request::is('travel/reimbursements/cfo'))
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            <input type="checkbox" id="select-all" required>
                        </th>
                        @endif
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Sr.No</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Name</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Manager</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Emp ID</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Company</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Total Amount</th>
                        <!-- <th style="border: 1px solid #dee2e6; padding: 0.75rem; min-width: 200px;">Details</th> -->
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Submitted</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Last Update</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Status</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Remarks</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reimbursements as $reimbursement)
                    <tr>
                        @if (Request::is('travel/reimbursements/cfo'))
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            @if(!in_array($reimbursement->status, ['processed', 'rejected', 'cfo_approved']))
                            <input type="checkbox" name="reimbursement_ids[]" value="{{ $reimbursement->id }}">
                            @endif
                        </td>
                        @endif
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->id }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->user->name }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->user->employee->manager->manager_name ?? 'N/A' }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->user->employee->employee_id ?? 'N/A' }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->company ?? 'N/A' }}</td>
                        @php
                        $details = json_decode($reimbursement->details, true);

                        $currencies = collect($details)->pluck('currency')->unique()->values();

                        if ($currencies->count() === 1) {
                        $currency = $currencies[0];
                        } else {
                        $currency = 'Multiple';
                        }
                        @endphp

                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            {{ $currency }} {{ number_format($reimbursement->amount, 2) }}
                        </td>

                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->created_at->format('M d, Y h:i A') }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->updated_at->format('M d, Y h:i A') }}</td>
                        <!-- <td style="border: 1px solid #dee2e6; padding: 0.75rem; min-width: 200px;">
                        @php
                        $details = is_array($reimbursement->details)
                        ? $reimbursement->details
                        : json_decode($reimbursement->details, true);
                        @endphp

                        @if (!empty($details))
                            <ul class="mb-0 ps-3" style="list-style-type: none; padding-left: 20px;">
                                @foreach ($details as $item)
                                <li class="mb-2" style="margin-bottom: 10px; font-size: 14px;">
                                    <strong>Date:</strong> {{ $item['date'] ?? 'N/A' }}<br>
                                    <strong>Type:</strong> {{ ucfirst($item['type'] ?? 'N/A') }}<br>
                                    <strong>Amount:</strong> ₹{{ $item['amount'] ?? 0 }}<br>
                                    <strong>Description:</strong> {{ $item['description'] ?? 'N/A' }}<br>
                                    @if (!empty($item['bill']))
                                    <strong>Bill:</strong>
                                    <a href="{{ asset($item['bill']) }}" target="_blank" style="color: #007bff; text-decoration: none;">View</a>
                                    @endif
                                    <hr style="border-top: 1px solid #dee2e6;">
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <span class="text-muted" style="font-size: 14px;">No details available</span>
                            @endif

                       
                    </td> -->
                        <!-- <td class="text-start" style="max-width: 300px; overflow: hidden;">
                        <div style="max-height: 200px; overflow-y: auto;">
                            @php
                            $details = is_array($reimbursement->details) ? $reimbursement->details : json_decode($reimbursement->details, true);
                            @endphp

                            @if (!empty($details))
                            <ul class="mb-0 ps-3" style="list-style-type: none; padding-left: 20px;">
                                @foreach ($details as $item)
                                <li class="mb-2" style="margin-bottom: 10px; font-size: 14px;">
                                    <strong>Date:</strong> {{ $item['date'] ?? 'N/A' }}<br>
                                    <strong>Type:</strong> {{ ucfirst($item['type'] ?? 'N/A') }}<br>
                                    <strong>Amount:</strong> ₹{{ $item['amount'] ?? 0 }}<br>
                                    <strong>Description:</strong> {{ $item['description'] ?? 'N/A' }}<br>
                                    @if (!empty($item['bill']))
                                    <strong>Bill:</strong>
                                    <a href="{{ asset($item['bill']) }}" target="_blank" style="margin-right: 3px; padding: 2px 5px; background-color: #0dcaf0; color: white; border-radius: 6px; text-decoration: none;">👁 View Bill</a>
                                    @endif
                                    <hr style="border-top: 1px solid #dee2e6;">
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <span class="text-muted" style="font-size: 14px;">No details available</span>
                            @endif
                        </div>
                    </td> -->
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            @php
                            $statusColors = [
                            'pending' => '#6c757d',
                            'manager_approved' => '#0d6efd',
                            'hr_approved' => '#ffc107',
                            'accountant_approved' => '#17a2b8',
                            'cfo_approved' => '#198754',
                            'hold' => '#fd7e14',
                            'rejected' => '#dc3545',
                            'processed' => '#0dcaf0',

                            ];
                            $color = $statusColors[$reimbursement->status] ?? '#212529';
                            @endphp

                            <span style="display: inline-block; padding: 0.35em 0.65em; font-size: 0.6em; font-weight: 400; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.375rem; text-transform: uppercase; background-color: {{ $color }}; color: white;">
                                {{ str_replace('_', ' ', $reimbursement->status) }}
                            </span>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->rejection_reason ?? 'N/A' }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            <a href="{{ url('internal-reimbursements/approve/' . $reimbursement->id) }}" style="font-size: 0.875rem; padding: 0.25rem 0.5rem; background-color: #198754; color: white; border: none; border-radius: 0.2rem; text-decoration: none;">View</a>
                        </td>
                    </tr>
                    @endforeach
                    @if ($reimbursements->count() == 0)
                    <tr>
                        <td colspan="11" style="padding: 1rem; text-align: center; color: #dc3545;">
                            No records found.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>

            <div style="margin-top: 1.5rem; text-align: center;">
                <div style="display: inline-flex; gap: 0.5rem; align-items: center; font-size: 14px;">
                    {!! $reimbursements->onEachSide(1)->links('vendor.pagination.custom') !!}
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    document.getElementById('select-all').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('input[name="reimbursement_ids[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });
</script>