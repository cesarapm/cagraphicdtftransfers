<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Pay;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdenAprobada extends Mailable
{
    use Queueable, SerializesModels;

    public $orden;
    public $pago;
    public $productos;  

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, Pay $pago)
    {
        $this->orden = $order;
        $this->pago = $pago;
        $this->productos = $order->items()->get();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva orden aprobada ' . $this->orden->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orden-aprobada',
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
