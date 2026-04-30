<?php

namespace App\Mail;

use App\Models\ExternalReimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReimbursementFinalConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
 
    public $month;
    public function __construct($month)
    {
        $this->month = $month;
    }

    public function build()
    {
        return $this->subject("{$this->month} Reimbursement Claims – Payment Confirmation")
            ->view('emails.reimbursements.confirmed')
            ->with([
                'month' => $this->month,
        ]);
    }
}
