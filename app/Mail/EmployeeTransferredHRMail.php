<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeTransferredHRMail extends Mailable
{
    use Queueable, SerializesModels;

    public $managerEmail;
    public $resignation;

    /**
     * Create a new message instance.
     */
    public function __construct($resignation, $managerEmail)
    {
        $this->resignation = $resignation;
        $this->managerEmail = $managerEmail;
    }


    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Employee Transferred')
            ->view('emails.resignations.employee-transferred-hr')
            ->with([
                'resignation' => $this->resignation,
                'managerEmail' => $this->managerEmail
            ]);
    }
}
