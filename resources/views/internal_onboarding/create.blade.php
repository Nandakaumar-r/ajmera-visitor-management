@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4" style="margin-top: 90px;font-size:20px;font-weight:600;text-align:center">Internal ORF Creation Form</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('internal_onboarding.store') }}" method="POST">
        @csrf


        <div class="mb-3">
            <label for="name" class="form-label">Candidate Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="experience_level" class="form-label">Experience Level</label>
            <select name="experience_level" id="experience_level" class="form-control" required>
                <option value="">-- Select Experience Level --</option>
                <option value="Fresher">Fresher</option>
                <option value="Experienced">Experienced</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Candidate Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select name="gender" class="form-control" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="company" class="form-label">Company</label>
            <input type="text" name="company" id="company" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="designation" class="form-label">Designation</label>
            <input type="text" name="designation" id="designation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="candidate_ctc" class="form-label">Candidate CTC</label>
            <input type="text" name="candidate_ctc" id="candidate_ctc" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="employee_type" class="form-label">Employee Type</label>
            <select name="employee_type" id="employee_type" class="form-control" required>
                <option value="">-- Select Employee Type --</option>
                <option value="Permanent">Permanent</option>
                <option value="Consultant">Consultant</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="candidate_type" class="form-label">Candidate Type</label>
            <select name="candidate_type" id="candidate_type" class="form-control" required>
                <option value="">-- Select Candidate Type --</option>
                <option value="New">New</option>
                <option value="Replace">Replace</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="interview_selection_date" class="form-label">Interview Selection Date</label>
            <input type="date" name="interview_selection_date" id="interview_selection_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="date_of_joining" class="form-label">Date of Joining</label>
            <input type="date" name="date_of_joining" id="date_of_joining" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="expiry_date" class="form-label">Link Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="{{ now()->addDays(7)->toDateString() }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit & Send Email</button>
    </form>
</div>
@endsection
