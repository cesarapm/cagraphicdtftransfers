<?php

namespace App\Http\Controllers\Api;

use App\Models\GangSheet;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Controlador para manejar el flujo de pago de Gang Sheets
 * 
 * Flujo:
 * 1. Usuario guarda design → POST /api/gang-sheets (genera gang_sheet_id)
 * 2. Usuario va a checkout → GET /checkout?gang_sheet_id=123
 * 3. Usuario paga → POST /api/gang-sheets/payment
 * 4. Stripe webhook → POST /api/webhooks/stripe/gang-sheets
 * 5. Backend genera imagen → GET /api/gang-sheets/123/download
 */
class GangSheetPaymentController extends Controller
{
    /**
     * Initiate payment for a gang sheet
     * 
     * POST /api/gang-sheets/payment
     */
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'gang_sheet_id' => 'required|exists:gang_sheets,id',
            'amount' => 'required|numeric|min:1',
            'email' => 'required|email',
        ]);

        $gangSheet = GangSheet::findOrFail($validated['gang_sheet_id']);

        // Verificar que el gang sheet está en estado draft (no pagado)
        if ($gangSheet->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'This gang sheet has already been processed',
            ], 400);
        }

        try {
            // Crear orden en BD
            $order = Order::create([
                'gang_sheet_id' => $gangSheet->id,
                'customer_id' => auth()->id() ?? null,
                'amount' => $validated['amount'],
                'email' => $validated['email'],
                'status' => 'pending',
                'payment_method' => 'stripe',
            ]);

            // Crear Payment Intent en Stripe
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => (int)($validated['amount'] * 100), // centavos
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id,
                    'gang_sheet_id' => $gangSheet->id,
                    'customer_email' => $validated['email'],
                ],
                'receipt_email' => $validated['email'],
            ]);

            // Guardar stripe_payment_id en orden
            $order->update([
                'stripe_payment_id' => $paymentIntent->id,
            ]);

            // Actualizar gang sheet a processing
            $gangSheet->update(['status' => 'processing']);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment initiation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error initiating payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Stripe webhook for completed payments
     * 
     * POST /api/webhooks/stripe/gang-sheets
     * 
     * Set this URL in Stripe Dashboard:
     * https://yourdomain.com/api/webhooks/stripe/gang-sheets
     * 
     * Events to listen for:
     * - payment_intent.succeeded
     * - payment_intent.payment_failed
     */
    public function handleStripeWebhook(Request $request)
    {
        $event = json_decode($request->getContent());

        try {
            // Verificar firma de Stripe
            $this->verifyStripeSignature($request);

            match ($event->type) {
                'payment_intent.succeeded' => $this->handlePaymentSucceeded($event),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event),
                default => \Log::info('Unhandled webhook event: ' . $event->type),
            };

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle payment succeeded event
     */
    private function handlePaymentSucceeded($event)
    {
        $paymentIntent = $event->data->object;
        $gangSheetId = $paymentIntent->metadata->gang_sheet_id;
        $orderId = $paymentIntent->metadata->order_id;

        $gangSheet = GangSheet::findOrFail($gangSheetId);
        $order = Order::findOrFail($orderId);

        // Actualizar orden a pagada
        $order->update(['status' => 'paid']);

        \Log::info("Payment succeeded for gang sheet {$gangSheetId}");

        // Generar imagen de alta resolución en background job
        // Para no bloquear el webhook, usamos un job asincrónico
        \App\Jobs\GenerateGangSheetImage::dispatch($gangSheet);

        // Enviar email de confirmación
        \App\Mail\GangSheetPaidMail::send(
            $order->email,
            $gangSheet,
            $order
        );
    }

    /**
     * Handle payment failed event
     */
    private function handlePaymentFailed($event)
    {
        $paymentIntent = $event->data->object;
        $gangSheetId = $paymentIntent->metadata->gang_sheet_id;
        $orderId = $paymentIntent->metadata->order_id;

        $gangSheet = GangSheet::findOrFail($gangSheetId);
        $order = Order::findOrFail($orderId);

        // Actualizar orden a fallida
        $order->update(['status' => 'failed']);
        $gangSheet->update(['status' => 'failed']);

        \Log::warning("Payment failed for gang sheet {$gangSheetId}");

        // Enviar email de error
        \App\Mail\GangSheetPaymentFailedMail::send(
            $order->email,
            $gangSheet,
            $paymentIntent->last_payment_error->message ?? 'Unknown error'
        );
    }

    /**
     * Verify Stripe webhook signature
     */
    private function verifyStripeSignature(Request $request)
    {
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        \Stripe\Webhook::constructEvent(
            $request->getContent(),
            $signature,
            $secret
        );
    }
}
