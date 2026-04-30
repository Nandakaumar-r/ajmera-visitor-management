@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 mt-16">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>
                            @if($workflow->bill->is_credit_note)
                                Credit Note Approval
                            @else
                                Bill Approval
                            @endif
                        </h6>
                        <a href="{{ route('admin.bills.approval.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Queue
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Bill Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Bill Number:</th>
                                    <td>
                                        @if($workflow->bill->is_credit_note)
                                            {{ $workflow->bill->credit_note_number }}
                                        @else
                                            {{ $workflow->bill->bill_number }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Vendor:</th>
                                    <td>{{ $workflow->bill->vendor->company_name }}</td>
                                </tr>
                                <tr>
                                    <th>Bill Date:</th>
                                    <td>
                                        @if($workflow->bill->is_credit_note)
                                            {{ $workflow->bill->credit_note_date->format('d M Y') }}
                                        @else
                                            {{ $workflow->bill->bill_date->format('d M Y') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Amount:</th>
                                    <td>
                                        @if($workflow->bill->is_credit_note)
                                            <span class="text-danger">-₹{{ number_format($workflow->bill->total_amount, 2) }}</span>
                                        @else
                                            ₹{{ number_format($workflow->bill->total_amount, 2) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($workflow->bill->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($workflow->bill->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($workflow->bill->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($workflow->bill->status == 'transferred')
                                            <span class="badge bg-info">Transferred</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($workflow->bill->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($workflow->bill->is_credit_note)
                                    <tr>
                                        <th>Credit Note Reason:</th>
                                        <td>{{ $workflow->bill->credit_note_reason }}</td>
                                    </tr>
                                    @if($workflow->bill->originalBill)
                                        <tr>
                                            <th>Original Bill:</th>
                                            <td>
                                                <a href="{{ route('admin.bills.show', $workflow->bill->originalBill->id) }}">
                                                    {{ $workflow->bill->originalBill->bill_number }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Approval Workflow</h6>
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <h6 class="mb-0">Current Level: {{ $workflow->current_level }}</h6>
                                            <p class="text-sm mb-0">Overall Status: 
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
                                            </p>
                                        </div>
                                        @if($canApprove)
                                            <div>
                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                                                    Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                    Reject
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Approval Progress -->
                                    <div class="progress-steps">
                                        @for($i = 1; $i <= 6; $i++)
                                            <div class="step {{ $i <= $workflow->current_level ? 'active' : '' }} {{ $workflow->{'level_'.$i.'_status'} == 'approved' ? 'completed' : '' }} {{ $workflow->{'level_'.$i.'_status'} == 'rejected' ? 'rejected' : '' }}">
                                                <div class="step-icon">
                                                    @if($workflow->{'level_'.$i.'_status'} == 'approved')
                                                        <i class="fas fa-check"></i>
                                                    @elseif($workflow->{'level_'.$i.'_status'} == 'rejected')
                                                        <i class="fas fa-times"></i>
                                                    @else
                                                        {{ $i }}
                                                    @endif
                                                </div>
                                                <div class="step-label">
                                                    @if($i == 1)
                                                        Initial
                                                    @elseif($i == 2)
                                                        HR
                                                    @elseif($i == 3)
                                                        Finance
                                                    @elseif($i == 4)
                                                        CFO
                                                    @elseif($i == 5)
                                                        Payment
                                                    @elseif($i == 6)
                                                        Final
                                                    @endif
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status History -->
                    @if($workflow->bill->statusHistory->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>Status History</h6>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comments</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($workflow->bill->statusHistory as $history)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm">{{ $history->created_at->format('d M Y') }}</h6>
                                                                <p class="text-xs text-secondary mb-0">{{ $history->created_at->format('h:i A') }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($history->status == 'pending')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @elseif($history->status == 'approved')
                                                            <span class="badge bg-success">Approved</span>
                                                        @elseif($history->status == 'rejected')
                                                            <span class="badge bg-danger">Rejected</span>
                                                        @elseif($history->status == 'transferred')
                                                            <span class="badge bg-info">Transferred</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($history->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">{{ $history->comments ?? 'No comments' }}</p>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm">{{ $history->user->name }}</h6>
                                                                <p class="text-xs text-secondary mb-0">{{ $history->user->email }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.bills.approval.update', $workflow->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="approve">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Bill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approveComments" class="form-label">Comments (Optional)</label>
                        <textarea class="form-control" id="approveComments" name="comments" rows="3" placeholder="Add any comments for this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.bills.approval.update', $workflow->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="reject">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Bill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectComments" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectComments" name="comments" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
