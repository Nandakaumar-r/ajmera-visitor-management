<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\WfhRequest;
use App\Mail\WfhRequestMail;
use App\Mail\WfhStatusMail;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WfhController extends Controller
{
    public function index()
    {
        return view('wfh.apply');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:500',
            'emergency_contact' => 'required|string|max:15',
            'work_location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'equipment_needed' => 'nullable|string|max:255',
            'internet_speed' => 'required|numeric|min:1',
            'backup_plan' => 'required|string|max:255'
        ]);

        $employeeId = Auth::user();
        $employee = Employee::where('employee_email', $employeeId->email)->first();
        
        // Create WFH request
        $wfhRequest = WfhRequest::create([
            'employee_id' => $employee->employee_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'emergency_contact' => $request->emergency_contact,
            'work_location' => $request->work_location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'equipment_needed' => $request->equipment_needed,
            'internet_speed' => $request->internet_speed,
            'backup_plan' => $request->backup_plan,
            'status' => 'pending'
        ]);

        // Send email to manager and HR
        $this->sendNotificationEmail($wfhRequest, $employeeId);

        return redirect()->back()->with('success', 'WFH request submitted successfully. Your manager and HR have been notified.');
    }


private function sendNotificationEmail($wfhRequest, $employee)
{
    Log::info('Employee User ID: ' . $employee->id);
    Log::info('Employee Email: ' . $employee->email);

    // Step 1: Get employee record by matching email
    $employeeRecord = \App\Models\Employee::where('employee_email', $employee->email)->first();

    if (!$employeeRecord) {
        Log::error('No matching employee found for email: ' . $employee->email);
        return;
    }

    Log::info('Employee Record ID: ' . $employeeRecord->id);

    // Step 2: Get manager ID from employee table
    $managerId = $employeeRecord->manager_id;

    if (!$managerId) {
        Log::error('Manager ID not found for employee ID: ' . $employeeRecord->id);
        return;
    }

    Log::info('Resolved Manager ID: ' . $managerId);

    // Step 3: Find manager record in MANAGERS table
    $managerRecord = \App\Models\Manager::find($managerId);

    if (!$managerRecord) {
        Log::error('No manager record found for ID: ' . $managerId);
        return;
    }

    $managerEmail = $managerRecord->manager_email ?? 'manager@company.com';

    Log::info('Resolved Manager Email: ' . $managerEmail);

    $hrEmail = config('mail.hr_email', 'karteek.kr@fidelisgroup.in');

    // Send the mail
    Mail::to($managerEmail)
        ->cc($hrEmail)
        ->send(new WfhRequestMail($wfhRequest, $employeeRecord));
}



    public function approve($id)
{
    $wfhRequest = WfhRequest::findOrFail($id);
    
    $wfhRequest->update([
        'status' => 'approved',
        'approved_by' => Auth::id(),
        'approved_at' => now()
    ]);
    
    // Send approval notification email to employee
    Mail::to($wfhRequest->employee->email)->send(new WfhStatusMail($wfhRequest, 'approved'));
    
    return redirect()->back()->with('success', 'WFH request approved successfully.');
}

public function reject(Request $request, $id)
{
    $wfhRequest = WfhRequest::findOrFail($id);
    
    $wfhRequest->update([
        'status' => 'rejected',
        'approved_by' => Auth::id(),
        'manager_comments' => $request->input('comments', 'No comments provided')
    ]);
    
    // Send rejection notification email to employee
    Mail::to($wfhRequest->employee->email)->send(new WfhStatusMail($wfhRequest, 'rejected'));
    
    return redirect()->back()->with('success', 'WFH request rejected.');
}
public function showApproveConfirm($id)
{
    $wfhRequest = WfhRequest::findOrFail($id);
    return view('wfh.approve-confirm', compact('wfhRequest'));
}

public function showRejectConfirm($id)
{
    $wfhRequest = WfhRequest::findOrFail($id);
    return view('wfh.reject-confirm', compact('wfhRequest'));
}

public function manageRequests()
{
    $user = Auth::user();
    
    // Get requests for employees under this manager
    $requests = WfhRequest::whereHas('employee', function ($query) use ($user) {
        $query->where('manager_id', $user->id);
    })
    ->orWhere('employee_id', $user->id) // User's own requests
    ->orderBy('created_at', 'desc')
    ->paginate(10);
    
    return view('wfh.manage', compact('requests'));
}

public function myRequests()
{
    $requests = WfhRequest::where('employee_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    return view('wfh.my-requests', compact('requests'));
}
}
