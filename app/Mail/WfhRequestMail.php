<?php

namespace App\Mail;

use App\Models\WfhRequest;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WfhRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $wfhRequest;
    public $employee;

    public function __construct(WfhRequest $wfhRequest, Employee $employee) // ✅ Accept Employee!
    {
        $this->wfhRequest = $wfhRequest;
        $this->employee = $employee;
    }

   public function build()
{
    return $this->markdown('emails.wfh-request') // ✅ This is your markdown view file name
                ->subject('New WFH Request Submitted'); // ✅ This sets the email subject line
}

}
