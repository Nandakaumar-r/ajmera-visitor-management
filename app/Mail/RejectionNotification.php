<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectionNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
 public $orf;
public $role;
public $reason;

public function __construct($orf, $role,$reason)
{
    $this->orf = $orf;
    $this->role = $role;
     $this->reason = $reason;
}

public function build()
{
    return $this->markdown('emails.orf.rejection')
                ->subject('ORF Rejected by ' . strtoupper($this->role))
                ->with([
                    'orf' => $this->orf,
                    'role' => $this->role,
                    'reason' => $this->reason,
                ]);
}


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rejection Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orf.rejection',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
