@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">All External Reimbursements</h2>

    {{-- Search form --}}
    <form method="GET" action="{{ route('external-reimbursements.index') }}" class="mb-3 d-flex justify-content-end">
        <div class="input-group" style="max-width: 300px;">
            <input type="text" name="emp_id" class="form-control" placeholder="Search by Emp ID" value="{{ request('emp_id') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    @if (session('success'))
    <div style="width: 40%; margin: 20px auto; padding: 12px 20px; background-color: #d4edda; color: #FF0000; border-radius: 6px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-size: 15px;">
        {{ session('success') }}
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle text-nowrap">
            <thead class="table-dark text-center">
                <tr>
                    <th>Sr.No</th>
                    <th>Name</th>
                    <!-- <th>Manager</th> -->
                    <th>Emp ID</th>
                    <!-- <th>Designation</th> -->
                    <!-- <th>Purpose</th> -->
                    <th>Total Amount</th>
                    <th class="min-w-[200px] text-center">Reimbursement Items</th>
                    <th>Status</th>
                    <th>Attachment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach ($reimbursements as $reimbursement)
                <tr>
                    <td>{{ $reimbursement->id }}</td>
                    <td>{{ $reimbursement->name }}</td>
                    <!-- <td>{{ $reimbursement->manager_name }}</td> -->
                    <td>{{ $reimbursement->emp_id }}</td>
                    <!-- <td>{{ $reimbursement->designation }}</td>
                    <td>{{ $reimbursement->business_purpose }}</td> -->
                    <td>{{ $reimbursement->amount }}</td>
                    <td class="min-w-[200px] text-center">
                        @php
                        $details = is_array($reimbursement->reimbursement_details)
                        ? $reimbursement->reimbursement_details
                        : json_decode($reimbursement->reimbursement_details, true);

                        $flattened = collect($details)
                        ->flatMap(fn($group) => is_array($group) ? $group : [])
                        ->map(fn($item) =>
                        ($item['description'] ?? 'N/A') . ' - ₹' . ($item['amount'] ?? 0)
                        );

                        $preview = $flattened->take(2)->implode(', ');
                        $moreExists = $flattened->count() > 2;
                        $tooltipText = $flattened->implode(' | ');
                        @endphp

                        <div class="truncate w-[200px] text-center mx-auto" title="{{ $tooltipText }}">
                            {{ $preview }}{{ $moreExists ? ' ...' : '' }}
                        </div>
                    </td>
                    <td>
                        @php
                        $statusColors = [
                        'pending' => 'secondary',
                        'finance_approved' => 'primary',
                        'cfo_approved' => 'success',
                        'rejected' => 'danger',
                        'processed' => 'info',
                        ];
                        $color = $statusColors[$reimbursement->status] ?? 'dark';
                        @endphp

                        <span class="badge bg-{{ $color }}">
                            {{ str_replace('_', ' ', $reimbursement->status) }}
                        </span>
                    </td>
                    <td>
                        @if ($reimbursement->manager_approval_attachment)
                        <a href="{{ asset('/' . $reimbursement->manager_approval_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('external-reimbursements.delete', $reimbursement->id) }}" 
                            onclick="return confirm('Are you sure you want to delete this reimbursement?')" 
                            class="btn btn-sm btn-danger">Delete
                        </a>
                        <a href="{{ route('external-reimbursements.show', $reimbursement->id) }}" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination links --}}
        <div class="mt-4">
            {{ $reimbursements->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection