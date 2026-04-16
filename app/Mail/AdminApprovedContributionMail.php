<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminApprovedContributionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contribution $contribution,
        public User $recipient,
        public User $admin
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admin Approved - Your Approval Needed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-approved-contribution',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
