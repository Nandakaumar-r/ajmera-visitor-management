<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WFHAttendanceReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $employee;

    public function __construct(Employee $employee = null)
    {
        $this->employee = $employee;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        if ($this->employee) {
            // Notification for manager
            return (new MailMessage)
                ->subject('Missing WFH Attendance - ' . $this->employee->full_name)
                ->line('This is a reminder that ' . $this->employee->full_name . ' has not logged their WFH attendance for today.')
                ->line('Please ensure they complete their attendance record.')
                ->action('View Attendance', route('attendance.hybrid'))
                ->line('Thank you for your attention to this matter.');
        }

        // Notification for employee
        return (new MailMessage)
            ->subject('WFH Attendance Reminder')
            ->line('This is a reminder to log your Work From Home (WFH) attendance for today.')
            ->line('Please take a moment to record your working hours.')
            ->action('Log Attendance', route('attendance.hybrid'))
            ->line('Thank you for your cooperation.');
    }

    public function toArray($notifiable)
    {
        if ($this->employee) {
            return [
                'title' => 'Missing WFH Attendance - ' . $this->employee->full_name,
                'message' => $this->employee->full_name . ' has not logged their WFH attendance for today.',
                'action_url' => route('attendance.hybrid')
            ];
        }

        return [
            'title' => 'WFH Attendance Reminder',
            'message' => 'Please log your Work From Home (WFH) attendance for today.',
            'action_url' => route('attendance.hybrid')
        ];
    }
}
