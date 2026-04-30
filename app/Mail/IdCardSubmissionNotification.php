<?php

namespace App\Mail;

use App\Models\IdCardSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IdCardSubmissionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;

    public function __construct(IdCardSubmission $submission)
    {
        $this->submission = $submission;
    }
    
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New ID Card Submission Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return (new Content(
            view: 'emails.id_card_submission'
        ))->with([
            'employee_id' => $this->submission->employee_id,
            'file_path' => $this->submission->file_path,
            'remarks' => $this->submission->remarks,
        ]);
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
