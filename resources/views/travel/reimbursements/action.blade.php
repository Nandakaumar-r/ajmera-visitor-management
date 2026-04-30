<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reimbursement Action</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div id="loading-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.8); z-index: 9999; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg w-100" style="max-width: 600px;">
            <div class="card-header text-center bg-primary text-white">
                <h4 class="mb-0">Reimbursement Request Details</h4>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex gap-3 flex-wrap">
                    <p><strong>Name:</strong> {{ $reimbursements->user->name }}</p>
                    <p><strong>Company:</strong> {{ $reimbursements->company }}</p>
                </div>
                <div class="mb-3 d-flex gap-3 flex-wrap">
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($reimbursements->created_at)->format('d M Y h:i A') }}</p>
                    @php
                    $status = $reimbursements->status;
                    $statusColors = [
                    'pending' => 'secondary',
                    'manager_approved' => 'primary',
                    'hr_approved' => 'info',
                    'accountant_approved' => 'dark',
                    'cfo_approved' => 'warning',
                    'rejected' => 'danger',
                    'processed' => 'success',
                    ];
                    $badgeColor = $statusColors[$status] ?? 'secondary';
                    @endphp

                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $badgeColor }} text-uppercase">{{ str_replace('_', ' ', $status) }}</span>
                    </p>
                </div>
                <!-- <p><strong>Rejection Reason:</strong> {{ $reimbursements->rejection_reason }}</p> -->
                @if(is_array(json_decode($reimbursements->details, true)))
                @php $details = json_decode($reimbursements->details, true); @endphp
                <h5 class="mt-4">Expense Details</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mt-2">
                        <thead class="table-primary">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Event</th>
                                <th>Currency</th>
                                <th>Amount</th>
                                <th>Bill</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details as $item)
                            <tr>
                                <td>{{ $item['date'] ?? '-' }}</td>
                                <td>{{ $item['description'] ?? 'N/A' }}</td>
                                <td>{{ $item['event'] ?? 'N/A' }}</td>
                                <td>{{ $item['currency'] ?? 'INR' }}</td>
                                <td>{{ number_format($item['amount'] ?? 0, 2) }}</td>
                                <td>
                                    @if(!empty($item['bill']))
                                    <a href="{{ asset($item['bill']) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                    @else
                                    N/A
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p><strong>Description:</strong> {{ $reimbursements->details }}</p>
                @endif

                @php
                $details = json_decode($reimbursements->details, true);
                $totalAmount = is_array($details) ? array_sum(array_column($details, 'amount')) : 0;
                @endphp

                <p><strong>Total Amount:</strong> {{ number_format($totalAmount, 2) }}</p>

                @if (
                !$reimbursements->manager_id &&
                $reimbursements->status !== 'rejected' &&
                $reimbursements->status !== 'processed' &&
                $reimbursements->status !== 'manager_approved' &&
                $reimbursements->status !== 'accountant_approved' &&
                $reimbursements->status !== 'cfo_approved' &&
                $reimbursements->status !== 'hr_approved' &&
                !session('success') && !session('status')
                )
                {{-- Form for manager if not yet acted --}}
                <form action="{{ url('/reimbursement/action/' . $reimbursements->id) }}" method="POST" class="mt-4" onsubmit="return validateForm(event)">
                    @csrf
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Comments</label>
                        <textarea class="form-control" name="rejection_reason" placeholder="Add any remarks here..." id="rejection_reason" rows="3"></textarea>
                    </div>
                    <div class="d-flex justify-content-between gap-3">
                        <button type="submit" name="status" value="approved" class="btn btn-success w-50">✅ Approve</button>
                        <button type="submit" name="status" value="rejected" onclick="setRejectFlag()" class="btn btn-danger w-50">❌ Reject</button>
                    </div>
                </form>
                @endif

                {{-- ✅✅ AFTER ACTION: Show success message --}}
                @if (session('status') || session('success'))
                <div class="alert alert-success mt-3" role="alert">
                    {{ session('status') ?? session('success') }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                console.log('Form submitted!');
                document.getElementById('loading-overlay').style.display = 'flex';
            });
        });
    </script> -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>