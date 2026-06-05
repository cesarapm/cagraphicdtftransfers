<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOrdersAccessCode extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $code,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu codigo para ver tus pedidos en IzaguirreQu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-orders-access-code',
            with: [
                'order' => $this->order,
                'code' => $this->code,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}