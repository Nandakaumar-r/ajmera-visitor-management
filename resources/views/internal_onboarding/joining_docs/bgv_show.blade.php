@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center text-primary">BGV Documents Uploaded</h2>

    @if($candidates->isEmpty())
    <div class="alert alert-warning text-center">
        No BGV documents found.
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Candidate Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>DOB</th>
                    <th>Actions</th>

                </tr>
            </thead>
            <tbody>
                @foreach($candidates as $doc)
                <tr>
                    <td>{{ $doc->id }}</td>
                    <td>{{ $doc->candidate->name ?? 'N/A' }}</td>
                    <td>{{ $doc->candidate->email ?? 'N/A' }}</td>
                    <td>{{ $doc->candidate->mobile ?? 'N/A' }}</td>
                    <td>{{ $doc->candidate->dob ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('joining-docs.view', ['id' => $doc->id]) }}" class="btn btn-primary btn-sm">View</a>
                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection