<?php

namespace App\Mail;

use App\Models\ExternalReimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReimbursementHRApproval extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $reimbursement;
    public $monthYear;
    
    public function __construct(ExternalReimbursement $reimbursement, $monthYear) {
        $this->reimbursement = $reimbursement;
        $this->monthYear = $monthYear;
    }
    public function build()
{
    return $this->subject("Approval Request – {$this->monthYear} IT External Employee Claims")
                ->view('emails.reimbursements.hr')
                ->with([
                    'reimbursement' => $this->reimbursement,
                    'monthYear' => $this->monthYear,
                ]);
}


   
}
