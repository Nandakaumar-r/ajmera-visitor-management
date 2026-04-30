<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use App\Models\CabinBooking;
use App\Models\User;
use App\Models\BookingAttendee;
use App\Notifications\CabinBookingNotification;
use App\Notifications\CabinBookingCancellationNotification;
use App\Services\QrCodeService;
use App\Services\TeamsService;
use App\Mail\BookingExtendedMail;
use App\Mail\BookingCancelledMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CabinBookingController extends Controller
{
    protected $qrCodeService;
    protected $teamsService;

    public function __construct(QrCodeService $qrCodeService, TeamsService $teamsService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->teamsService = $teamsService;
    }

    public function index()
    {
        // check if get c is available
        if(isset($_GET['c'])){
            $selectedCabin = Cabin::findOrFail($_GET['c']);
        }else{
            $selectedCabin = null;
        }
        $bookings = CabinBooking::with(['cabin', 'user', 'attendees.user'])
            ->where('user_id', auth()->id())
            ->orderBy('start_time', 'desc')
            ->get();

        $cabins = Cabin::where('is_active', true)->get();

        return view('cabins.index', compact('bookings', 'cabins', 'selectedCabin'));
    }

    public function calendar()
    {
        $selectedCabin = null;
        if(isset($_GET['c'])){
            $selectedCabin = Cabin::findOrFail($_GET['c']);
        }

        $cabins = Cabin::where('is_active', true)->get();
      
        // Add some dummy data if no bookings exist
        // if (CabinBooking::count() === 0) {
        //     $this->createDummyBookings();
        // }

        $bookings = CabinBooking::with(['cabin', 'user'])
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->cabin->name . ' - ' . $booking->user->name,
                    'start' => $booking->start_time,
                    'end' => $booking->end_time,
                    'description' => $booking->purpose,
                    'backgroundColor' => '#4F46E5',
                    'borderColor' => '#4338CA',
                    'extendedProps' => [
                        'cabin_id' => $booking->cabin_id,
                        'status' => $booking->status,
                        'purpose' => $booking->purpose
                    ]
                ];
            });

        return view('cabins.calendar', compact('bookings', 'cabins'));
    }

    private function createDummyBookings()
    {
        $cabins = Cabin::where('is_active', true)->get();
        $users = User::inRandomOrder()->limit(5)->get();
        
        $purposes = [
            'Team Meeting',
            'Client Call',
            'Interview',
            'Training Session',
            'Project Planning',
            'Performance Review',
            'Sprint Planning',
            'Code Review'
        ];

        // Create bookings for the next 30 days
        for ($i = 0; $i < 20; $i++) {
            $startDate = now()->addDays(rand(1, 30));
            $duration = rand(1, 4); // 1-4 hours

            CabinBooking::create([
                'cabin_id' => $cabins->random()->id,
                'user_id' => $users->random()->id,
                'start_time' => $startDate,
                'end_time' => $startDate->copy()->addHours($duration),
                'purpose' => $purposes[array_rand($purposes)],
                'status' => 'confirmed'
            ]);
        }

        // Create some bookings for today
        for ($i = 0; $i < 3; $i++) {
            $startHour = rand(9, 15);
            $duration = rand(1, 3);

            CabinBooking::create([
                'cabin_id' => $cabins->random()->id,
                'user_id' => $users->random()->id,
                'start_time' => now()->setHour($startHour)->setMinute(0),
                'end_time' => now()->setHour($startHour)->setMinute(0)->addHours($duration),
                'purpose' => $purposes[array_rand($purposes)],
                'status' => 'confirmed'
            ]);
        }
    }

    public function showQrBooking(Cabin $cabin, Request $request)
    {
        if (!$this->qrCodeService->validateQrCode($cabin->id, $request->code)) {
            abort(403, 'Invalid QR code');
        }

        $users = User::all(); // For user selection in the form
        return view('cabins.qr-booking', compact('cabin', 'users'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cabin_id' => 'required|exists:cabins,id',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'purpose' => 'required|string|max:255',
                'attendees' => 'nullable|array',
                'attendees.*' => 'exists:users,id'
            ]);

            // Check if cabin is already booked for the time period
            $isBooked = CabinBooking::where('cabin_id', $validated['cabin_id'])
                ->where(function($query) use ($validated) {
                    $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                        ->orWhere(function($q) use ($validated) {
                            $q->where('start_time', '<=', $validated['start_time'])
                              ->where('end_time', '>=', $validated['end_time']);
                        });
                })
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($isBooked) {
                return response()->json([
                    'success' => false,
                    'message' => 'This cabin is already booked for the selected time period'
                ], 422);
            }

            DB::beginTransaction();
            try {
                // Create the booking
                $booking = CabinBooking::create([
                    'cabin_id' => $validated['cabin_id'],
                    'user_id' => auth()->id(),
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'purpose' => $validated['purpose'],
                    'status' => 'confirmed'
                ]);

                // Create attendees and send notifications if attendees are present
                if (!empty($validated['attendees'])) {
                    foreach ($validated['attendees'] as $userId) {
                        $attendee = $booking->attendees()->create([
                            'user_id' => $userId
                        ]);

                        $user = User::find($userId);
                        if ($user) {
                            $notification = new CabinBookingNotification($booking);
                            
                            // Insert notification directly
                            DB::table('notifications')->insert([
                                'id' => $notification->id,
                                'user_id' => $userId,
                                'type' => 'cabin_booking',
                                'notifiable_type' => User::class,
                                'notifiable_id' => $userId,
                                'data' => json_encode([
                                    'booking_id' => $booking->id,
                                    'cabin_name' => $booking->cabin->name,
                                    'start_time' => $booking->start_time,
                                    'end_time' => $booking->end_time,
                                    'purpose' => $booking->purpose,
                                ]),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully!'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Booking Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking. ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $availableCabins = Cabin::where('is_active', true)
            ->get()
            ->filter(function ($cabin) use ($validated) {
                return $cabin->isAvailable($validated['start_time'], $validated['end_time']);
            })
            ->values();

        return response()->json(['cabins' => $availableCabins]);
    }

    public function destroy(CabinBooking $booking)
    {
        $this->authorize('delete', $booking);
        
        $booking->update(['status' => 'cancelled']);
        
        foreach ($booking->attendees as $attendee) {
            $attendee->user->notify(new CabinBookingCancellationNotification($booking));
        }

        return redirect()->route('bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    public function getBookingDetails(CabinBooking $booking)
    {
        return response()->json([
            'id' => $booking->id,
            'cabin' => [
                'name' => $booking->cabin->name,
                'location' => $booking->cabin->location,
                'capacity' => $booking->cabin->capacity,
            ],
            'user' => [
                'name' => $booking->user->name,
                'email' => $booking->user->email,
            ],
            'start_time' => $booking->start_time->format('Y-m-d H:i'),
            'end_time' => $booking->end_time->format('Y-m-d H:i'),
            'purpose' => $booking->purpose,
            'status' => $booking->status,
            'created_at' => $booking->created_at->format('Y-m-d H:i'),
        ]);
    }

    // Admin methods for QR code management
    public function adminIndex()
    {
        $this->authorize('manage-cabins');
        $cabins = Cabin::all();
        return view('cabins.admin', compact('cabins'));
    }

    public function generateQrCode(Cabin $cabin)
    {
        $this->authorize('manage-cabins');
        
        try {
            $qrCode = app(QrCodeService::class)->generateQrCode($cabin);
            $cabin->qr_code = $qrCode;
            $cabin->save();

            return response()->json([
                'success' => true,
                'qr_image' => $qrCode,
                'message' => 'QR code generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code'
            ], 500);
        }
    }

    public function qrCodes()
    {
        $cabins = Cabin::where('is_active', true)->get();

        // Generate QR codes for cabins that don't have them
        foreach ($cabins as $cabin) {
            if (!$cabin->qr_code) {
                $cabin->qr_code = $this->qrCodeService->generateQrCode($cabin);
                $cabin->save();
            }
        }

        return view('cabins.qr-codes', compact('cabins'));
    }

    /**
     * Extend the booking duration
     */
    public function extendBooking(Request $request, $id)
    {
        $booking = CabinBooking::findOrFail($id);
        
        // Validate request
        $request->validate([
            'additional_hours' => 'required|integer|min:1|max:60'
        ]);

        // Check if cabin is available for extension
        $newEndTime = Carbon::parse($booking->end_time)->addMinutes($request->additional_hours);
        $isAvailable = CabinBooking::where('cabin_id', $booking->cabin_id)
            ->where('id', '!=', $booking->id)
            ->where('start_time', '<', $newEndTime)
            ->where('end_time', '>', $booking->end_time)
            ->doesntExist();

        if (!$isAvailable) {
            return response()->json([
                'message' => 'Cabin is not available for the requested extension period'
            ], 422);
        }

        // Update booking end time
        $booking->end_time = $newEndTime;
        $booking->save();

        // Send notification email
        Mail::to($booking->user->email)->send(new BookingExtendedMail($booking));

        return response()->json([
            'message' => 'Booking extended successfully',
            'booking' => $booking
        ]);
    }

    /**
     * Cancel booking early and release the cabin
     */
    public function cancelEarly(Request $request, $id)
    {
        $booking = CabinBooking::findOrFail($id);
        
        // Check if booking is ongoing or in future
        if (Carbon::parse($booking->end_time)->isPast()) {
            return response()->json([
                'message' => 'Cannot cancel a completed booking'
            ], 422);
        }

        // Calculate refund if cancellation is more than 24 hours before start
        $refundAmount = 0;
        if (Carbon::parse($booking->start_time)->subHours(24)->isFuture()) {
            $refundAmount = $this->calculateRefundAmount($booking);
        }

        // Update booking status
        $booking->status = 'cancelled';
        $booking->cancelled_at = now();
        $booking->save();

        // Send cancellation confirmation email
        Mail::to($booking->user->email)->send(new BookingCancelledMail($booking));

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking
        ]);
    }

    /**
     * Calculate refund amount for early cancellation
     */
    private function calculateRefundAmount($booking)
    {
        // Example: 75% refund if cancelled more than 24 hours before
        return $booking->total_amount * 0.75;
    }

    /**
     * Get booking statistics
     */
    public function getStatistics()
    {
        $statistics = [
            'total_bookings' => CabinBooking::count(),
            'active_bookings' => CabinBooking::where('status', 'active')->count(),
            'cancelled_bookings' => CabinBooking::where('status', 'cancelled')->count(),
            'total_revenue' => CabinBooking::where('status', '!=', 'cancelled')->count('id'),
            'popular_cabins' => CabinBooking::select('cabin_id', DB::raw('count(*) as total_bookings'))
                ->groupBy('cabin_id')
                ->orderByDesc('total_bookings')
                ->limit(5)
                ->get()
        ];

        return response()->json($statistics);
    }

    public function show(CabinBooking $booking)
    {
        return view('cabin-bookings.show', compact('booking'));
    }

    public function updateNotes(Request $request, CabinBooking $booking)
    {
        
        
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:10000'],
        ]);

        $booking->update([
            'notes' => $validated['notes']
        ]);

        return back()->with('status', 'notes-updated');
    }

    public function updateMinutes(Request $request, CabinBooking $booking)
    {
        
        
        $validated = $request->validate([
            'meeting_minutes' => ['required', 'string', 'max:10000'],
        ]);

        $booking->update([
            'meeting_minutes' => $validated['meeting_minutes']
        ]);

        return back()->with('status', 'minutes-updated');
    }

    public function addAttendees(Request $request, CabinBooking $booking)
    {
        
        
        $validated = $request->validate([
            'emails' => ['required', 'string'],
        ]);

        $emails = array_filter(array_map('trim', explode("\n", $validated['emails'])));
        $existingAttendees = $booking->attendees()->pluck('user_id')->toArray();

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();
            if ($user && !in_array($user->id, $existingAttendees)) {
                BookingAttendee::create([
                    'booking_id' => $booking->id,
                    'user_id' => $user->id
                ]);

                $user->notify(new CabinBookingNotification($booking));
            }
        }

        return back()->with('status', 'attendees-added');
    }

    public function removeAttendee(CabinBooking $booking, BookingAttendee $attendee)
    {
        if ($attendee->booking_id === $booking->id) {
            $attendee->delete();
        }

        return back()->with('status', 'attendee-removed');
    }

    public function createTeamsMeeting(CabinBooking $booking)
    {
        try {
            $result = $this->teamsService->createMeeting($booking);

            if (!$result['success']) {
                return back()->with('error', 'Failed to create Teams meeting: ' . $result['error']);
            }

            $booking->update([
                'teams_meeting_id' => $result['meeting_id'],
                'teams_meeting_link' => $result['join_url']
            ]);

            // Send email notifications to attendees
            foreach ($booking->attendees as $attendee) {
                Mail::to($attendee->user->email)->queue(new TeamsMeetingInvitation($booking));
            }

            return back()->with('status', 'teams-meeting-created');
        } catch (\Exception $e) {
            \Log::error('Teams meeting creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create Teams meeting: ' . $e->getMessage());
        }
    }
}
