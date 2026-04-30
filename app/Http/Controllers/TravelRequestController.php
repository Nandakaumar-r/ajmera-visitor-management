<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TravelRequest;
use App\Models\Traveler;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Gate;
use App\Notifications\TravelRequestSubmitted;
use App\Notifications\TravelRequestStatusUpdated;

class TravelRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $travelRequests = TravelRequest::where('user_id', $user->id)
            ->with(['travelers', 'manager', 'reimbursements'])
            ->latest()
            ->paginate(10);

        return view('travel.index', compact('travelRequests'));
    }

    public function create()
    {
        if (! Gate::allows('create', TravelRequest::class)) {
            abort(403);
        }

        $user = Auth::user();
        $employee = Employee::where('employee_email', $user->email)->first();
        $manager = $employee ? Employee::find($employee->manager_id) : null;

        return view('travel.create', compact('employee', 'manager'));
    }

    public function store(Request $request)
    {
        if (! Gate::allows('create', TravelRequest::class)) {
            abort(403);
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'transport_mode' => 'required|string',
            'number_of_travelers' => 'required|integer|min:1',
            'destination' => 'required|string',
            'travel_reason' => 'required|string',
            'accommodation_details' => 'nullable|array',
            'estimated_cost' => 'required|numeric|min:0',
            'is_international' => 'boolean',
            'travelers' => 'required|array|min:1',
            'travelers.*.name' => 'required|string',
            'travelers.*.age' => 'nullable|integer',
            'travelers.*.passport_number' => 'required_if:is_international,true',
            'travelers.*.passport_expiry' => 'required_if:is_international,true|date',
            'travelers.*.employee_id' => 'nullable|string',
        ]);

        // Get the employee and their manager
        $user = Auth::user();
        $employee = Employee::where('employee_email', $user->email)->first();
        $manager = $employee ? Employee::where('employee_id', $employee->manager_id)->first() : null;
        $managerUser = $manager ? User::where('email', $manager->employee_email)->first() : null;

        $travelRequest = TravelRequest::create([
            'user_id' => Auth::id(),
            'manager_id' => $managerUser ? $managerUser->id : null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'transport_mode' => $validated['transport_mode'],
            'number_of_travelers' => $validated['number_of_travelers'],
            'destination' => $validated['destination'],
            'travel_reason' => $validated['travel_reason'],
            'accommodation_details' => $validated['accommodation_details'] ?? [],
            'estimated_cost' => $validated['estimated_cost'],
            'is_international' => $validated['is_international'] ?? false,
            'is_group_travel' => count($validated['travelers']) > 1,
            'status' => 'pending_manager',
        ]);

        foreach ($validated['travelers'] as $travelerData) {
            $travelRequest->travelers()->create($travelerData);
        }

        // Notify manager if exists
        if ($managerUser) {
            Notification::send($managerUser, new TravelRequestSubmitted($travelRequest));
        }

        return redirect()->route('travel.index')
            ->with('success', 'Travel request submitted successfully.');
    }

    public function show(TravelRequest $travelRequest)
    {
        if (! Gate::allows('view', $travelRequest)) {
            abort(403);
        }
        
        $travelRequest->load(['travelers', 'manager', 'cfo', 'reimbursements']);
        
        return view('travel.show', compact('travelRequest'));
    }

    public function approveManager(TravelRequest $travelRequest, Request $request)
    {
        if (! Gate::allows('approveAsManager', $travelRequest)) {
            abort(403);
        }

        $validated = $request->validate([
            'comments' => 'nullable|string',
        ]);

        $travelRequest->update([
            'status' => 'pending_cfo',
            'manager_approved_at' => now(),
            'manager_comments' => $validated['comments'],
        ]);

        // Notify CFO and employee
        $cfo = User::find($travelRequest->cfo_id);
        Notification::send($cfo, new TravelRequestStatusUpdated($travelRequest));
        Notification::send($travelRequest->user, new TravelRequestStatusUpdated($travelRequest));

        return redirect()->back()->with('success', 'Travel request approved and forwarded to CFO.');
    }

    public function approveCFO(TravelRequest $travelRequest, Request $request)
    {
        if (! Gate::allows('approveAsCFO', $travelRequest)) {
            abort(403);
        }

        $validated = $request->validate([
            'comments' => 'nullable|string',
        ]);

        $travelRequest->update([
            'status' => 'approved',
            'cfo_approved_at' => now(),
            'cfo_comments' => $validated['comments'],
        ]);

        // Notify employee and manager
        Notification::send($travelRequest->user, new TravelRequestStatusUpdated($travelRequest));
        Notification::send($travelRequest->manager, new TravelRequestStatusUpdated($travelRequest));

        return redirect()->back()->with('success', 'Travel request approved.');
    }

    public function reject(TravelRequest $travelRequest, Request $request)
    {
        if (! Gate::allows('reject', $travelRequest)) {
            abort(403);
        }

        $validated = $request->validate([
            'comments' => 'required|string',
        ]);

        $travelRequest->update([
            'status' => 'rejected',
            'manager_comments' => Auth::id() === $travelRequest->manager_id ? $validated['comments'] : null,
            'cfo_comments' => Auth::id() === $travelRequest->cfo_id ? $validated['comments'] : null,
        ]);

        // Notify relevant parties
        Notification::send($travelRequest->user, new TravelRequestStatusUpdated($travelRequest));

        return redirect()->back()->with('success', 'Travel request rejected.');
    }

    public function updateBooking(TravelRequest $travelRequest, Request $request)
    {
        if (! Gate::allows('updateBooking', $travelRequest)) {
            abort(403);
        }

        $validated = $request->validate([
            'booking_details' => 'required|array',
            'booking_details.transport_confirmation' => 'required|string',
            'booking_details.accommodation_confirmation' => 'nullable|string',
            'booking_details.provider_contact' => 'required|string',
            'actual_cost' => 'required|numeric|min:0',
        ]);

        $travelRequest->update([
            'booking_details' => $validated['booking_details'],
            'actual_cost' => $validated['actual_cost'],
            'status' => 'booked',
        ]);

        // Notify employee
        Notification::send($travelRequest->user, new TravelRequestStatusUpdated($travelRequest));

        return redirect()->back()->with('success', 'Booking details updated successfully.');
    }

    public function adminDashboard()
    {
        // Get all travel requests with relationships
        $travelRequests = TravelRequest::with(['user', 'manager', 'cfo', 'travelers'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total' => TravelRequest::count(),
            'pending' => TravelRequest::whereIn('status', ['pending_manager', 'pending_cfo'])->count(),
            'approved' => TravelRequest::where('status', 'approved')->count(),
            'booked' => TravelRequest::where('status', 'booked')->count(),
            'rejected' => TravelRequest::where('status', 'rejected')->count(),
        ];

        return view('travel.admin', compact('travelRequests', 'stats'));
    }
}
