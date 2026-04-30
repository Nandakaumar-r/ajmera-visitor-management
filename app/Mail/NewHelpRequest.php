<?php

namespace App\Mail;

use App\Models\HelpRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewHelpRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $helpRequest;

    public function __construct(HelpRequest $helpRequest)
    {
        $this->helpRequest = $helpRequest;
    }

    public function build()
    {
        return $this->markdown('emails.help-request.new')
            ->subject('New Help Request: ' . $this->helpRequest->subject);
    }
}
