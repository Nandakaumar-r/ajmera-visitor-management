<?php

namespace App\Http\Controllers;

use App\Models\ShiftRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftRequestController extends Controller
{
    public function index()
    {
        $shiftRequests = ShiftRequest::with(['user', 'approver'])
            ->when(auth()->user()->hasRole('employee'), function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('employees.shift-requests.index', compact('shiftRequests'));
    }

    public function create()
    {
        $timeSlots = $this->getTimeSlots();
        return view('employees.shift-requests.create', compact('timeSlots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'current_start_time' => 'required|date_format:H:i',
            'current_end_time' => 'required|date_format:H:i|after:current_start_time',
            'requested_start_time' => 'required|date_format:H:i',
            'requested_end_time' => 'required|date_format:H:i|after:requested_start_time',
            'effective_from' => 'required|date|after:today',
            'reason' => 'required|string|max:500',
        ]);

        // Convert times to full datetime for storage
        $baseDate = now()->format('Y-m-d');
        $validated['current_start_time'] = Carbon::parse($baseDate . ' ' . $validated['current_start_time']);
        $validated['current_end_time'] = Carbon::parse($baseDate . ' ' . $validated['current_end_time']);
        $validated['requested_start_time'] = Carbon::parse($baseDate . ' ' . $validated['requested_start_time']);
        $validated['requested_end_time'] = Carbon::parse($baseDate . ' ' . $validated['requested_end_time']);

        $shiftRequest = ShiftRequest::create([
            'user_id' => auth()->id(),
            'current_start_time' => $validated['current_start_time'],
            'current_end_time' => $validated['current_end_time'],
            'requested_start_time' => $validated['requested_start_time'],
            'requested_end_time' => $validated['requested_end_time'],
            'effective_from' => $validated['effective_from'],
            'reason' => $validated['reason'],
            'status' => 'pending'
        ]);

        return redirect()
            ->route('employees.shift-requests.index')
            ->with('success', 'Shift change request submitted successfully.');
    }

    public function show(ShiftRequest $shiftRequest)
    {
        $this->authorize('view', $shiftRequest);
        return view('employees.shift-requests.show', compact('shiftRequest'));
    }

    public function update(Request $request, ShiftRequest $shiftRequest)
    {
        $this->authorize('update', $shiftRequest);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
        ]);

        $shiftRequest->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // If approved, you might want to update the user's actual shift schedule here
        // This would depend on your specific implementation of shift management

        return redirect()
            ->route('employees.shift-requests.index')
            ->with('success', 'Shift change request ' . $validated['status'] . ' successfully.');
    }

    public function destroy(ShiftRequest $shiftRequest)
    {
        $this->authorize('delete', $shiftRequest);
        $shiftRequest->delete();

        return redirect()
            ->route('employees.shift-requests.index')
            ->with('success', 'Shift change request cancelled successfully.');
    }

    protected function getTimeSlots()
    {
        $slots = [];
        $start = Carbon::createFromTime(0, 0, 0);
        $end = Carbon::createFromTime(23, 30, 0);

        while ($start <= $end) {
            $slots[$start->format('H:i')] = $start->format('h:i A');
            $start->addMinutes(30);
        }

        return $slots;
    }
}
