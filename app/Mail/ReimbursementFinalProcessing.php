<?php

namespace App\Mail;

use App\Models\ExternalReimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReimbursementFinalProcessing extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $reimbursement;
    public $month;
    public function __construct($month)
    {
        $this->month = $month;
    }

    public function build()
    {
        return $this->subject("Approval Confirmation – {$this->month} IT Reimbursement Claims")
            ->view('emails.reimbursements.final')
            ->with([
                'month' => $this->month
        ]);
    }
}
