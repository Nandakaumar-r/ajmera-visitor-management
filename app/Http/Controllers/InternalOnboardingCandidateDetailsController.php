<?php

namespace App\Http\Controllers;

use App\Models\InternalOnboardingCandidateDetails;
use App\Models\InternalORFCreation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InternalOnboardingCandidateDetailsController extends Controller
{

    public function create($id)
    {
    
        $orf = InternalORFCreation::findOrFail($id);
        return view('internal_onboarding.form', compact('orf'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:internal_candidate_details,email',
                'mobile' => 'required|string|max:15',
                'dob' => 'required|date',

                // Aadhar number (12 digits, must be unique)
                'aadhar_no' => ['required', 'digits:12', 'unique:internal_candidate_details,aadhar_no'],

                // PAN number (ABCDE1234F format, must be unique)
                'pan_no' => ['required', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', 'unique:internal_candidate_details,pan_no'],

                'present_address' => 'required|string',
                'permanent_address' => 'required|string',

                // File uploads (max 2MB, valid formats only)
                'aadhar_card' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'pan_card' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'resume' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',

                'payslips.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'bank_proof.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'education_docs.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'salary_revision_letter.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'experience_letters.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'passport_photo.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ],
            [
                'email.unique' => 'This email is already registered.',
                'pan_no.unique' => 'This PAN number is already registered.',
                'aadhar_no.unique' => 'This Aadhar number is already registered.',
                'pan_no.regex' => 'The PAN number format is invalid. Example: ABCDE1234F',
                'aadhar_no.digits' => 'Aadhar number must be exactly 12 digits.',
                '*.mimes' => 'Only PDF, JPG, JPEG, and PNG files are allowed.',
                '*.max' => 'Each file must be less than 2MB.',
            ]
        );

        $data = $validated;
        $baseFolder = 'onboarding_candidate';

        // Handle single file uploads
        foreach (['aadhar_card', 'pan_card', 'resume'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store("{$baseFolder}/{$field}", 'public');
            }
        }

        // Handle multiple file uploads
        foreach (
            [
                'payslips',
                'bank_proof',
                'education_docs',
                'salary_revision_letter',
                'experience_letters',
                'passport_photo'
            ] as $field
        ) {
            $paths = [];
            if ($request->hasFile($field)) {
                foreach ($request->file($field) as $file) {
                    $paths[] = $file->store("{$baseFolder}/{$field}", 'public');
                }
            }
            $data[$field] = $paths;
        }

        $candidate = InternalOnboardingCandidateDetails::create($data);
        if ($request->filled('orf_id')) {
            InternalORFCreation::where('id', $request->input('orf_id'))
                ->update(['candidate_id' => $candidate->id]);
        }

        return back()->with('success', 'Documents uploaded successfully.');
    }
}
