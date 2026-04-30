<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\WfhRequest;

class WfhStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $wfhRequest;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct(WfhRequest $wfhRequest, $status)
    {
        $this->wfhRequest = $wfhRequest;
        $this->status = $status;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Work From Home Request has been ' . ucfirst($this->status))
                    ->markdown('emails.wfh_status');
    }
}
