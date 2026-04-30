<?php
namespace App\Mail;

use App\Models\InternalOnboardingCandidateDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfferMadeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $orf;

    public function __construct(InternalOnboardingCandidateDetails $orf)
    {
        $this->orf = $orf;
    }

    public function build()
    {
        return $this->subject('Your Offer from Fidelis Group')
                    ->markdown('emails.offer_made')
                    ->with([
                        'orf' => $this->orf,
                    ]);
    }
}
