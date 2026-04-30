<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->markdown('emails.bookings.cancelled')
                    ->subject('Cabin Booking Cancellation - Confirmation')
                    ->with([
                        'booking' => $this->booking
                    ]);
    }
}
