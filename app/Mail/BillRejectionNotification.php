<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillRejectionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;
    public $rejectedBy;
    public $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct($bill, $rejectedBy, $recipientType = 'vendor')
    {
        $this->bill = $bill;
        $this->rejectedBy = $rejectedBy;
        $this->recipientType = $recipientType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Bill #{$this->bill->id} has been rejected")
            ->markdown('emails.bills.bill_rejection_notification');
    }
}
