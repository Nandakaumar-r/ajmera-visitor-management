@extends('layouts.app')
 
@section('content')
<div class="container mt-5">
    <h2 class="mb-4" style="margin-top: 90px;font-size:20px;font-weight:600;text-align:center">Onboarding Candidates</h2>
 
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>status</th>
                    <th>Approving</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orfs as $index => $orf)
                    <tr>
                        <td>{{ $loop->iteration + ($orfs->currentPage() - 1) * $orfs->perPage() }}</td>
                        <td>{{ $orf->name }}</td>
                        <td>{{ $orf->email }}</td>
                        <td>{{ $orf->mobile }}</td>
                        <td>{{ $orf->status }}</td>
                        <td>HRBP</td>
                        <td>
                            <a href="{{ route('onboarding.view', $orf->id) }}" class="btn btn-sm btn-primary">View</a>
 
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No candidate data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
 
    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        {{ $orfs->links() }}
    </div>
</div>
@endsection