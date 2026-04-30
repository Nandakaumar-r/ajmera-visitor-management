@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center" style="margin-top: 70px; font-size: 20px; font-weight: 600;">Candidate BGV Documents</h2>
    <div class="card p-4">
        <h5 class="mb-3">Candidate Information</h5>
        <p><strong>Name:</strong> {{ $joiningDoc->candidate->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $joiningDoc->candidate->email ?? 'N/A' }}</p>
        <p><strong>Mobile:</strong> {{ $joiningDoc->candidate->mobile ?? 'N/A' }}</p>
        <p><strong>DOB:</strong> {{ $joiningDoc->candidate->dob ?? 'N/A' }}</p>
    </div>

    <div class="card p-4 mt-4">
        <h5 class="mb-3">Uploaded Documents</h5>
        <ul class="list-group">

            @php
            $docs = [
            'offer_letter' => 'Offer Letter',
            'acceptence_mail' => 'Acceptance Mail',
            'bgv' => 'BGV',
            'epf' => 'EPF',
            'gratuity' => 'Gratuity',
            'joining_form' => 'Joining Form',
            'nomination_declaration' => 'Nomination Declaration',
            'posh_ack' => 'POSH Ack',
            ];
            @endphp

            @foreach($docs as $field => $label)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $label }}
                @if($joiningDoc->$field)
                <div class="btn-group gap-4">
                    <a href="{{ route('joining-docs.bgv.view', ['id' => $joiningDoc->id, 'field' => $field]) }}"
                        target="_blank" class="btn btn-sm btn-secondary">View</a>

                    <a href="{{ route('joining-docs.bgv.download', ['id' => $joiningDoc->id, 'field' => $field]) }}"
                        class="btn btn-sm btn-primary">Download</a>
                </div>
                @else
                <span class="text-muted">Not Uploaded</span>
                @endif
            </li>
            @endforeach

        </ul>
    </div>
</div>
@endsection