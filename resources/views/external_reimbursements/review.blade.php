<div class="container py-4">
    <div style="position: fixed; top: 0; background: white; width:100%; z-index: 1000; display: flex; align-items: center; justify-content: space-between; padding: 1rem 0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div style="flex-shrink: 0;">
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" style="height: 55px;">
        </div>
    </div>
</div>

<div style="padding-top: 1.5rem; padding-bottom: 1.5rem; margin-top:70px;">
    <h2 style="margin-bottom: 0.5rem; font-size: 25px; text-align: center;">External Reimbursement</h2>

    <form method="GET" action="{{ url()->current() }}" style=" text-align: right;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by EMP ID or Name"
            style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
        <button type="submit" style="padding: 0.5rem 1rem; cursor: pointer; background-color: #198754; color: white; border: none; border-radius: 4px;">
            Search
        </button>
        <a href="{{ route('reimbursements.export') }}"
            style="padding: 0.5rem 1rem; background-color: #198754; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 1rem;">
            Export CSV
        </a>
        <a href="{{ route('external-bank-details.export') }}"
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
        <a href="{{ route('external-reimbursement.send-emails') }}"
            onclick="return confirm('Are you sure you want to send email for approved reimbursements ?');"
            style="padding: 0.5rem 1rem; background-color: #0d6efd; color: white; border: none; border-radius: 4px; text-decoration: none; margin-left: 1rem;">
            Send Email
        </a>
    </form>
    <form method="POST" action="{{ route('reimbursements.bulk-approve') }}">
        @csrf
        <div style="overflow-x: auto;">
            @php
            use Illuminate\Support\Facades\Request;
            @endphp
            @if (Request::is('cfo/approval'))
            <div style="margin-bottom: 10px; text-align: left;">
                <button onclick="return confirm('Are you sure you want to approve all?');" type="submit" style="padding: 0.5rem 1rem; cursor: pointer; background-color: #198754; color: white; border: none; border-radius: 4px;">
                    Approve Selected
                </button>
            </div>
            @endif
            <table style="width: 100%; border-collapse: collapse; table-layout: auto; text-align: center;">
                <thead style="background-color: #343a40; color: white;">
                    <tr>
                        @if (Request::is('cfo/approval'))
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            <input type="checkbox" id="select-all" required>
                        </th>
                        @endif
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Sr.No</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Name</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Manager</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Emp ID</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Client</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Project</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Submitted</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Total Amount</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem; min-width: 200px;">Reimbursement Items</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Status</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Remarks</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Attachment</th>
                        <th style="border: 1px solid #dee2e6; padding: 0.75rem;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reimbursements as $reimbursement)
                    <tr>
                        @if (Request::is('cfo/approval'))
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            @if(!in_array($reimbursement->status, ['processed', 'rejected', 'cfo_approved']))
                            <input type="checkbox" name="reimbursement_ids[]" value="{{ $reimbursement->id }}">
                            @endif
                        </td>
                        @endif
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->id }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->name }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->manager_name }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->emp_id }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->client ?? 'N/A'}}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->project ?? 'N/A' }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">{{ $reimbursement->created_at->format('M d, Y h:i A') }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">₹{{ $reimbursement->amount }}</td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem; min-width: 200px;">
                            @php
                            $details = is_array($reimbursement->reimbursement_details)
                            ? $reimbursement->reimbursement_details
                            : json_decode($reimbursement->reimbursement_details, true);

                            $flattened = collect($details)
                            ->flatMap(fn($group) => is_array($group) ? $group : [])
                            ->map(fn($item) => ($item['description'] ?? 'N/A') . ' - ₹' . ($item['amount'] ?? 0));

                            $preview = $flattened->take(2)->implode(', ');
                            $moreExists = $flattened->count() > 2;
                            $tooltipText = $flattened->implode(' | ');
                            @endphp
                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin: auto;" title="{{ $tooltipText }}">
                                {{ $preview }}{{ $moreExists ? ' ...' : '' }}
                            </div>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            @php
                            $statusColors = [
                            'pending' => '#6c757d',
                            'finance_approved' => '#0d6efd',
                            'cfo_approved' => '#198754',
                            'rejected' => '#dc3545',
                            'processed' => '#0dcaf0',
                            'chro_approved' => '#ffc107',
                            ];
                            $color = $statusColors[$reimbursement->status] ?? '#212529';
                            @endphp
                            <span style="display: inline-block; padding: 0.35em 0.65em; font-size: 0.6em; font-weight: 400; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.375rem; text-transform: uppercase; background-color: {{ $color }}; color: white;">
                                {{ str_replace('_', ' ', $reimbursement->status) }}
                            </span>
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            @if ($reimbursement->remarks)
                            <span style="font-size: 0.875rem;">{{ $reimbursement->remarks }}</span>
                            @else
                            <span style="color: #6c757d;">N/A</span>
                            @endif
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            @if ($reimbursement->manager_approval_attachment)
                            <a href="{{ asset('/' . $reimbursement->manager_approval_attachment) }}" target="_blank"
                                style="font-size: 0.875rem; padding: 0.25rem 0.5rem; border: 1px solid #0d6efd; color: #0d6efd; border-radius: 0.2rem; text-decoration: none;">Attachment</a>
                            @else
                            <span style="color: #6c757d;">N/A</span>
                            @endif
                        </td>
                        <td style="border: 1px solid #dee2e6; padding: 0.75rem;">
                            <a href="{{ url('external-reimbursements/approve/' . $reimbursement->id) }}"
                                style="font-size: 0.875rem; padding: 0.25rem 0.5rem; background-color: #198754; color: white; border: none; border-radius: 0.2rem; text-decoration: none;">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" style="padding: 1rem; text-align: center; color: #dc3545;">
                            No records found.
                        </td>
                    </tr>
                    @endforelse
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
<!-- <form action="{{ route('bank-details.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit" class="btn btn-primary">Import Bank Details</button>
</form>

@if(session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif -->