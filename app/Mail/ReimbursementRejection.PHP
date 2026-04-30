<?php

namespace App\Mail;

use App\Models\ExternalReimbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReimbursementRejection extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $reimbursement;
    public function __construct(ExternalReimbursement $reimbursement) {
        $this->reimbursement = $reimbursement;
    }
    public function build()
{
    return $this->subject('New Reimbursement has been rejected')
                ->view('emails.reimbursements.rejected')
                ->with(['reimbursement' => $this->reimbursement]);
}


   
}
