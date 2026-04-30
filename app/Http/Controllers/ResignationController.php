<?php

namespace App\Http\Controllers;

use App\Mail\FarewellEmail;
use App\Mail\LWDConfirmationMail;
use App\Mail\LWDConfirmationMailToEmployee;
use App\Mail\LWDConfirmationMailToHR;
use App\Mail\LWDConfirmationMailtoManager;
use App\Mail\ResignationDeclinedNotification;
use App\Mail\ResignationNotification;
use App\Mail\ResignationNotificationForEmployee;
use App\Models\Departments;
use App\Models\Employee;
use App\Models\ExitProcess;
use App\Models\InterviewQuestion;
use App\Models\InterviewResponse;
use App\Models\Manager;
use App\Models\Resignation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ResignationController extends Controller
{
    // Display all resignations for the authenticated employee
    public function index(Request $request)
    {
        $employee = $request->employee;
        $resignations = Resignation::where('employee_id', $employee->employee_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('resignation.index', compact('resignations'));
    }

    // Show resignation form
    public function create(Request $request)
    {
        // Employee is now available from the request
        $employee = $request->employee;
        $resignation = Resignation::where('employee_id', $employee->employee_id)->first();
        return view('resignation.create', compact('resignation', 'employee'));
    }

    // Store the resignation form
    public function store(Request $request)
    {
        $employee = $request->employee;

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'additional_details' => 'nullable|string',
            'resignation_date' => 'required|date',
        ]);

        $validated['employee_id'] = $employee->employee_id;

        $resignation = Resignation::create($validated);

        $manager = Manager::where('manager_id', $employee->manager_id)->first();
        if ($manager) {
            Mail::to($employee->employee_email)->send(new ResignationNotificationForEmployee($resignation));
            Mail::to($manager->manager_email)->send(new ResignationNotification($resignation));
        }

        ExitProcess::create([
            'employee_id' => $validated['employee_id'],
            'resignation_mail' => $validated['additional_details'] . ' - ' . $validated['resignation_date'],
        ]);

        return redirect()->back()->with('success', 'Your resignation request has been submitted.');
    }


    // Show resignation details
    public function show($id)
    {
        $resignation = Resignation::with('employee')->findOrFail($id);
        return view('resignation.show', compact('resignation'));
    }

    // Display all pending resignations
    // public function pending_resignations()
    // {
    //     $resignations = Resignation::whereNull('status')->get();
    //     return view('manager.resignations', compact('resignations'));
    // }

    // Display all pending resignations
    public function pending_resignations()
    {
        $user = Auth::user();

        if ($user->hasRole('Manager')) {
            // Match logged-in user with managers table by email
            $manager = Manager::where('manager_email', $user->email)->first();

            if ($manager) {
                $managerId = $manager->manager_id;

                $resignations = Resignation::whereNull('status')
                    ->whereHas('employee', function ($query) use ($managerId) {
                        $query->where('manager_id', $managerId);
                    })
                    ->with('employee') // eager load employee for efficiency
                    ->get();
            } else {
                $resignations = collect();
            }
        }

        // Apply notice period
        foreach ($resignations as $resignation) {
            $designation = strtolower($resignation->employee->employee_designation);

            // Default notice = 1 month
            $noticePeriod = '1 Month';

            if (
                str_contains($designation, 'manager') ||
                str_contains($designation, 'lead') ||
                str_contains($designation, 'partner') ||
                str_contains($designation, 'head') ||
                str_contains($designation, 'vice president') ||
                str_contains($designation, 'consultant')
            ) {
                $noticePeriod = '2 Months';
            }

            $resignation->notice_period = $noticePeriod;
        }

        return view('manager.resignations', compact('resignations'));
    }


    // Accept the resignation and set the last working date
    public function accept(Request $request, $id)
    {
        $request->validate([
            'manager_last_working_day' => 'required|date|after:today',
        ]);

        $resignation = Resignation::findOrFail($id);
        $resignation->manager_last_working_day = $request->input('manager_last_working_day');
        $resignation->status = 'Accepted';
        $resignation->save();

        $manager = Manager::where('manager_id', $resignation->employee->manager_id)->first();
        Mail::to($manager->manager_email)->send(new LWDConfirmationMailtoManager($resignation, $manager));
        Mail::to($resignation->employee->employee_email)->send(new LWDConfirmationMailToEmployee($resignation, $manager, Auth::user()));
        Mail::to(env('HR_EMAIL'))->send(new LWDConfirmationMailToHR($resignation, $manager, Auth::user()));

        ExitProcess::where('employee_id', $resignation->employee->employee_id)->update(['manager_acknowledged' => 1]);

        return redirect()->route('resignations.manager.pending_resignations')->with('success', 'Resignation accepted and last working date assigned.');
    }


    // Decline the resignation
    public function decline(Request $request, $id)
    {

        $request->validate([
            'resignation_reason' => 'required|string',
        ]);
        $resignation = Resignation::findOrFail($id);
        $resignation->status = 'Declined';
        $resignation->resignation_reason = $request->resignation_reason; // ✅ correct column name
        $resignation->save();

        ExitProcess::where('employee_id', $resignation->employee->employee_id)
            ->update(['manager_acknowledged' => 1]);

        // ✅ Send email to employee
        $employee = $resignation->employee;
        $manager = $employee->manager; // Assuming you have this relationship

        if ($employee && $manager) {
            Mail::to($employee->employee_email)
                ->send(new ResignationDeclinedNotification($resignation, $manager, $employee));
        }

        return redirect()
            ->route('resignations.manager.pending_resignations')
            ->with('success', 'Resignation declined.');
    }

    public function interview_process()
    {
        // Fetch resignations where 'manager_last_working_date' is filled
        $resignations = Resignation::with('employee') // Assuming there's a relationship defined
            ->whereNotNull('manager_last_working_day')
            ->get();

        // Fetch interview questions
        $interviewQuestions = InterviewQuestion::all();

        return view('hr.interview', compact('resignations', 'interviewQuestions'));
    }

    // Show interview questions and employee details for the specific resignation
    public function start_interview_process($id)
    {
        $resignation = Resignation::with('employee')->findOrFail($id);
        $departments = Departments::all();
        $interviewQuestions = InterviewQuestion::all();
        $interviewAnswers = InterviewResponse::where('employee_id', $resignation->employee_id)->get();
        $exitProcess = ExitProcess::where('employee_id', $resignation->employee_id)->first();

        return view('hr.interview-process', compact('resignation', 'interviewQuestions', 'departments', 'interviewAnswers', 'exitProcess'));
    }

    public function submit_lwd_confirmation(Request $request, $id)
    {
        $resignation = Resignation::findOrFail($id);
        $resignation->lwd_confirmation = $request->input('lwd_confirmation');
        $resignation->save();

        $employee = $resignation->employee;
        $manager = $resignation->employee->manager;
        $hr = Auth::user();

        Mail::to($employee->employee_email)->send(new LWDConfirmationMail($employee, $manager, $hr));

        return redirect()->route('resignations.interview')->with('success', 'LWD confirmation submitted successfully.');
    }

    public function FarewellEmail()
    {
        $resignations = Resignation::all();
        return view('hr.farewell', compact('resignations'));
    }

    public function FarewellEmail_send($id)
    {
        $resignation = Resignation::findOrFail($id);
        $employee = Employee::where('employee_id', $resignation->employee_id)->first();
        Mail::to($resignation->employee->employee_email)->send(new FarewellEmail($resignation, $employee));

        ExitProcess::where('employee_id', $resignation->employee->employee_id)->update(['farewell_mail_sent' => 1]);

        return redirect()->route('resignations.interview')->with('success', 'Farewell email sent successfully.');
    }
}
