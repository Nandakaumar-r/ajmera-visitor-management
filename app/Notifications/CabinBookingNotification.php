<?php

namespace App\Notifications;

use App\Models\CabinBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CabinBookingNotification extends Notification
{
    use Queueable;

    protected $booking;
    public $id;

    public function __construct(CabinBooking $booking)
    {
        $this->booking = $booking;
        $this->id = (string) \Illuminate\Support\Str::uuid();
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Meeting Invitation')
            ->line('You have been invited to a meeting.')
            ->line('Meeting Details:')
            ->line('Location: ' . $this->booking->cabin->name)
            ->line('Date: ' . $this->booking->start_time->format('Y-m-d'))
            ->line('Time: ' . $this->booking->start_time->format('H:i') . ' - ' . $this->booking->end_time->format('H:i'))
            ->line('Purpose: ' . $this->booking->purpose)
            ->action('View Details', url('/bookings/' . $this->booking->id));
    }

    public function toDatabase($notifiable)
    {
        return [
            'id' => $this->id,
            'type' => 'cabin_booking',
            'data' => json_encode([
                'booking_id' => $this->booking->id,
                'cabin_name' => $this->booking->cabin->name,
                'start_time' => $this->booking->start_time,
                'end_time' => $this->booking->end_time,
                'purpose' => $this->booking->purpose,
                'created_by' => $this->booking->user->name
            ])
        ];
    }

    public function toArray($notifiable)
    {
        $data = $this->toDatabase($notifiable);
        return json_decode($data['data'], true);
    }
}
