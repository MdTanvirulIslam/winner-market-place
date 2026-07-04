<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// Sent when an order is placed and awaiting payment — carries the manual
// payment instructions and a link to pay online.
class OrderPlacedMail extends Mailable
{
    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('Order %s received — how to pay', $this->order->order_no),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-placed',
            with: ['setting' => Setting::current()],
        );
    }
}
