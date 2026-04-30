<?php

namespace App\Mail;

use App\Models\Resignation;
use App\Models\Manager;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LWDConfirmationMailToHR extends Mailable
{
    use Queueable, SerializesModels;

    public $resignation;
    public $manager;
    public $approvedBy;

    public function __construct(Resignation $resignation, Manager $manager)
    {
        $this->resignation = $resignation;
        $this->manager = $manager;
    }

    public function build()
    {
        return $this->subject('Resignation Accepted - HR Notification')
                    ->markdown('emails.lwd_confirmation_hr');
    }
}
