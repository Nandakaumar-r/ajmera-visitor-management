<div class="container py-4">
    <div style="position: fixed; top: 0; background: white; width:100%; z-index: 1000; display: flex; align-items: center; justify-content: space-between; padding: 1rem 0;  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Logo on the left -->
        <div style="flex-shrink: 0;">
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" style="height: 55px;">
        </div>

    </div>
</div>

<div id="loading-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(255, 255, 255, 0.8); z-index: 9999; justify-content: center; align-items: center;">
    <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div style="max-width: 1200px; margin: 4rem auto; margin-top:100px; padding: 2rem; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); font-family: Arial, sans-serif; color: #333;">
    <h2 style="margin-bottom: 2rem; font-size: 28px; font-weight: bold; text-align: center; color: #2c3e50;">External Reimbursement Details</h2>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size: 15px; background: #fafafa;">
            <tbody>
                @foreach([
                ['Name' => $reimbursement->name, 'Manager Name' => $reimbursement->manager_name],
                ['Employee ID' => $reimbursement->emp_id, 'Department' => $reimbursement->department],
                ['Designation' => $reimbursement->designation, 'Business Purpose' => $reimbursement->business_purpose],
                ['From' => $reimbursement->from->format('M d, Y'), 'To' => $reimbursement->to->format('M d, Y')],
                ['Total Amount' => '₹' . $reimbursement->amount, 'Status' => $reimbursement->status],
                ['Submitted by' => $reimbursement->submitted_by, 'Approved by' => $reimbursement->approved_by],
                ] as $row)
                <tr style="border-bottom: 1px solid #ddd;">
                    @foreach ($row as $label => $value)
                    <th style="padding: 12px; background-color: #f1f1f1; text-align: left; white-space: nowrap;">{{ $label }}</th>
                    <td style="padding: 12px;">
                        @if ($label === 'Status')
                        @php
                        $statusColors = [
                        'pending' => '#6c757d',
                        'chro_approved' => '#ffc107',
                        'finance_approved' => '#0d6efd',
                        'cfo_approved' => '#198754',
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
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($reimbursement->manager_approval_attachment)
    <div style="margin-bottom: 2rem;">
        <h4 style="font-weight: bold; font-size: 18px;">Attachments</h4>
        <div style="margin-top: 10px;">
            <a href="{{ route('reimbursement.download.attachments', $reimbursement->id) }}" style="margin-right: 10px; padding: 8px 14px; background-color: #ffc107; color: black; border-radius: 6px; text-decoration: none;">📥 Download All</a>
            <a href="{{ asset('/' . $reimbursement->manager_approval_attachment) }}" target="_blank" style="margin-right: 10px; padding: 8px 14px; background-color: #0dcaf0; color: white; border-radius: 6px; text-decoration: none;">👁 View Approval</a>
            {{-- Bills Attachments --}}
            @php
            $bills = is_array($reimbursement->bills_attachment)
            ? $reimbursement->bills_attachment
            : json_decode($reimbursement->bills_attachment, true);
            @endphp

            @if (!empty($bills))
            @foreach ($bills as $index => $bill)
            <a href="{{ asset('/' . $bill) }}" target="_blank" style="margin-right: 10px; padding: 8px 14px; background-color: #0dcaf0; color: white; border-radius: 6px; text-decoration: none;">👁 View Bill {{ $index + 1 }}</a>
            @endforeach
            @else
            <!-- <p class="text-muted font-bold">No bills uploaded.</p> -->
            @endif
        </div>
    </div>
    @endif

    <!-- ✅ Approval Form Buttons -->
    <form action="{{ route('reimbursement.handle.approval', ['id' => $reimbursement->id]) }}" method="POST"  onsubmit="return validateForm(event)" style="margin-top: 3rem;">
        @csrf

        <!-- Remarks Textarea -->
        <div style="margin-bottom: 1.5rem;">
            <label for="remarks" style="display: block; font-weight: bold; margin-bottom: 6px;">Remarks</label>
            <textarea id="rejection_reason" name="remarks" rows="4" placeholder="Add any remarks here..." style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; resize: vertical;">{{ old('remarks', $reimbursement->remarks ?? '') }}</textarea>
            @error('remarks')
            <div style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

         @php
        $isRejected = in_array($reimbursement->status, ['rejected', 'processed']);
        @endphp

        @if (!$isRejected)
        <!-- Buttons -->
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="submit" name="status" value="Approved" style="padding: 12px 20px; background-color: #28a745; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; max-width: 200px;">Approve</button>
            <button type="submit" name="status" value="rejected" onclick="setRejectFlag()" style="padding: 12px 20px; background-color: #dc3545; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; max-width: 200px;">Reject</button>
        </div>
        @endif
    </form>


    @php $details = $reimbursement->reimbursement_details; @endphp
    @foreach (['otherExpenses' => 'Mobile, Internet, Laptop and Others', 'travelExpenses' => 'Travel', 'foodExpenses' => 'Food'] as $key => $label)
    @if (!empty($details[$key]))
    <h4 style="font-size: 20px; font-weight: bold; margin-top: 2.5rem; margin-bottom: 1rem; color: #2c3e50;">{{ $label }} Reimbursement</h4>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size: 14px;">
            <thead style="background-color: #f0f0f0;">
                <tr>
                    @foreach (array_keys($details[$key][0]) as $heading)
                    <th style="padding: 10px; text-align: left; text-transform: capitalize; font-weight: bold;">{{ str_replace('_', ' ', $heading) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($details[$key] as $item)
                <tr style="border-top: 1px solid #e0e0e0;">
                    @foreach ($item as $value)
                    <td style="padding: 10px;">{{ $value ?? 'N/A' }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endforeach

</div>
<script>
    let clickedButton = null;

    document.addEventListener("DOMContentLoaded", function () {
        // Track which button was clicked
        document.querySelectorAll('form button[type="submit"]').forEach(button => {
            button.addEventListener('click', function () {
                clickedButton = this;
            });
        });
    });

    function validateForm(event) {
        const remarks = document.getElementById('rejection_reason').value.trim();
        const isRejecting = clickedButton && clickedButton.value === 'rejected';

        if (isRejecting && remarks === '') {
            alert('Please provide a rejection reason when rejecting.');
            event.preventDefault();
            return false;
        }

        // Show loading only if validation passes
        document.getElementById('loading-overlay').style.display = 'flex';
        return true;
    }
</script>