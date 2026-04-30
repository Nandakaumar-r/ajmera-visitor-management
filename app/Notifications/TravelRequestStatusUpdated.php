<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelRequestStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $travelRequest;

    public function __construct(TravelRequest $travelRequest)
    {
        $this->travelRequest = $travelRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = route('travel.show', $this->travelRequest);
        $status = ucfirst(str_replace('_', ' ', $this->travelRequest->status));

        $message = (new MailMessage)
            ->subject("Travel Request Status Updated: {$status}")
            ->line("Your travel request status has been updated to: {$status}")
            ->line("Destination: {$this->travelRequest->destination}")
            ->line("Travel Dates: {$this->travelRequest->start_date} to {$this->travelRequest->end_date}");

        if ($this->travelRequest->status === 'rejected') {
            $message->line('Reason for rejection: ' . 
                ($this->travelRequest->manager_comments ?? $this->travelRequest->cfo_comments));
        } elseif ($this->travelRequest->status === 'booked') {
            $message->line('Booking details have been updated. Please check the request for more information.');
        }

        return $message
            ->action('View Request', $url)
            ->line('Thank you for using our travel management system.');
    }

    public function toArray($notifiable)
    {
        $status = ucfirst(str_replace('_', ' ', $this->travelRequest->status));
        
        return [
            'travel_request_id' => $this->travelRequest->id,
            'message' => "Travel request status updated to: {$status}",
            'link' => route('travel.show', $this->travelRequest),
        ];
    }
}
