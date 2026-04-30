<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\InternalOnboardingCandidateDetails;

class OfferCancelledNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $orf;
    public $reason;

    public function __construct(InternalOnboardingCandidateDetails $orf)
    {
        $this->orf = $orf;
        $this->reason = $orf->remarks; // Generic remarks field
    }

   public function build()
{
    return $this->subject('Application Update: Cancelled - Fidelis Group')
                ->markdown('emails.offer_cancelled');
}

}
