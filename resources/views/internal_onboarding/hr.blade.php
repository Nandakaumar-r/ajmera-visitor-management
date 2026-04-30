@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center fw-bold" style="margin-top: 90px;font-size:26px;">HR Review</h2>

    <div class="card shadow p-4">
        {{-- Section 1: Internal ORF --}}
        @if($orfCreation)
        <h2 class="text-primary mb-3" style="font-size:22px; font-weight:600;">Internal ORF Details</h2>
        <div class="row mb-4">
            <div class="col-md-6 mb-2"><strong>Candidate Name:</strong> {{ $orfCreation->name }}</div>
            <div class="col-md-6 mb-2"><strong>Email:</strong> {{ $orfCreation->email }}</div>
            <div class="col-md-6 mb-2"><strong>Experience Level:</strong> {{ $orfCreation->experience_level }}</div>
            <div class="col-md-6 mb-2"><strong>Gender:</strong> {{ $orfCreation->gender }}</div>
            <div class="col-md-6 mb-2"><strong>Company:</strong> {{ $orfCreation->company }}</div>
            <div class="col-md-6 mb-2"><strong>Date of Joining:</strong> {{ $orfCreation->date_of_joining }}</div>
            <div class="col-md-6 mb-2"><strong>Candidate Ctc:</strong> {{ $orfCreation->candidate_ctc }}</div>
            <div class="col-md-6 mb-2"><strong>Designation:</strong> {{ $orfCreation->designation }}</div>
            <div class="col-md-6 mb-2"><strong>Employee Type:</strong> {{ $orfCreation->employee_type }}</div>
            <div class="col-md-6 mb-2"><strong>Candidate Type:</strong> {{ $orfCreation->candidate_type }}</div>
            <div class="col-md-6 mb-2"><strong>Interview Selection Date:</strong> {{ $orfCreation->interview_selection_date }}</div>
        </div>
        @else
        <p class="text-muted mb-4">No ORF creation details available.</p>
        @endif

        <hr>

        {{-- Section 2: Candidate Details --}}
        <h2 class="text-primary mb-3" style="font-size:22px; font-weight:600;margin-top:20px;">Candidate Details</h2>
        <div class="row mb-4">
            <div class="col-md-6 mb-2"><strong>Name:</strong> {{ $orf->name }}</div>
            <div class="col-md-6 mb-2"><strong>Email:</strong> {{ $orf->email }}</div>
            <div class="col-md-6 mb-2"><strong>Mobile:</strong> {{ $orf->mobile }}</div>
            <div class="col-md-6 mb-2"><strong>Date of Birth:</strong> {{ $orf->dob }}</div>
            <div class="col-md-6 mb-2"><strong>Aadhar No:</strong> {{ $orf->aadhar_no }}</div>
            <div class="col-md-6 mb-2"><strong>PAN No:</strong> {{ $orf->pan_no }}</div>
            <div class="col-md-12 mb-2"><strong>Present Address:</strong> {{ $orf->present_address }}</div>
            <div class="col-md-12 mb-2"><strong>Permanent Address:</strong> {{ $orf->permanent_address }}</div>
        </div>

        <hr>

        {{-- Section 3: Documents --}}
        <h2 class="text-primary mb-3" style="font-size:22px; font-weight:600;margin-top:20px;">Uploaded Documents</h2>
        <div class="row">
            @php
            $fileGroups = [
            'resume' => 'Resume',
            'aadhar_card' => 'Aadhar Card',
            'pan_card' => 'PAN Card',
            'payslips' => 'Payslips',
            'bank_proof' => 'Bank Proof',
            'education_docs' => 'Education Documents',
            'salary_revision_letter' => 'Salary Revision Letter',
            'experience_letters' => 'Experience Letters',
            'passport_photo' => 'Passport Photo',
            ];
            @endphp

            @foreach ($fileGroups as $field => $label)
            @php
            $fieldData = $orf->$field;

            // For fields that are arrays (multi-file)
            $multiFileFields = ['payslips', 'bank_proof', 'education_docs', 'salary_revision_letter', 'experience_letters', 'passport_photo'];

            if (in_array($field, $multiFileFields)) {
            // Handle multi-files (stored as JSON or array)
            if (is_string($fieldData)) {
            $files = json_decode($fieldData, true);
            } elseif (is_array($fieldData)) {
            $files = $fieldData;
            } else {
            $files = [];
            }

            // Wrap in array if it's a single string
            if (!is_array($files)) {
            $files = [$files];
            }
            } else {
            // Handle single file
            $files = $fieldData ? [$fieldData] : [];
            }
            @endphp


            @if (!empty($files) && $files[0] !== null)
            <div class="col-md-6 mb-3">
                <strong>{{ $label }}:</strong>

                @foreach ($files as $index => $file)
                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                    {{ $label }} {{ is_array($files) ? $index + 1 : '' }}
                </a>

                @endforeach
            </div>
            @endif
            @endforeach
        </div>
        <hr style="margin-top: 20px;">
        <div class="row">
            <h2 class="mb-4 text-primary " style="font-size:22px; font-weight:600;margin-top:20px;">Create Salary Breakup</h2>

            <form action="{{ route('salary_breakups.import', ['candidate_id' => $orf->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Flex container for file input and generate button --}}
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                    {{-- File Upload --}}
                    <div style="flex: 1 1 300px;">
                        <label for="file" class="form-label">Upload Salary Excel Sheet (.xlsx)</label>
                        <input type="file" name="file" class="form-control" required accept=".xlsx,.xls">
                    </div>
                </div>
                <input type="hidden" name="candidate_id" value="{{ request('candidate_id') }}">
                {{-- Upload Button --}}
                <button type="submit" class="btn btn-primary mt-4">Upload</button>
                {{-- Generate Sample Offer Letter Button --}}
                <div style="flex: 1 1 300px; display: flex; align-items: end; margin-top: 20px;">
                    <a type="button" href="{{ url('/sample/offer-letter/' . $orf->id) }}" class="btn btn-primary w-30">
                        Generate Sample Offer Letter
                    </a>
                </div>

            </form>
        </div>

        {{-- Footer Buttons for HR --}}
        <div class="d-flex justify-content-between mt-4 flex-wrap gap-3">
            <a href="{{ url('/orf/' . $role) }}" class="btn btn-secondary">← Back to List</a>

            @php
            $currentStatus = $orf->{$role . '_status'};
            @endphp

            @if($currentStatus === null)
            <div class="d-flex">
                <form action="{{ route('offer.letter', ['role' => $role, 'id' => $orf->id]) }}" method="GET" class="me-2">
                    @csrf
                    <button type="submit" class="btn btn-success">Final Offer</button>
                </form>

                <form action="{{ route('orf.cancel', ['role' => $role, 'id' => $orf->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Offer Cancel</button>
                </form>
            </div>
            @elseif($currentStatus === 'offered')
            <div class="alert alert-success mb-0">
                Candidate has been <strong>Offered</strong> by HR.
            </div>
            @elseif($currentStatus === 'cancelled')
            <div class="alert alert-danger mb-0">
                Candidate has been <strong>Cancelled</strong> by HR.
            </div>
            @endif
        </div>

    </div>
</div>
@endsection