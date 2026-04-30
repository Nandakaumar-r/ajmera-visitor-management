<?php

namespace App\Mail;

use App\Models\VendorBill;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillApprovalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;
    public $approver;

    public function __construct(VendorBill $bill, User $approver)
    {
        $this->bill = $bill;
        $this->approver = $approver;
    }

    public function build()
    {
        return $this->subject('New Bill Awaiting Your Approval')
            ->markdown('emails.bills.approval')
            ->with([
                'bill' => $this->bill,
                'approver' => $this->approver,
            ]);
    }
}
