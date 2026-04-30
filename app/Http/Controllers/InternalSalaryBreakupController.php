<?php

namespace App\Http\Controllers;

use App\Imports\SalaryBreakupImport;
use App\Mail\InternalFinalOfferMail;
use App\Models\InternalOnboardingCandidateDetails;
use App\Models\InternalOnboardingJoiningDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\In;
use Illuminate\Support\Facades\File;

class InternalSalaryBreakupController extends Controller
{

    public function import(Request $request, $candidate_id)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new SalaryBreakupImport($candidate_id), $request->file('file'));

        return back()->with('success', 'Salary breakup imported successfully.');
    }

    public function create()
    {
        return view('internal_onboarding.salary_breakup');
    }

    public function generateOfferLetter($id)
    {
        $candidate = InternalOnboardingCandidateDetails::with('salaryBreakup', 'orfCreation')->findOrFail($id);
        $pdf = Pdf::loadView('offer_letter', [
            'candidate' => $candidate,
            'salary' => $candidate->salaryBreakup,
            'companyName' => 'Fidelis Group',
            'hr_name' => 'HR Department'
        ])->setPaper('a4');

        return $pdf->download('Sample_Offer_Letter_' . $candidate->name . '.pdf');
    }

    public function candidateFinalOffer($id, $role)
    {
        $candidate = InternalOnboardingCandidateDetails::with('salaryBreakup', 'orfCreation')->findOrFail($id);
        $offerLetterDir = storage_path('app/public/offer_letters');

        if (!File::exists($offerLetterDir)) {
            File::makeDirectory($offerLetterDir, 0755, true);
        }

        $pdf = Pdf::loadView('offer_letter', [
            'candidate' => $candidate,
            'salary' => $candidate->salaryBreakup,
            'companyName' => 'Fidelis Group',
            'hr_name' => 'HR Department'
        ])->setPaper('a4');

        $offerLetterPath = $offerLetterDir . '/Offer_Letter_' . $candidate->name . '.pdf';
        $pdf->save($offerLetterPath);

        $attachments = [
            ['file' => $offerLetterPath, 'options' => []], // Offer Letter
        ];

        //$docDir = public_path('internal_joining_doc');

        // Attach documents by default name (static)
        $files  = [
            'BGV.pdf',
            'EPF - New Form No. 11 - Declaration Form.pdf',
            'Gratuity.pdf',
            'Joining Form.pdf',
            'Nomination & Declaration form.pdf',
            'POSH Acknowlegement.pdf',
        ];

        $publicPath = public_path('internal_joining_doc');

        foreach ($files as $filename) {
            $path = $publicPath . '/' . $filename;
            if (file_exists($path)) {
                $attachments[] = ['file' => $path, 'options' => []];
            }
        }
        if ($role === 'hr') {
            $candidate->status = 'offered';
            $candidate->remarks = 'Marked as Offered by HR';
            $candidate->user_id = Auth::id();
            $candidate->{$role . '_status'} = 'offered';
            $candidate->save();
        }

        Mail::to($candidate->email)->send(new InternalFinalOfferMail($candidate, $attachments));

        return redirect()->back()->with('success', 'Final offer and documents sent to candidate.');
    }
}
