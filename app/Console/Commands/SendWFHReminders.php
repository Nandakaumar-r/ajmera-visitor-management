<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use App\Notifications\WFHAttendanceReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWFHReminders extends Command
{
    protected $signature = 'attendance:wfh-reminders';
    protected $description = 'Send reminders to WFH employees who haven\'t logged their attendance';

    public function handle()
    {
        $today = Carbon::today();
        
        // Get all employees with WFH mode
        $employees = Employee::whereHas('attendances', function($query) {
            $query->where('work_mode', 'WFH');
        })->get();

        foreach ($employees as $employee) {
            // Check if attendance is logged for today
            $attendance = Attendance::where('employee_id', $employee->employee_id)
                ->where('date', $today)
                ->first();

            if (!$attendance || !$attendance->total_hours) {
                // Send reminder if no attendance or hours not logged
                if ($attendance && $attendance->shouldSendReminder()) {
                    $employee->user->notify(new WFHAttendanceReminder());
                    
                    // Update reminder status
                    $attendance->update([
                        'is_reminder_sent' => true,
                        'last_reminder_sent_at' => now()
                    ]);
                } elseif (!$attendance) {
                    // Create attendance record and send reminder
                    $attendance = Attendance::create([
                        'employee_id' => $employee->employee_id,
                        'date' => $today,
                        'work_mode' => 'WFH',
                        'status' => 'pending',
                        'is_reminder_sent' => true,
                        'last_reminder_sent_at' => now()
                    ]);
                    
                    $employee->user->notify(new WFHAttendanceReminder());
                }

                // Notify manager
                if ($employee->manager) {
                    $employee->manager->user->notify(new WFHAttendanceReminder($employee));
                }
            }
        }

        $this->info('WFH attendance reminders sent successfully.');
    }
}
