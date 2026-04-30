<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Vendor;

class VendorWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $vendor;
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @param Vendor $vendor
     * @return void
     */
    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
        $this->loginUrl = url('/login');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to ' . config('app.name') . ' - Vendor Portal')
                    ->markdown('emails.vendor.welcome')
                    ->with([
                        'vendor' => $this->vendor,
                        'portalUrl' => $this->loginUrl,
                    ]);
    }
}
