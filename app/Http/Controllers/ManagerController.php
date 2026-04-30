<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Models\Resignation;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = Manager::all();
        return view('managers.index', compact('managers'));
    }

    public function create()
    {
        return view('managers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email|unique:managers,manager_email',
        ]);

        Manager::create($validated);

        return redirect()->route('managers.index')->with('success', 'Manager added successfully');
    }

    public function edit($id)
    {
        $manager = Manager::findOrFail($id);
        return view('managers.edit', compact('manager'));
    }

    public function update(Request $request, $id)
    {
        $manager = Manager::findOrFail($id);

        $validated = $request->validate([
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email|unique:managers,manager_email,' . $manager->id,
        ]);

        $manager->update($validated);

        return redirect()->route('managers.index')->with('success', 'Manager updated successfully');
    }

    public function destroy($id)
    {
        Manager::findOrFail($id)->delete();
        return redirect()->route('managers.index')->with('success', 'Manager deleted successfully');
    }

    public function resignations()
    {
        $resignations = Resignation::with('employee')->whereHas('employee', function ($query) {
            $query->where('manager_id', Auth::user()->id);
        })->get();

        return view('manager.resignations', compact('resignations'));
    }

    public function pendingAttendance()
    {
        $user = auth()->user();
        $pendingAttendances = Attendance::with('employee.user')
            ->whereHas('employee', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })
            ->where('status', 'pending')
            ->get();

        return view('manager.attendance.pending', compact('pendingAttendances'));
    }

    public function approveAttendance(Attendance $attendance)
    {
        $user = auth()->user();

        // Check if the attendance belongs to one of the manager's subordinates
        if ($attendance->employee->manager_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $attendance->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Attendance approved successfully.');
    }

    public function rejectAttendance(Request $request, Attendance $attendance)
    {
        $user = auth()->user();

        // Check if the attendance belongs to one of the manager's subordinates
        if ($attendance->employee->manager_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $attendance->update([
            'status' => 'rejected',
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason']
        ]);

        return redirect()->back()->with('success', 'Attendance rejected successfully.');
    }

    public function pendingLeaves()
    {
        $leaves = Leave::with(['user', 'user.employee'])
            ->whereHas('user.employee', function ($query) {
                $query->where('manager_id', Auth::id());
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('manager.leaves.pending', compact('leaves'));
    }

    public function approveLeave(Request $request, Leave $leave)
    {
        // Verify the leave belongs to an employee managed by this manager
        if ($leave->user->employee->manager_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave request approved successfully.');
    }

    public function rejectLeave(Request $request, Leave $leave)
    {
        // Verify the leave belongs to an employee managed by this manager
        if ($leave->user->employee->manager_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $leave->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason']
        ]);

        return back()->with('success', 'Leave request rejected successfully.');
    }
}
