<?php

namespace App\Notifications;

use App\Models\TravelReimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReimbursementSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reimbursement;

    public function __construct(TravelReimbursement $reimbursement)
    {
        $this->reimbursement = $reimbursement;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = route('travel.show', $this->reimbursement->travelRequest);

        return (new MailMessage)
            ->subject('New Travel Reimbursement Request')
            ->line('A new travel reimbursement request has been submitted for approval.')
            ->line("Amount: {$this->reimbursement->amount}")
            ->line("Description: {$this->reimbursement->description}")
            ->line('The receipts have been attached to the request.')
            ->action('View Request', $url)
            ->line('Please review the request and take necessary action.');
    }

    public function toArray($notifiable)
    {
        return [
            'reimbursement_id' => $this->reimbursement->id,
            'travel_request_id' => $this->reimbursement->travel_request_id,
            'message' => 'New travel reimbursement request submitted',
            'link' => route('travel.show', $this->reimbursement->travelRequest),
        ];
    }
}
