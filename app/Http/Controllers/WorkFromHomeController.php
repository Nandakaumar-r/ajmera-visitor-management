<?php

namespace App\Http\Controllers;

use App\Models\WorkFromHome;
use Illuminate\Http\Request;

class WorkFromHomeController extends Controller
{



 public function signIn(Request $request)
{
    $request->validate([
        'work_location' => 'required|string|max:255',
        'current_location' => 'nullable|string|max:255',
        'remarks' => 'nullable|string|max:500',
        'captured_photo_path' => 'nullable|string|max:500',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    $userId = auth()->user()->id;

    // Prevent multiple sign-ins without sign-out
    $existing = WorkFromHome::where('user_id', $userId)
        ->whereNull('sign_out_time')
        ->first();

    if ($existing) {
        return response()->json(['success' => false, 'message' => 'You are already signed in. Please sign out first.']);
    }

    WorkFromHome::create([
        'user_id'   => $userId,
        'work_location' => $request->work_location,
        'current_location' => $request->current_location,
        'remarks'       => $request->remarks,
        'captured_photo_path' => $request->captured_photo_path,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'sign_in_time'  => now(),
    ]);

    return response()->json(['success' => true, 'message' => 'Sign-In successful']);
}


    public function signOut()
    {
        $userId = auth()->user()->id;

        $record = WorkFromHome::where('user_id', $userId)
            ->whereNull('sign_out_time')
            ->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'No active sign-in found.']);
        }

        $record->update(['sign_out_time' => now()]);

        return response()->json(['success' => true, 'message' => 'Sign-Out successful']);
    }

    public function samples()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $records = WorkFromHome::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->get(['sign_in_time', 'sign_out_time', 'work_location', 'remarks']);

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

public function index(Request $request)
{
    $query = WorkFromHome::with('user')->orderBy('created_at', 'desc');

    // Search filter
    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    // Date filter
    if ($request->filled('filter')) {
        switch ($request->filter) {
            case 'today':
                $query->whereDate('created_at', now()->toDateString());
                break;
            case 'yesterday':
                $query->whereDate('created_at', now()->subDay()->toDateString());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'last_month':
                $query->whereBetween('created_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ]);
                break;
        }
    }

    $records = $query->paginate(10);
    $records->appends($request->except('page'));

    // Extract coordinates for map markers
    $locations = $records->map(function ($record) {
        return [
            'name'      => $record->user->name ?? 'N/A',
            'location'  => $record->current_location ?? 'Unknown',
            'latitude'  => $record->latitude,
            'longitude' => $record->longitude,
        ];
    });

    return view('wfh.index', compact('records', 'locations'));
}


    public function exportCsv(Request $request)
    {
        $fileName = 'wfh_records_' . now()->format('Y-m-d') . '.csv';

        $query = WorkFromHome::with('user')->orderBy('created_at', 'desc');

        // Apply search
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply date filter
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;

                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;

                case 'month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;

                case 'last_month':
                    $query->whereBetween('created_at', [
                        now()->subMonth()->startOfMonth(),
                        now()->subMonth()->endOfMonth()
                    ]);
                    break;
            }
        }

        $records = $query->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, [
                'ID',
                'Name',
                'Email',
                'Work Type',
                'Signed In Location',
                'Client Name',
                'Sign In',
                'Sign Out',
                'Photo'
            ]);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->id,
                    $record->user->name ?? 'N/A',
                    $record->user->email ?? 'N/A',
                    $record->work_location,
                    $record->current_location,
                    $record->remarks ?? '-',
                    $record->sign_in_time,
                    $record->sign_out_time ?? 'Not Signed Out',
                    $record->captured_photo_path ? asset('storage/' . $record->captured_photo_path) : 'No Photo'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function uploadPhoto(Request $request)
    {
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('wfh_photos', 'public');
            return response()->json([
                'path' => $path,
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
