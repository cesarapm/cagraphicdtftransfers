<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Pay;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdenClienteAprobada extends Mailable
{
    use Queueable, SerializesModels;

    public $orden;
    public $pago;
    public $productos;
    public $trackingUrl;

    public function __construct(Order $order, Pay $pago)
    {
        $this->orden = $order;
        $this->pago = $pago;
        $this->productos = $order->items()->get();
        $this->trackingUrl = $order->tracking_url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu pedido ' . $this->orden->order_number . ' fue confirmado y ya puedes seguirlo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orden-cliente-aprobada',
            with: [
                'orden' => $this->orden,
                'pago' => $this->pago,
                'productos' => $this->productos,
                'trackingUrl' => $this->trackingUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}