<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Services\EsslService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    protected $esslService;
    protected $similarityThreshold = 0.7;

    public function __construct(EsslService $esslService)
    {
        $this->esslService = $esslService;
    }

    public function index(Request $request)
    {
        $employee = $request->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee record not found.');
        }

        $date = Carbon::now();
        $currentMonth = $date->month;
        $currentYear = $date->year;
        $daysInMonth = $date->daysInMonth;

        // Get all attendance records for the current month
        $attendances = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('employee_id', $employee->employee_id)
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Calculate monthly statistics
        $stats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'wfh' => $attendances->where('work_mode', 'wfh')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
        ];

        // Get today's attendance for check-in/out buttons
        $todayAttendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', Carbon::today())
            ->first();

        return view('attendance.index', compact('attendances', 'stats', 'daysInMonth', 'date', 'todayAttendance'));
    }

    public function create(Request $request)
    {
        $employee = $request->employee;
        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'Employee record not found.');
        }

        $date = Carbon::now();
        $currentMonth = $date->month;
        $currentYear = $date->year;
        
        // Get all attendance records for the current month
        $attendances = Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('employee_id', $employee->employee_id)
            ->orderBy('date')
            ->get();

        // Calculate statistics for the current month
        $stats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'wfh' => $attendances->where('work_mode', 'wfh')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
            'leave' => $attendances->where('status', 'leave')->count(),
        ];

        // Get eSSL attendance data for today
        $todayEsslData = $this->esslService->getAttendanceData($employee->employee_id, Carbon::today());

        return view('attendance.create', compact('attendances', 'stats', 'todayEsslData'));
    }

    public function store(Request $request)
    {
        $employee = $request->employee;
        $date = Carbon::now();

        // Validate the request
        $request->validate([
            'work_mode' => 'required|in:office,wfh',
            'start_time' => 'required_if:work_mode,wfh|date_format:H:i',
            'end_time' => 'required_if:work_mode,wfh|date_format:H:i|after:start_time',
            'remarks' => 'nullable|string|max:500',
        ]);

        // For office attendance, get timing from eSSL
        if ($request->work_mode === 'office') {
            $esslData = $this->esslService->getAttendanceData($employee->employee_id, $date);
            if (!$esslData) {
                return back()->with('error', 'Unable to fetch office attendance data. Please try again or contact IT support.');
            }
            $startTime = $esslData['first_in'] ?? null;
            $endTime = $esslData['last_out'] ?? null;
        } else {
            // For WFH, use manually entered time
            $startTime = $request->start_time;
            $endTime = $request->end_time;
        }

        // Calculate work hours
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);
        $totalHours = $end->diffInHours($start);

        // Create or update attendance record
        Attendance::updateOrCreate(
            [
                'employee_id' => $employee->employee_id,
                'date' => $date->format('Y-m-d'),
            ],
            [
                'work_mode' => $request->work_mode,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'total_hours' => $totalHours,
                'status' => $totalHours >= 8 ? 'present' : 'half_day',
                'remarks' => $request->remarks,
            ]
        );

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function history(Request $request)
    {
        // Employee is now available from the request
        $employee = $request->employee;
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found. Please contact HR to set up your employee profile.');
        }
        
        // Get the month and year from request or use current date
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        
        // Get the first and last day of the month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        // Get all attendance records for the month
        $attendances = Attendance::where('employee_id', $employee->employee_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
            
        // Calculate attendance stats
        $stats = $this->calculateAttendanceStats($attendances);
        
        // Index attendance by date for calendar
        $attendancesByDate = $attendances->keyBy(function($attendance) {
            return $attendance->date->format('Y-m-d');
        });
        
        // Get calendar data
        $calendar = collect();
        $currentDate = $startDate->copy();
        
        // Get the day of week for the first day (0 = Sunday, 6 = Saturday)
        $firstDayOfWeek = $startDate->dayOfWeek;
        
        // Add empty days before the first day of month
        $prevMonthDate = $startDate->copy()->subDays($firstDayOfWeek);
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendar->push([
                'day' => $prevMonthDate->day,
                'date' => $prevMonthDate->format('Y-m-d'),
                'attendance' => null,
                'isCurrentMonth' => false,
                'isWeekend' => $prevMonthDate->isWeekend(),
                'isToday' => $prevMonthDate->isToday()
            ]);
            $prevMonthDate->addDay();
        }
        
        // Add all days of the month
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $calendar->push([
                'day' => $currentDate->day,
                'date' => $dateString,
                'attendance' => $attendancesByDate->get($dateString),
                'isCurrentMonth' => true,
                'isWeekend' => $currentDate->isWeekend(),
                'isToday' => $currentDate->isToday()
            ]);
            $currentDate->addDay();
        }
        
        // Fill remaining days to complete the last week
        $remainingDays = 42 - $calendar->count(); // 42 = 6 rows × 7 days
        $nextMonthDate = $endDate->copy()->addDay();
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar->push([
                'day' => $nextMonthDate->day,
                'date' => $nextMonthDate->format('Y-m-d'),
                'attendance' => null,
                'isCurrentMonth' => false,
                'isWeekend' => $nextMonthDate->isWeekend(),
                'isToday' => $nextMonthDate->isToday()
            ]);
            $nextMonthDate->addDay();
        }
        
        // Get list of months for dropdown
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create(null, $m, 1)->format('F');
        }
        
        // Get list of years (current year and previous 2 years)
        $years = range(date('Y'), date('Y') - 2);
        
        return view('attendance.history', [
            'calendar' => $calendar,
            'month' => $month,
            'year' => $year,
            'months' => $months,
            'years' => $years,
            'stats' => $stats
        ]);
    }

    public function hybrid(Request $request)
    {
        $employee = $request->employee;
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found. Please contact HR to set up your employee profile.');
        }

        // Get today's attendance if it exists
        $todayAttendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', Carbon::today())
            ->first();

        return view('attendance.hybrid-dashboard', [
            'employee' => $employee,
            'todayAttendance' => $todayAttendance,
            'currentDate' => now()->format('d F Y'),
            'currentMode' => $todayAttendance ? $todayAttendance->work_mode : 'WFO',
            'todayHours' => $todayAttendance ? $todayAttendance->actual_work_hours : null,
            'status' => $todayAttendance ? $todayAttendance->status : null,
            'location' => $todayAttendance ? $todayAttendance->location_address : null
        ]);
    }

    public function updateMode(Request $request)
    {
        $request->validate([
            'work_mode' => 'required|in:WFO,WFH'
        ]);

        $employee = $request->employee;
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found. Please contact HR to set up your employee profile.');
        }

        // Update or create today's attendance record with the new work mode
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->employee_id,
                'date' => Carbon::today()
            ],
            [
                'work_mode' => $request->work_mode,
                'status' => 'pending'
            ]
        );

        return redirect()->route('attendance.hybrid')
            ->with('success', 'Work mode updated successfully to ' . $request->work_mode);
    }

    public function logWfh(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $employee = $request->employee;
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found. Please contact HR to set up your employee profile.');
        }

        // Convert times to Carbon instances
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = Carbon::createFromFormat('H:i', $request->end_time);
        
        // Calculate work hours
        $totalWorkHours = $endTime->diffInHours($startTime);
        $breakHours = 1; // 1 hour break
        $actualWorkHours = $totalWorkHours - $breakHours;

        // Update or create today's attendance
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->employee_id,
                'date' => Carbon::today()
            ],
            [
                'work_mode' => 'WFH',
                'first_in' => $startTime,
                'last_out' => $endTime,
                'total_work_hours' => $totalWorkHours,
                'actual_work_hours' => $actualWorkHours,
                'break_hours' => $breakHours,
                'status' => 'pending',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location_address' => $this->getLocationAddress($request->latitude, $request->longitude)
            ]
        );

        return redirect()->route('attendance.hybrid')
            ->with('success', 'WFH attendance logged successfully.');
    }

    public function edit(Request $request, Attendance $attendance)
    {
        // Employee is now available from the request
        $employee = $request->employee;

        // Check if the attendance belongs to the current user
        if ($attendance->employee_id !== $employee->employee_id) {
            abort(403);
        }

        // Check if the attendance is from today
        if (!$attendance->date->isToday()) {
            return redirect()->route('attendance.history')
                ->with('error', 'You can only edit today\'s attendance');
        }

        return view('attendance.edit', [
            'attendance' => $attendance,
            'statuses' => [
                'present' => 'Present',
                'absent' => 'Absent',
                'half_day' => 'Half Day',
                'wfh' => 'Work From Home',
                'leave' => 'Leave'
            ],
            'types' => [
                'regular' => 'Regular',
                'overtime' => 'Overtime',
                'comp_off' => 'Compensatory Off'
            ]
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        // Employee is now available from the request
        $employee = $request->employee;

        // Check if the attendance belongs to the current user
        if ($attendance->employee_id !== $employee->employee_id) {
            abort(403);
        }

        // Check if the attendance is from today
        if (!$attendance->date->isToday()) {
            return redirect()->route('attendance.history')
                ->with('error', 'You can only edit today\'s attendance');
        }

        $request->validate([
            'status' => 'required|in:present,absent,half_day,wfh,leave',
            'type' => 'required|in:regular,overtime,comp_off',
            'reason' => 'nullable|string|max:255'
        ]);

        $attendance->update([
            'status' => $request->status,
            'type' => $request->type,
            'reason' => $request->reason
        ]);

        return redirect()->route('attendance.history')
            ->with('success', 'Attendance updated successfully');
    }

    public function getDashboardStats(Request $request)
    {
        $employee = $request->employee;
        
        if (!$employee) {
            return [
                'error' => 'Employee record not found'
            ];
        }

        // Get current month's attendance
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $today = Carbon::now()->format('Y-m-d');

        // Fetch attendance data for the current month
        $attendances = Attendance::whereMonth('date', $currentMonth)
                                ->whereYear('date', $currentYear)
                                ->where('employee_id', $employee->employee_id)
                                ->orderBy('date')
                                ->get();

        // Get today's attendance
        $todayAttendance = $attendances->where('date', $today)->first();

        // Calculate working days
        $workingDays = Carbon::now()->daysInMonth - Carbon::now()->endOfMonth()->weekendsCount();
        
        // Calculate attendance stats
        $stats = $this->calculateAttendanceStats($attendances);
        
        // Get leave balances
        $leaveBalances = $employee->leaveBalances()->first();

        // Get next holiday
        $nextHoliday = Holiday::where('date', '>', now())
                             ->orderBy('date')
                             ->first();

        return [
            // Attendance Stats
            'presentDays' => $stats['present'],
            'workingDays' => $workingDays,
            'avgActualWorkHours' => $attendances->avg('actual_work_hours'),
            'penaltyDays' => $attendances->where('status', 'penalty')->count(),
            
            // Leave Balance
            'casualLeaveBalance' => $leaveBalances->casual_leave_balance ?? 0,
            'sickLeaveBalance' => $leaveBalances->sick_leave_balance ?? 0,
            'earnedLeaveBalance' => $leaveBalances->earned_leave_balance ?? 0,
            
            // Next Holiday
            'nextHoliday' => $nextHoliday ? [
                'name' => $nextHoliday->name,
                'date' => $nextHoliday->date,
            ] : null,
            
            // Today's Summary
            'todayStatus' => $todayAttendance ? $todayAttendance->status : null,
            'todayCheckIn' => $todayAttendance ? $todayAttendance->check_in : null,
            'todayWorkHours' => $todayAttendance ? $todayAttendance->actual_work_hours : 0,
        ];
    }

    public function mark(Request $request)
    {
        $request->validate([
            'action' => 'required|in:in,out',
            'work_type' => 'required|in:office,wfh'
        ]);

        $employee = $request->employee;
        $today = Carbon::today();
        
        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', $today)
            ->first();

        if ($request->action === 'in') {
            if ($attendance && $attendance->first_in) {
                return response()->json(['error' => 'Already checked in today'], 422);
            }

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->employee_id = $employee->employee_id;
                $attendance->date = $today;
                $attendance->work_type = $request->work_type;
                $attendance->status = 'pending';
            }

            $attendance->first_in = Carbon::now();
            $attendance->save();

            return response()->json([
                'message' => 'Checked in successfully',
                'check_in_time' => $attendance->first_in->format('h:i A')
            ]);
        } else {
            if (!$attendance || !$attendance->first_in) {
                return response()->json(['error' => 'No check-in record found for today'], 422);
            }

            if ($attendance->last_out) {
                return response()->json(['error' => 'Already checked out today'], 422);
            }

            $attendance->last_out = Carbon::now();
            $attendance->total_work_hours = $attendance->last_out->diffInHours($attendance->first_in);
            $attendance->break_hours = 1; // 1 hour break
            $attendance->actual_work_hours = $attendance->total_work_hours - $attendance->break_hours;
            $attendance->save();

            return response()->json([
                'message' => 'Checked out successfully',
                'check_out_time' => $attendance->last_out->format('h:i A'),
                'total_hours' => $attendance->actual_work_hours
            ]);
        }
    }

    public function status(Request $request)
    {
        $employee = $request->employee;
        $today = Carbon::today();
        
        $attendance = Attendance::where('employee_id', $employee->employee_id)
            ->whereDate('date', $today)
            ->first();

        return response()->json([
            'checked_in' => $attendance && $attendance->first_in ? true : false,
            'checked_out' => $attendance && $attendance->last_out ? true : false,
            'check_in_time' => $attendance && $attendance->first_in ? $attendance->first_in->format('h:i A') : null,
            'check_out_time' => $attendance && $attendance->last_out ? $attendance->last_out->format('h:i A') : null,
            'total_hours' => $attendance && $attendance->actual_work_hours ? $attendance->actual_work_hours : null,
            'work_type' => $attendance ? $attendance->work_type : null
        ]);
    }

    public function hybridDashboard(Request $request)
    {
        $employee = $request->employee;
        
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        // Get current work mode and today's attendance
        $currentMode = session('work_mode', 'WFO');
        $today = now()->format('Y-m-d');
        
        $todayAttendance = Attendance::where('employee_id', $employee->employee_id)
            ->where('date', $today)
            ->first();

        $data = [
            'currentMode' => $currentMode,
            'todayHours' => $todayAttendance ? $todayAttendance->total_hours : 0,
            'status' => $todayAttendance ? $todayAttendance->status : null,
            'location' => $todayAttendance ? $todayAttendance->location_address : null
        ];

        return view('attendance.hybrid-dashboard', $data);
    }

    public function setWorkMode(Request $request)
    {
        $request->validate([
            'work_mode' => 'required|in:WFO,WFH'
        ]);

        session(['work_mode' => $request->work_mode]);

        return redirect()->back()->with('success', 'Work mode updated successfully.');
    }

    public function checkIn(Request $request)
    {
        try {
            $employee = $request->employee;
            if (!$employee) {
                return response()->json(['error' => 'Employee record not found.'], 404);
            }

            // Validate the request
            $request->validate([
                'webcam_image' => 'required|string',
                'work_mode' => 'required|in:office,wfh',
            ]);

            // For WFH, verify face
            if ($request->work_mode === 'wfh') {
                // Check if user has face embedding
                if (!$employee->user->face_embedding) {
                    return response()->json(['error' => 'No face profile found. Please upload a profile picture and contact HR.'], 400);
                }

                // Save webcam image
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->webcam_image));
                $tempImage = tempnam(sys_get_temp_dir(), 'face_');
                file_put_contents($tempImage, $imageData);

                // Process the image using face detection helper
                $pythonScript = base_path('app/Python/face_detection_helper.py');
                $command = "python \"{$pythonScript}\" \"{$tempImage}\"";
                $output = shell_exec($command);

                if (empty($output)) {
                    unlink($tempImage);
                    return response()->json(['error' => 'Face detection failed.'], 400);
                }

                $result = json_decode($output, true);
                unlink($tempImage);

                if (isset($result['error'])) {
                    return response()->json(['error' => $result['error']], 400);
                }

                // Compare with stored face embedding
                $storedFeatures = json_decode($employee->user->face_embedding, true);
                if (!$storedFeatures) {
                    return response()->json(['error' => 'Face profile is invalid. Please contact HR.'], 400);
                }

                $similarity = $this->calculateCosineSimilarity($result['features'], $storedFeatures);
                if ($similarity < $this->similarityThreshold) {
                    return response()->json(['error' => 'Face verification failed.'], 401);
                }
            }

            // Check if already checked in
            $existingAttendance = Attendance::where('employee_id', $employee->employee_id)
                ->whereDate('date', Carbon::today())
                ->first();

            if ($existingAttendance) {
                if ($existingAttendance->check_in) {
                    return response()->json(['error' => 'Already checked in for today.'], 400);
                }
            } else {
                $existingAttendance = new Attendance();
                $existingAttendance->employee_id = $employee->employee_id;
                $existingAttendance->date = Carbon::today();
            }

            $existingAttendance->check_in = Carbon::now();
            $existingAttendance->work_mode = $request->work_mode;
            $existingAttendance->status = 'present';
            $existingAttendance->save();

            return response()->json([
                'message' => 'Check-in successful',
                'attendance' => $existingAttendance
            ]);

        } catch (\Exception $e) {
            Log::error('Check-in error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during check-in.'], 500);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            $employee = $request->employee;
            if (!$employee) {
                return response()->json(['error' => 'Employee record not found.'], 404);
            }

            // Validate the request
            $request->validate([
                'webcam_image' => 'required_if:work_mode,wfh|string',
            ]);

            // Get today's attendance
            $attendance = Attendance::where('employee_id', $employee->employee_id)
                ->whereDate('date', Carbon::today())
                ->first();

            if (!$attendance) {
                return response()->json(['error' => 'No check-in record found for today.'], 400);
            }

            if ($attendance->check_out) {
                return response()->json(['error' => 'Already checked out for today.'], 400);
            }

            // For WFH, verify face
            if ($attendance->work_mode === 'wfh') {
                // Save webcam image
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->webcam_image));
                $tempImage = tempnam(sys_get_temp_dir(), 'face_');
                file_put_contents($tempImage, $imageData);

                // Process the image using face detection helper
                $pythonScript = storage_path('app/face_detection_helper.py');
                $command = "python \"{$pythonScript}\" \"{$tempImage}\"";
                $output = shell_exec($command);

                if (empty($output)) {
                    unlink($tempImage);
                    return response()->json(['error' => 'Face detection failed.'], 400);
                }

                $result = json_decode($output, true);
                unlink($tempImage);

                if (isset($result['error'])) {
                    return response()->json(['error' => $result['error']], 400);
                }

                // Compare with stored face embedding
                $storedFeatures = json_decode($employee->user->face_embedding, true);
                if (!$storedFeatures) {
                    return response()->json(['error' => 'No face profile found. Please contact HR.'], 400);
                }

                $similarity = $this->calculateCosineSimilarity($result['features'], $storedFeatures);
                if ($similarity < $this->similarityThreshold) {
                    return response()->json(['error' => 'Face verification failed.'], 401);
                }
            }

            $attendance->check_out = Carbon::now();
            $attendance->save();

            return response()->json([
                'message' => 'Check-out successful',
                'attendance' => $attendance
            ]);

        } catch (\Exception $e) {
            Log::error('Check-out error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during check-out.'], 500);
        }
    }

    protected function calculateCosineSimilarity($vector1, $vector2)
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        foreach ($vector1 as $i => $value) {
            $dotProduct += $value * $vector2[$i];
            $magnitude1 += $value * $value;
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }
        
        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    private function getLocationAddress($latitude, $longitude)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get("https://nominatim.openstreetmap.org/reverse", [
                'query' => [
                    'format' => 'json',
                    'lat' => $latitude,
                    'lon' => $longitude
                ],
                'headers' => [
                    'User-Agent' => 'FidelisAttendanceSystem/1.0'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['display_name'] ?? 'Location not found';
        } catch (\Exception $e) {
            return 'Location service unavailable';
        }
    }

    private function calculateAttendanceStats($attendances)
    {
        return [
            'present' => $attendances->where('status', 'present')->count(),
            'leave' => $attendances->where('status', 'leave')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
            'comp_off' => $attendances->where('status', 'comp_off')->count(),
            'wfh' => $attendances->where('status', 'wfh')->count(),
            'total_days' => $attendances->count(),
            'working_days' => $attendances->count(),
            'attendance_percentage' => $attendances->count() > 0 
                ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 2)
                : 0
        ];
    }
}
