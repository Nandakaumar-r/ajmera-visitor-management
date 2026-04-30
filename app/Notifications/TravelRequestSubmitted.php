<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelRequestSubmitted extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject('New Travel Request Submitted')
            ->line('A new travel request has been submitted for your approval.')
            ->line("Destination: {$this->travelRequest->destination}")
            ->line("Travel Dates: {$this->travelRequest->start_date} to {$this->travelRequest->end_date}")
            ->line("Estimated Cost: {$this->travelRequest->estimated_cost}")
            ->action('View Request', $url)
            ->line('Please review the request and take necessary action.');
    }

    public function toArray($notifiable)
    {
        return [
            'travel_request_id' => $this->travelRequest->id,
            'message' => 'New travel request submitted for approval',
            'link' => route('travel.show', $this->travelRequest),
        ];
    }
}
