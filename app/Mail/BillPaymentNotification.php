<?php

namespace App\Mail;

use App\Models\VendorBill;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillPaymentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;
    public $vendor;
    public $paymentStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(VendorBill $bill, $paymentStatus)
    {
        $this->bill = $bill;
        $this->vendor = $bill->vendor;
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Payment Status Update for Your Bill #' . $this->bill->id)
            ->markdown('emails.bills.payment_notification')
            ->with([
                'vendor' => $this->vendor,
                'bill' => $this->bill,
                'status' => ucfirst($this->paymentStatus),
            ]);
    }
}
