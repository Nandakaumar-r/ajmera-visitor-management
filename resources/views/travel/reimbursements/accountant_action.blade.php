<div class="container py-4">
    <div style="position: fixed; top: 0; background: white; width:100%; z-index: 1000; display: flex; align-items: center; justify-content: space-between; padding: 1rem 0;  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Logo on the left -->
        <div style="flex-shrink: 0;">
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" style="height: 55px;">
        </div>

    </div>
</div>
<div id="loading-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="color:white; font-size:1.5rem;">Loading...</div>
</div>

<div style="max-width: 1200px; margin: 4rem auto; margin-top: 100px; padding: 2rem; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); font-family: Arial, sans-serif; color: #333;">
    <h2 style="margin-bottom: 2rem; font-size: 28px; font-weight: bold; text-align: center; color: #2c3e50;">Internal Reimbursement Details</h2>

    {{-- Reimbursement Details Table --}}
    @php
    $expenses = json_decode($reimbursements->details, true);
    @endphp

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size: 15px; background: #fafafa;">
        <tbody>
            @php
            $details = json_decode($reimbursements->details, true);

            $currencies = collect($details)->pluck('currency')->unique()->values();

            if ($currencies->count() === 1) {
            $currency = $currencies[0];
            } else {
            $currency = 'Multiple';
            }
             $totalAmountDisplay = $currency . ' ' . number_format($reimbursements->amount, 2);
            @endphp
            @foreach([
            'Name' => $reimbursements->user->name ?? 'N/A',
            'Manager' => $reimbursements->user->employee->manager->manager_name ?? 'N/A',
            'Emp ID' => $reimbursements->user->employee->employee_id ?? 'N/A',
            'Company' => $reimbursements->company ?? 'N/A',
            'Total Amount' => $totalAmountDisplay,
            'Status' => $reimbursements->status,
            'Previous Status' => $reimbursements->previous_status ?? null,
            'Remarks' => $reimbursements->rejection_reason ?? 'N/A'
            ] as $label => $value)
            @if($label === 'Previous Status' && !$value)
                @continue
            @endif
            <tr style="border-bottom: 1px solid #ddd;">
                <th style="padding: 12px; background-color: #f1f1f1; text-align: left; white-space: nowrap;">{{ $label }}</th>
                <td style="padding: 12px;">
                    @if ($label === 'Status' || $label === 'Previous Status')
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
                    $color = $statusColors[$value] ?? '#343a40';
                    @endphp
                    <span style="padding: 6px 12px; border-radius: 4px; background-color: {{ $color }}; color: white; text-transform: uppercase; font-size: 13px;">
                        {{ str_replace('_', ' ', $value) }}
                    </span>
                    @else
                    {{ $value }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-bottom: 15px;">
        <a href="{{ route('internal-reimbursement.download.bills', $reimbursements->id) }}" style="margin-right: 10px; padding: 8px 14px; background-color: #ffc107; color: black; border-radius: 6px; text-decoration: none;">📥 Download All Bills</a>
    </div>


    {{-- Approval Form --}}
    <form action="{{ route('internal_handle', ['id' => $reimbursements->id]) }}" method="POST" onsubmit="return validateForm(event)">
        @csrf

        {{-- Preserve previous URL with filters (e.g. ?month_filter=last_3&page=3) --}}
        <input type="hidden" name="redirect_url" value="{{ url()->previous() }}">

        <div style="margin-bottom: 1.5rem;">
            <label for="rejection_reason" style="display: block; font-weight: bold; margin-bottom: 6px;">Remarks</label>
            <textarea id="rejection_reason" name="rejection_reason" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; resize: vertical;">{{ old('remarks', $reimbursements->rejection_reason ?? '') }}</textarea>
            @error('remarks')
            <div style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        @php
        $isRejected = in_array($reimbursements->status, ['rejected', 'processed', 'accountant_approved', 'cfo_approved']);
        @endphp

        @if (!$isRejected)
            @if($reimbursements->status === 'hold')
            {{-- Show Release Hold button when on hold --}}
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit" name="status" value="release_hold" style="padding: 12px 20px; background-color: #17a2b8; color: white; border: none; cursor: pointer; border-radius: 8px; font-weight: bold;">🔓 Release Hold</button>
                <button type="submit" name="status" value="rejected" onclick="setRejectFlag()" style="padding: 12px 20px; background-color: #dc3545; color: white; border: none; cursor: pointer; border-radius: 8px; font-weight: bold;">❌ Reject</button>
            </div>
            @else
            {{-- Show normal buttons when not on hold --}}
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit" name="status" value="approved" style="padding: 12px 20px; background-color: #28a745; color: white; border: none; cursor: pointer; border-radius: 8px; font-weight: bold;">✅ Approve</button>
                <button type="submit" name="status" value="hold" style="padding: 12px 20px; background-color: #ffc107; color: black; border: none; cursor: pointer; border-radius: 8px; font-weight: bold;">⏸️ Hold</button>
                <button type="submit" name="status" value="rejected" onclick="setRejectFlag()" style="padding: 12px 20px; background-color: #dc3545; color: white; border: none; cursor: pointer; border-radius: 8px; font-weight: bold;">❌ Reject</button>
            </div>
            @endif
        @endif

    </form>


    {{-- Expense Items Table --}}
    <h3 style="margin-bottom: 1rem;">Expense Details</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 3rem; font-size: 14px; background: #fff; border: 1px solid #ddd;">
        <thead>
            <tr style="background-color: #f1f1f1;">
                <th style="padding: 10px; text-align: left;">Date</th>
                <th style="padding: 10px; text-align: left;">Type</th>
                <th style="padding: 10px; text-align: left;">Description</th>
                <th style="padding: 10px; text-align: left;">Event</th>
                <th style="padding: 10px; text-align: left;">Amount</th>
                <th style="padding: 10px; text-align: left;">Bill</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
            <tr>
                <td style="padding: 10px;">{{ $expense['date'] }}</td>
                <td style="padding: 10px; text-transform: capitalize;">{{ $expense['type'] ?? 'N/A' }}</td>
                <td style="padding: 10px;">{{ $expense['description'] }}</td>
                <td style="padding: 10px;">{{ $expense['event'] ?? 'N/A' }}</td>
                @php
                $details = json_decode($reimbursements->details, true);

                $currencies = collect($details)->pluck('currency')->unique()->values();

                if ($currencies->count() === 1) {
                $currency = $currencies[0];
                } else {
                $currency = 'Multiple';
                }
                @endphp
                <td style="padding: 10px;"> {{ $currency }} {{ number_format($expense['amount'], 2) }}</td>
                <td style="padding: 10px;">
                    @if(!empty($expense['bill']))
                    <a href="{{ asset($expense['bill']) }}" target="_blank"
                        style="margin-right: 3px; padding: 2px 5px; background-color: #0dcaf0; color: white; border-radius: 6px; text-decoration: none;">
                        👁 View Bill
                    </a>
                    @else
                    N/A
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    let clickedButton = null;

    document.addEventListener("DOMContentLoaded", function() {
        // Track which button was clicked
        document.querySelectorAll('form button[type="submit"]').forEach(button => {
            button.addEventListener('click', function() {
                clickedButton = this;
            });
        });
    });

    function validateForm(event) {
        const remarks = document.getElementById('rejection_reason').value.trim();
        const isRejecting = clickedButton && clickedButton.value === 'rejected';
        const isHolding = clickedButton && clickedButton.value === 'hold';

        if (isRejecting && remarks === '') {
            alert('Remarks are required when rejecting.');
            event.preventDefault();
            return false;
        }

        if (isHolding && remarks === '') {
            alert('Remarks are required when putting on hold.');
            event.preventDefault();
            return false;
        }

        // Show loading only if validation passes
        document.getElementById('loading-overlay').style.display = 'flex';
        return true;
    }
</script>
