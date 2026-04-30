<?php

namespace App\Http\Controllers;

use App\Models\InternalOnboardingCandidateDetails;
use App\Models\InternalOnboardingJoiningDoc;
use Illuminate\Http\Request;

class InternalOnboardingJoiningDocController extends Controller
{

    public function create($candidate_id)
    {
        $candidate = InternalOnboardingCandidateDetails::findOrFail($candidate_id);
        return view('internal_onboarding.joining_docs.create', compact('candidate'));
    }

    public function createBgv($candidate_id)
    {
        $candidate = InternalOnboardingCandidateDetails::findOrFail($candidate_id);
        return view('internal_onboarding.joining_docs.bgv', compact('candidate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:internal_candidate_details,id',
        ]);

        $fieldsToCheck = [
            'offer_letter',
            'acceptence_mail',
            'bgv',
            'epf',
            'gratuity',
            'joining_form',
            'nomination_declaration',
            'posh_ack'
        ];

        $data = ['candidate_id' => $request->candidate_id];

        foreach ($fieldsToCheck as $field) {
            if ($request->hasFile($field)) {
                $request->validate([
                    $field => 'file|mimes:pdf,doc,docx|max:5120' // max 5MB
                ]);

                $data[$field] = $request->file($field)->store("joining_docs/{$field}", 'public');
            }
        }

        InternalOnboardingJoiningDoc::updateOrCreate(
            ['candidate_id' => $request->candidate_id],
            $data
        );

        return back()->with('success', 'Documents uploaded successfully.');
    }

    public function bgvShow()
    {
        $candidates = InternalOnboardingJoiningDoc::with('candidate')->get();
        return view('internal_onboarding.joining_docs.bgv_show', compact('candidates'));
    }

    public function bgvView($id)
    {
        $joiningDoc = InternalOnboardingJoiningDoc::with('candidate')->findOrFail($id);
        return view('internal_onboarding.joining_docs.bgv_view', compact('joiningDoc'));
    }

    public function bgvDownload($id, $field)
    {
        $joiningDoc = InternalOnboardingJoiningDoc::findOrFail($id);

        $allowedFields = [
            'offer_letter' => 'Offer Letter',
            'acceptence_mail' => 'Acceptance Mail',
            'bgv' => 'BGV',
            'epf' => 'EPF',
            'gratuity' => 'Gratuity',
            'joining_form' => 'Joining Form',
            'nomination_declaration' => 'Nomination Declaration',
            'posh_ack' => 'POSH Ack',
        ];

        // Validate requested field
        if (!array_key_exists($field, $allowedFields) || !$joiningDoc->$field) {
            return back()->with('error', 'Document not found.');
        }

        $filePath = storage_path('app/public/' . $joiningDoc->$field);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File does not exist.');
        }

        // Get original file extension (e.g. .pdf, .jpg)
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Set custom download name like "Offer Letter.pdf"
        $downloadName = $allowedFields[$field] . '.' . $extension;

        return response()->download($filePath, $downloadName);
    }

    public function bgvPreview($id, $field)
    {
        $joiningDoc = InternalOnboardingJoiningDoc::findOrFail($id);

        $allowedFields = [
            'offer_letter',
            'acceptence_mail',
            'bgv',
            'epf',
            'gratuity',
            'joining_form',
            'nomination_declaration',
            'posh_ack'
        ];

        if (!in_array($field, $allowedFields) || !$joiningDoc->$field) {
            return back()->with('error', 'Document not found.');
        }

        $filePath = storage_path('app/public/' . $joiningDoc->$field);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File does not exist.');
        }

        // Set correct content type based on file extension
        $mimeType = \Illuminate\Support\Facades\File::mimeType($filePath);

        return response()->file($filePath, ['Content-Type' => $mimeType]);
    }
}
