@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4" style="margin-top: 120px; font-size: 20px; font-weight: 600; text-align: center;">
        Candidate List for {{ strtoupper($role) }}
    </h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>DOB</th>
                    <th>Aadhar</th>
                    <th>Status ({{ strtoupper($role) }})</th> <!-- ✅ New Column -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($candidates as $index => $candidate)
                    @php
                        $statusField = $role . '_status';
                        $statusValue = $candidate->$statusField;
                        $statusDisplay = match(strtolower($statusValue)) {
                            'approved' => '✅ Approved',
                            'rejected' => '❌ Rejected',
                            'offered' => '📩 Offered',
                            'cancelled' => '🚫 Cancelled',
                            default => '⏳ Pending',
                        };
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration + ($candidates->currentPage() - 1) * $candidates->perPage() }}</td>
                        <td>{{ $candidate->name }}</td>
                        <td>{{ $candidate->email }}</td>
                        <td>{{ $candidate->mobile }}</td>
                        <td>{{ $candidate->dob }}</td>
                        <td>{{ $candidate->aadhar_no }}</td>
                        <td>{{ $statusDisplay }}</td> <!-- ✅ Display Status -->
                        <td>
                            <a href="{{ route('orf.view.role', ['role' => $role, 'id' => $candidate->id]) }}" class="btn btn-primary btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No candidate data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        {{ $candidates->links() }}
    </div>
</div>
@endsection
