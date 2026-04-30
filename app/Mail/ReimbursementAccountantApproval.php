<?php

namespace App\Mail;

use App\Models\ExternalReimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReimbursementAccountantApproval extends Mailable
{
    use Queueable, SerializesModels;

    public $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function build()
    {
        return $this->subject("{$this->month} IT External Employee Claims – Approval for Processing")
                    ->view('emails.reimbursements.accountant')
                    ->with([
                        'month' => $this->month,
                    ]);
    }
}
