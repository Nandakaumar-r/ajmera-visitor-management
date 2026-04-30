@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">All External Reimbursements</h2>

    {{-- Search form --}}
    <form method="GET" action="{{ route('external-reimbursements.index') }}" class="mb-3 d-flex justify-content-end">
        <div class="input-group" style="max-width: 300px;">
            <input type="text" name="emp_id" class="form-control" placeholder="Search by Emp ID" value="{{ request('emp_id') }}" disabled>
            <button class="btn btn-primary" type="submit" disabled>Search</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle text-nowrap">
            <thead class="table-dark text-center">
                <tr>
                    <th>Sr.No</th>
                    <th>Name</th>
                    <th>Manager</th>
                    <th>Emp ID</th>
                    <th>Company</th>
                    <th>Total Amount</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @forelse ($reimbursements as $reimbursement)
                <tr>
                    <td>{{ $reimbursement->id }}</td>
                    <td>{{ $reimbursement->user->name }}</td>
                    <td>{{ $reimbursement->user->employee->manager->manager_name ?? 'N/A' }}</td>
                    <td>{{ $reimbursement->user->employee->employee_id ?? 'N/A' }}</td>
                    <td>{{ $reimbursement->company ?? 'N/A' }}</td>
                    <td>{{ $reimbursement->amount }}</td>
                    <td class="text-start" style="max-width: 300px;">
                        <div style="max-height: 200px; overflow-y: auto;">
                            @php
                            $details = is_array($reimbursement->details) ? $reimbursement->details : json_decode($reimbursement->details, true);
                            @endphp

                            @if (!empty($details))
                            <ul class="mb-0 ps-3">
                                @foreach ($details as $item)
                                <li class="mb-2">
                                    <strong>Date:</strong> {{ $item['date'] ?? 'N/A' }}<br>
                                    <strong>Event:</strong> {{ ucfirst($item['event'] ?? 'N/A') }}<br>
                                    @php
                                    $details = json_decode($reimbursement->details, true);

                                    $currencies = collect($details)->pluck('currency')->unique()->values();

                                    if ($currencies->count() === 1) {
                                    $currency = $currencies[0];
                                    } else {
                                    $currency = 'Multiple';
                                    }
                                    @endphp
                                    <strong>Amount:</strong> {{ $currency }} {{ number_format($item['amount'] ?? 0, 2) }}<br>
                                    <strong>Description:</strong> {{ $item['description'] ?? 'N/A' }}<br>
                                    @if (!empty($item['bill']))
                                    <strong>Bill:</strong>
                                    <a href="{{ asset($item['bill']) }}" target="_blank">View</a>
                                    @endif
                                    <hr>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <span class="text-muted">No details available</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @php
                        $statusColors = [
                        'pending' => 'secondary',
                        'manager_approved' => 'primary',
                        'hr_approved' => 'warning',
                        'cfo_approved' => 'success',
                        'rejected' => 'danger',
                        'processed' => 'info',
                        ];
                        $color = $statusColors[$reimbursement->status] ?? 'dark';
                        @endphp

                        <span class="badge bg-{{ $color }} text-uppercase">
                            {{ str_replace('_', ' ', $reimbursement->status) }}
                        </span>
                    </td>
                    <td>{{ $reimbursement->rejection_reason ?? 'N/A'}}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">No reimbursement records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination links --}}
        <div class="mt-4">
            {{ $reimbursements->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection