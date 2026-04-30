<?php

namespace App\Http\Controllers;

use App\Mail\OnboardingLinkMail;
use App\Models\CandidateCreation;
use App\Models\InternalORFCreation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Can;

class InternalORFCreationController extends Controller
{
    public function create(Request $request)
    {

        return view('internal_onboarding.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'experience_level' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'expiry_date' => 'required|date',
            'company' => 'required|string|max:255',
            'date_of_joining' => 'required|date',
            'candidate_ctc' => 'required|numeric|min:0',
            'designation' => 'required|string',
            'employee_type' => 'required|string',
            'candidate_type' => 'nullable|string',
            'interview_selection_date' => 'required|date',
        ]);

        $validated['user_id'] = Auth::id();

       $orfCreate = InternalORFCreation::create($validated);

        // Create user in secondary DB (you can adjust fields as needed)
        // CandidateCreation::create([
        //     'name' => $validated['name'],
        //     'email' => $validated['email'],
        //     'password' => bcrypt(Str::random(10)), // generate random password
        // ]);

        $onboardingLink = url('/internal-onboarding/candidate-create/' . $orfCreate->id);
        Mail::to($validated['email'])->send(new OnboardingLinkMail($validated['name'], $onboardingLink));
        return redirect()->back()->with('success', 'ORF and user created successfully.');

        return redirect()->back()->with('success', 'ORF and user created successfully.');
    }
}
