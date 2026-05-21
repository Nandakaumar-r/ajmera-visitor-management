<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\VisitorLog;
use App\Services\VisitingCardOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class VisitorController extends Controller
{
    protected $ocrService;

    public function __construct(VisitingCardOcrService $ocrService)
    {
        $this->ocrService = $ocrService;
    }
    public function index(Request $request)
    {
        $query = Visitor::with(['creator', 'approver'])
            ->orderBy('created_at', 'desc');

        // Apply search filter if input is provided
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }


        $visitors = $query->paginate(10);

        if (auth()->user()->hasRole('reception')) {
            return view('visitors.index', compact('visitors'))
                ->layout('layouts.reception');
        }

        return view('visitors.index', compact('visitors'));
    }

    public function create()
    {
        if (auth()->user()->hasRole('reception')) {
            return view('visitors.create')
                ->layout('layouts.reception');
        }

        return view('visitors.create');
    }

    private function saveBase64Image($base64Data, $visitorName)
    {
        try {
            // Remove data URI scheme prefix
            $base64String = preg_replace('#^data:image/\w+;base64,#i', '', $base64Data);
            $imageData = base64_decode($base64String);

            // Create directory structure: storage/app/public/visitors/YYYY-MM-DD/visitor-name/
            $dateFolder = now()->format('Y-m-d');
            $visitorFolder = Str::slug($visitorName);
            $directory = "visitors/{$dateFolder}/{$visitorFolder}";

            // Generate unique filename
            $filename = uniqid() . '.jpg';
            $path = $directory . '/' . $filename;

            // Ensure storage directory exists
            Storage::disk('public')->makeDirectory($directory);

            // Save the file
            Storage::disk('public')->put($path, $imageData);

            return $path;
        } catch (\Exception $e) {
            Log::error('Error saving base64 image', [
                'error' => $e->getMessage(),
                'directory' => $directory ?? 'not_set'
            ]);
            throw $e;
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Visitor store request received', [
                'request_data' => $request->except(['photo', 'visiting_card', 'signature'])
            ]);

            // Validate personal information (required fields)
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone_number' => 'required|string|max:20',
                'purpose' => 'required|string|max:255',
                'whom_to_visit' => 'required|string|max:255',
                'government_id_type' => 'required|string',
                'government_id_last_digits' => 'required|string|max:10',
                'company' => 'nullable|string|max:255',
            ]);

            Log::info('Validation passed', ['validated_data' => $validatedData]);

            // Create visitor record
            $visitor = new Visitor();
            $visitor->first_name = $validatedData['first_name'];
            $visitor->last_name = $validatedData['last_name'];
            $visitor->email = $request->email;
            $visitor->phone_number = $validatedData['phone_number'];
            $visitor->purpose_of_visit = $validatedData['purpose'];
            $visitor->whom_to_visit = $validatedData['whom_to_visit'];
            $visitor->government_id_type = $validatedData['government_id_type'];
            $visitor->government_id_last_digits = $validatedData['government_id_last_digits'];
            $visitor->company = $validatedData['company'];
            $visitor->check_in_time = now();
            $visitor->created_by = Auth::id();

            // Get visitor's full name for directory structure
            $visitorName = $validatedData['first_name'] . ' ' . $validatedData['last_name'];

            // Handle photo if provided
            if ($request->filled('photo')) {
                Log::info('Processing photo');
                try {
                    $visitor->photo_path = $this->saveBase64Image($request->photo, $visitorName);
                    Log::info('Photo saved successfully', ['path' => $visitor->photo_path]);
                } catch (\Exception $e) {
                    Log::error('Error saving photo', ['error' => $e->getMessage()]);
                }
            }

            // Handle visiting card if provided
            if ($request->filled('visiting_card')) {
                Log::info('Processing visiting card');
                try {
                    $visitor->visiting_card_path = $this->saveBase64Image($request->visiting_card, $visitorName);
                    Log::info('Visiting card saved successfully', ['path' => $visitor->visiting_card_path]);
                } catch (\Exception $e) {
                    Log::error('Error saving visiting card', ['error' => $e->getMessage()]);
                }
            }

            // Handle signature if provided
            if ($request->filled('signature')) {
                Log::info('Processing signature');
                try {
                    $visitor->signature_path = $this->saveBase64Image($request->signature, $visitorName);
                    Log::info('Signature saved successfully', ['path' => $visitor->signature_path]);
                } catch (\Exception $e) {
                    Log::error('Error saving signature', ['error' => $e->getMessage()]);
                }
            }

            // Save visitor
            $visitor->save();
            Log::info('Visitor saved successfully', ['visitor_id' => $visitor->id]);

            // Send notifications
            $personToVisit = \App\Models\User::where('name', $visitor->whom_to_visit)->first();
            $hrUsers = \App\Models\User::role('hr')->get();

            // Notify person to visit
            if ($personToVisit) {
                $personToVisit->notify(new \App\Notifications\NewVisitorNotification($visitor));
            }

            // Notify HR users
            foreach ($hrUsers as $hrUser) {
                $hrUser->notify(new \App\Notifications\NewVisitorNotification($visitor));
            }

            // Create visitor log
            VisitorLog::create([
                'visitor_id' => $visitor->id,
                'action' => 'created',
                'user_id' => Auth::id()
            ]);

            return redirect()->route('visitors.index')
                ->with('success', 'Visitor registered successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['photo', 'visiting_card', 'signature'])
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error registering visitor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['photo', 'visiting_card', 'signature'])
            ]);
           return redirect()->route('visitors.index')
                ->with('error', 'Error registering visitor: ' . $e->getMessage());
        }
    }

    public function processVisitingCard(Request $request)
    {
        try {
            if (!$request->hasFile('visiting_card')) {
                return response()->json(['error' => 'No visiting card image provided'], 400);
            }

            $result = $this->ocrService->processVisitingCard($request->file('visiting_card'));

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error in processVisitingCard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to process visiting card',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Visitor $visitor)
    {
        $visitor->load(['creator', 'approver', 'logs.user']);

        if (auth()->user()->hasRole('reception')) {
            return view('visitors.show', compact('visitor'))
                ->layout('layouts.reception');
        }

        return view('visitors.show', compact('visitor'));
    }

    public function approve(Visitor $visitor)
    {
        if ($visitor->status !== 'pending') {
            return back()->with('error', 'This visit request has already been processed.');
        }

        $visitor->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        VisitorLog::create([
            'visitor_id' => $visitor->id,
            'user_id' => Auth::id(),
            'action' => 'approved',
            'description' => 'Visit request approved',
            'old_values' => ['status' => 'pending'],
            'new_values' => ['status' => 'approved'],
        ]);

        return back()->with('success', 'Visit request approved successfully.');
    }

    public function reject(Request $request, Visitor $visitor)
    {
        if ($visitor->status !== 'pending') {
            return back()->with('error', 'This visit request has already been processed.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $visitor->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),
        ]);

        VisitorLog::create([
            'visitor_id' => $visitor->id,
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'description' => 'Visit request rejected',
            'old_values' => ['status' => 'pending'],
            'new_values' => [
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
            ],
        ]);

        return back()->with('success', 'Visit request rejected successfully.');
    }

    public function checkout(Visitor $visitor)
    {
        if ($visitor->check_out_time) {
            return back()->with('error', 'Visitor has already checked out.');
        }

        $visitor->update([
            'check_out_time' => now(),
        ]);

        VisitorLog::create([
            'visitor_id' => $visitor->id,
            'user_id' => Auth::id(),
            'action' => 'checked_out',
            'description' => 'Visitor checked out',
            'new_values' => ['check_out_time' => now()],
        ]);

        return back()->with('success', 'Visitor checked out successfully.');
    }

    public function hrDashboard(Request $request)
    {
        // Apply date filter
        $query = Visitor::query();

        switch ($request->get('date_filter', 'today')) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
                // 'all' doesn't need any date filtering
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('purpose_of_visit', 'like', "%{$search}%");
            });
        }

        // Get filtered visitors
        $visitors = $query->latest()->get();

        // Calculate statistics
        $todayVisitors = Visitor::whereDate('created_at', today())->count();
        $weekVisitors = Visitor::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $pendingVisitors = Visitor::where('status', 'pending')->count();
        $totalVisitors = Visitor::count();

        // Prepare chart data (last 7 days)
        $chartData = [
            'labels' => [],
            'visitors' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData['labels'][] = $date->format('M d');
            $chartData['visitors'][] = Visitor::whereDate('created_at', $date)->count();
        }

        return view('visitors.hr-dashboard', compact(
            'visitors',
            'todayVisitors',
            'weekVisitors',
            'pendingVisitors',
            'totalVisitors',
            'chartData'
        ));
    }
}
