<?php

namespace App\Mail;

use App\Models\Release;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// Sent to every buyer with a delivered order when a new version of their
// product ships. Synchronous, like all mail in this app (no queue worker
// on shared hosting).
class NewReleaseMail extends Mailable
{
    public function __construct(public Release $release)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('%s v%s is available', $this->release->product->name, $this->release->version),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-release',
        );
    }
}
