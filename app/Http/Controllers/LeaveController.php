<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LeaveSubmitted;
use App\Services\LeaveService;
use Illuminate\Http\Request;


class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index()
    {
        $leaves = Leave::where('user_id', auth()->id())->latest()->get();
        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $user = auth()->user();
        $leaveBreakdown = collect($this->leaveService->getLeaveBreakdown($user));
        
        return view('leaves.create', compact('leaveBreakdown'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_type' => 'required',
            'reason' => 'required',
            'session_1' => 'nullable',
            'session_2' => 'nullable',
            'contact_details' => 'required',
        ]);

        if ($validated['from_date'] > $validated['to_date']) {
            return redirect()->back()->with('error', 'Start date must be before end date.');
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        // Check if already applied for leave on this date
        $existingLeave = Leave::where('user_id', auth()->id())
            ->whereBetween('from_date', [$validated['from_date'], $validated['to_date']])
            ->first();

        if ($existingLeave) {
            return redirect()->back()->with('error', 'You have already applied for leave on this date.');
        }

        // Check if employee has enough leave balance
        $leaveBreakdown = collect($this->leaveService->getLeaveBreakdown(auth()->user()));
        $leaveType = LeaveType::where('code', $validated['leave_type'])->first();

        if (!$leaveType) {
            return redirect()->back()->with('error', 'Invalid leave type selected.');
        }

        if (!$leaveBreakdown->has('leave_details_by_type')) {
            Log::error('Leave details by type is missing from breakdown', ['breakdown' => $leaveBreakdown]);
            return redirect()->back()->with('error', 'Unable to verify leave balance. Please contact support.');
        }

        // if ($leaveBreakdown['leave_details_by_type']->isEmpty()) {
        //     Log::warning('No leave types found for user');
        //     return redirect()->back()->with('error', 'No leave types are configured for your account.');
        // }

        $leave = Leave::create($validated);

        // Notify Employee
        Notification::send($leave->user, new LeaveSubmitted($leave, true));

        // Notify HR
        $hrUsers = User::role('hr')->get();
        Notification::send($hrUsers, new LeaveSubmitted($leave, false));

        // Notify Manager
        $employee = Employee::where('employee_email', $leave->user->email)->first();
        if ($employee && $employee->manager) {
            $managerEmail = $employee->manager->email;
            
            Notification::route('mail', $managerEmail)->notify(new LeaveSubmitted($leave, false));
        }

        // Update leave Balance
        $this->leaveService->updateLeaveBalance($leave);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    public function show($leaveId)
    {
        $leave = Leave::findOrFail($leaveId);
        return view('leaves.show', compact('leave'));
    }

    // New methods for manager functionality
    public function pendingApprovals()
    {
        // Get leaves from team members that are pending approval
        $pendingLeaves = Leave::whereHas('user', function($query) {
            $query->where('manager_id', auth()->id());
        })->where('status', 'pending')->latest()->get();

        return view('leaves.pending-approvals', compact('pendingLeaves'));
    }

    public function teamLeaves()
    {
        // Get all leaves from team members
        $teamLeaves = Leave::whereHas('user', function($query) {
            $query->where('manager_id', auth()->id());
        })->latest()->get();

        return view('leaves.team-leaves', compact('teamLeaves'));
    }

    public function approve($leaveId)
    {
        $leave = Leave::findOrFail($leaveId);
        
        // Check if the authenticated user is the manager of the leave applicant
        if ($leave->user->manager_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Leave request approved successfully');
    }

    public function reject(Request $request, $leaveId)
    {
        $leave = Leave::findOrFail($leaveId);
        
        // Check if the authenticated user is the manager of the leave applicant
        if ($leave->user->manager_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $leave->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_by' => auth()->id(),
            'rejected_at' => now()
        ]);

        return redirect()->back()->with('success', 'Leave request rejected successfully');
    }

    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
        
        // Check if the authenticated user owns this leave request
        if ($leave->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deletion if the leave is in pending status
        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leave requests can be deleted.');
        }

        $leave->delete();
        return redirect()->route('leaves.index')->with('success', 'Leave request deleted successfully');
    }

    public function bulkImportForm()
    {
        return view('leaves.bulk-import');
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $leaves = [];
        $validationErrors = [];
        $row = 1;
        
        if (($handle = fopen($path, "r")) !== FALSE) {
            // Skip header row
            $header = fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $row++;
                
                // Validate required columns
                if (count($data) < 7) {
                    $validationErrors[] = "Row {$row}: Missing required columns";
                    continue;
                }
                
                try {
                    [$employee_email, $leave_type, $from_date, $to_date, $session_1, $session_2, $reason] = $data;
                    
                    // Find user by email
                    $user = \App\Models\User::where('email', $employee_email)->first();
                    if (!$user) {
                        $validationErrors[] = "Row {$row}: Employee with email {$employee_email} not found";
                        continue;
                    }
                    
                    // Validate dates
                    if (!strtotime($from_date) || !strtotime($to_date)) {
                        $validationErrors[] = "Row {$row}: Invalid date format";
                        continue;
                    }
                    
                    // Create leave record
                    Leave::create([
                        'user_id' => $user->id,
                        'leave_type' => $leave_type,
                        'from_date' => $from_date,
                        'to_date' => $to_date,
                        'session_1' => $session_1,
                        'session_2' => $session_2,
                        'reason' => $reason,
                        'status' => 'approved', // Since HR is importing, we'll mark it as approved
                        'contact_details' => $user->phone ?? 'N/A'
                    ]);
                    
                    // Notify the employee
                    $user->notify(new \App\Notifications\LeaveApproved());
                    
                } catch (\Exception $e) {
                    $validationErrors[] = "Row {$row}: " . $e->getMessage();
                }
            }
            fclose($handle);
        }
        
        if (count($validationErrors) > 0) {
            return redirect()->route('leaves.bulk-import')
                ->with('warning', 'Some records could not be imported.')
                ->withErrors($validationErrors);
        }
        
        return redirect()->route('leaves.bulk-import')
            ->with('success', 'Leaves imported successfully!');
    }
}
