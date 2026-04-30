@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>Vendor Bill Approval Queue</h6>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive p-0">
                        @if($workflows->count() > 0)
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bill #</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Vendor</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Current Level</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workflows as $workflow)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $workflow->bill->bill_number }}</h6>
                                                        @if($workflow->bill->is_credit_note)
                                                            <span class="text-xs text-warning">Credit Note</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $workflow->bill->vendor->company_name }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ $workflow->bill->vendor->contact_person }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    @if($workflow->bill->is_credit_note)
                                                        -₹{{ number_format($workflow->bill->total_amount, 2) }}
                                                    @else
                                                        ₹{{ number_format($workflow->bill->total_amount, 2) }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge bg-primary">Level {{ $workflow->current_level }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($workflow->overall_status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($workflow->overall_status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($workflow->overall_status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @elseif($workflow->overall_status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($workflow->overall_status) }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('admin.bills.approval.show', $workflow->id) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                <h4>No bills pending approval</h4>
                                <p class="text-muted">There are currently no bills requiring your approval.</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($workflows->count() > 0)
                        <div class="d-flex justify-content-center mt-4">
                            {{ $workflows->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
