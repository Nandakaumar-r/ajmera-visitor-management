{{-- Top Bar --}}
<div class="container py-4">
    <div style="position: fixed; top: 0; background: white; width: 100%; z-index: 1000; display: flex; align-items: center; justify-content: space-between; padding: 1rem 0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div style="flex-shrink: 0;">
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" style="height: 55px;">
        </div>
    </div>
</div>

<div style="padding: 1.5rem 1rem; margin-top: 90px;">
    <h2 style="margin-bottom: 0.5rem; font-size: 25px; text-align: center;">Onboarding Form</h2>

    {{-- Toast Alerts --}}
    @if(session('success'))
        <div id="successToast" style="position: fixed; top: 20px; right: 20px; background-color: #d1fae5; border: 1px solid #34d399; color: #065f46; padding: 1rem; border-radius: 0.375rem; z-index: 9999;">
            <strong style="font-weight: bold;">Success:</strong> <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div id="errorToast" style="position: fixed; top: 20px; right: 20px; background-color: #fee2e2; border: 1px solid #f87171; color: #991b1b; padding: 1rem; border-radius: 0.375rem; z-index: 9999;">
            <strong style="font-weight: bold;">Error:</strong> <span>{{ session('error') }}</span>
        </div>
    @endif

    <script>
        setTimeout(() => {
            const success = document.getElementById('successToast');
            const error = document.getElementById('errorToast');
            if (success) success.remove();
            if (error) error.remove();
        }, 5000);
    </script>

    {{-- Form --}}
    <div style="max-width: 1000px; margin: auto; background-color: white; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 8px;">
        <form action="{{ route('internal_onboarding_candidate_details.store') }}" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            @csrf
            <input type="hidden" name="orf_id" value="{{ $orf->id ?? '' }}">

            {{-- Heading --}}
            {{-- Text Fields --}}
            @foreach([
                ['name' => 'name', 'label' => 'Full name (as per AADHAR) *', 'type' => 'text'],
                ['name' => 'email', 'label' => 'Email address *', 'type' => 'email'],
                ['name' => 'mobile', 'label' => 'Mobile no (Whatsapp) *', 'type' => 'tel'],
                ['name' => 'dob', 'label' => 'Date of Birth *', 'type' => 'date'],
                ['name' => 'aadhar_no', 'label' => 'Aadhar Number *', 'type' => 'text'],
                ['name' => 'pan_no', 'label' => 'PAN Number *', 'type' => 'text'],
            ] as $field)
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">{{ $field['label'] }}</label>
                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" required
                           value="{{ old($field['name']) }}"
                           style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 0.375rem;">
                    @error($field['name'])
                        <span style="color: red; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach

            {{-- Address Fields --}}
            @foreach([
                ['name' => 'present_address', 'label' => 'Present Address *'],
                ['name' => 'permanent_address', 'label' => 'Permanent Address *']
            ] as $address)
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">{{ $address['label'] }}</label>
                    <textarea name="{{ $address['name'] }}" required
                              style="width: 100%; height: 100px; padding: 0.5rem; border: 1px solid #ccc; border-radius: 0.375rem;">{{ old($address['name']) }}</textarea>
                    @error($address['name'])
                        <span style="color: red; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach

            {{-- File Uploads --}}
            @php
                $files = [
                    ['name' => 'aadhar_card', 'label' => 'Aadhar Card *', 'multiple' => false],
                    ['name' => 'pan_card', 'label' => 'Pan Card *', 'multiple' => false],
                    ['name' => 'payslips', 'label' => 'Payslips (All Previous Companies) *', 'multiple' => true],
                    ['name' => 'bank_proof', 'label' => 'Bank Passbook / Cancelled Cheque *', 'multiple' => true],
                    ['name' => 'education_docs', 'label' => '10th, 12th, Graduation Mark Sheets & Provisional *', 'multiple' => true],
                    ['name' => 'salary_revision_letter', 'label' => 'Salary Revision Letter', 'multiple' => true],
                    ['name' => 'experience_letters', 'label' => 'Experience Letters (Previous Companies) *', 'multiple' => true],
                    ['name' => 'passport_photo', 'label' => 'Passport Size Photo *', 'multiple' => true],
                    ['name' => 'resume', 'label' => 'Updated Resume *', 'multiple' => false],
                ];
            @endphp

            @foreach($files as $file)
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.25rem;">{{ $file['label'] }}</label>
                    <input type="file"
                           name="{{ $file['multiple'] ? $file['name'] . '[]' : $file['name'] }}"
                           {{ $file['multiple'] ? 'multiple' : '' }}
                           accept=".pdf,.jpg,.jpeg,.png"
                           style="width: 100%; border: 1px solid #ccc; padding: 0.5rem; border-radius: 0.375rem;">
                    @error($file['name'])
                        <span style="color: red; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror

                    {{-- For array validation errors like payslips.* --}}
                    @if($file['multiple'])
                        @foreach ($errors->get($file['name'] . '.*') as $messages)
                            @foreach ($messages as $msg)
                                <span style="color: red; font-size: 0.875rem;">{{ $msg }}</span><br>
                            @endforeach
                        @endforeach
                    @endif
                </div>
            @endforeach

            {{-- Submit --}}
            <div style="grid-column: span 2; display: flex; justify-content: center; margin-top: 2rem;">
                <button type="submit"
                        style="padding: 0.75rem 2rem; background-color: #2563eb; color: white; font-weight: 600; border: none; border-radius: 0.375rem; cursor: pointer;">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
