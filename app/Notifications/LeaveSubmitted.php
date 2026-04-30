<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Leave $leave, $isEmployee)
    {
        $this->leave = $leave;
        $this->isEmployee = $isEmployee;
        $this->leave->leave_type = $leave->leave_type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->isEmployee) {
            return (new MailMessage)
                ->subject('Leave Request')
                ->line('Your leave request has been submitted.')
                ->line('Leave Type: ' . $this->leave->leave_type)
                ->line('Start Date: ' . $this->leave->from_date)
                ->line('End Date: ' . $this->leave->to_date)
                ->line('Reason: ' . $this->leave->reason);
        }
        return (new MailMessage)
            ->subject('Leave Request')
            ->line('A leave request has been submitted by ' . $this->leave->user->name)
            ->line('Leave Type: ' . $this->leave->leave_type)
            ->line('Start Date: ' . $this->leave->from_date)
            ->line('End Date: ' . $this->leave->to_date)
            ->line('Contact Details: ' . $this->leave->contact_details)
            ->line('Reason: ' . $this->leave->reason);
    }
}