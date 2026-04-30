<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InternalReimbursementAccountantApproval extends Mailable
{
    use Queueable, SerializesModels;

    public $monthYear;

    public function __construct($monthYear)
    {
        $this->monthYear = $monthYear;
    }

    public function build()
    {
        return $this->subject("Approval Request – {$this->monthYear} Internal Employee Claims")
            ->view('emails.internal-reimbursements.accountant')
            ->with([
                'monthYear' => $this->monthYear,
            ]);
    }
}
