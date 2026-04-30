<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OnboardingLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $candidateName;
    public $link;

    public function __construct($candidateName, $link)
    {
        $this->candidateName = $candidateName;
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject('Complete Your Onboarding Form')
                    ->view('emails.onboarding_link');
    }
}
