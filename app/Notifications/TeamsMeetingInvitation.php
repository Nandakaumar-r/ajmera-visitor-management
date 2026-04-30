<?php

namespace App\Notifications;

use App\Models\CabinBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class TeamsMeetingInvitation extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct(CabinBooking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Teams Meeting Invitation: ' . $this->booking->purpose)
            ->line('You have been invited to a Teams meeting.')
            ->line('Purpose: ' . $this->booking->purpose)
            ->line('Start Time: ' . $this->booking->start_time->format('F j, Y g:i A'))
            ->line('End Time: ' . $this->booking->end_time->format('F j, Y g:i A'))
            ->action('Join Meeting', $this->booking->teams_meeting_link)
            ->line('Click the button above to join the Teams meeting at the scheduled time.');
    }
}
