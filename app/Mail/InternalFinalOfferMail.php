<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InternalFinalOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $candidate;
    public $attachments;

    public function __construct($candidate, $attachments = [])
    {
        $this->candidate = $candidate;
        $this->attachments = $attachments;
    }

    public function build()
    {
    
        $email = $this->subject('Your Final Offer & Joining Documents')
                      ->view('emails.final_offer');

        foreach ($this->attachments as $attachment) {
        $email->attach($attachment['file'], $attachment['options']);
    }
        return $email;
    }
}
