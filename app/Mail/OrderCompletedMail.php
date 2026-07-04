<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// Sent synchronously (no queue worker on shared hosting). The license
// credentials email comes separately from the License Manager — this one
// covers the order itself and where to download.
class OrderCompletedMail extends Mailable
{
    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('Your order %s is complete — %s', $this->order->order_no, $this->order->product_name),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-completed',
        );
    }
}
