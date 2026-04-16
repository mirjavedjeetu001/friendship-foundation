<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContributionSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contribution $contribution,
        public User $recipient
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contribution Submitted - Approval Required',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contribution-submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
