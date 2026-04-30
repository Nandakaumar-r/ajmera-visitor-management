<?php

namespace App\Notifications;

use App\Models\TravelReimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReimbursementStatusUpdated extends Notification implements ShouldQueue
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
        $status = ucfirst($this->reimbursement->status);

        $message = (new MailMessage)
            ->subject("Travel Reimbursement {$status}")
            ->line("Your travel reimbursement request has been {$status}.")
            ->line("Amount: {$this->reimbursement->amount}")
            ->line("Description: {$this->reimbursement->description}");

        if ($this->reimbursement->status === 'rejected') {
            $message->line("Reason for rejection: {$this->reimbursement->rejection_reason}");
        }

        return $message
            ->action('View Details', $url)
            ->line('Thank you for using our travel management system.');
    }

    public function toArray($notifiable)
    {
        $status = ucfirst($this->reimbursement->status);
        
        return [
            'reimbursement_id' => $this->reimbursement->id,
            'travel_request_id' => $this->reimbursement->travel_request_id,
            'message' => "Travel reimbursement request has been {$status}",
            'link' => route('travel.show', $this->reimbursement->travelRequest),
        ];
    }
}
