<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVisitorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $visitor;

    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $photoUrl = $this->visitor->photo_path
            ? asset('storage/' . $this->visitor->photo_path)
            : null;

        return (new MailMessage)
            ->subject('New Visitor Registration')
            ->view('emails.visitor-notification', [
                'notifiable' => $notifiable,
                'visitor' => $this->visitor,
                'photoUrl' => $photoUrl,
                'actionUrl' => route('visitors.show', $this->visitor)
            ]);
    }


    public function toArray($notifiable)
    {
        return [
            'visitor_id' => $this->visitor->id,
            'visitor_name' => $this->visitor->first_name . ' ' . $this->visitor->last_name,
            'purpose' => $this->visitor->purpose_of_visit,
        ];
    }
}
