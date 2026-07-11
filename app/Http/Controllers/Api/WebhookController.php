<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrdenClienteAprobada;
use App\Mail\OrdenAprobada;
use App\Models\Order;
use App\Models\Pay;
use App\Services\MercadoPagoConfig;
use App\Services\PayPalConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Log::info('✅ Webhook de Mercado Pago recibido', $request->all());

        if (!$this->validateWebhookSignature($request)) {
            Log::error('❌ Webhook de Mercado Pago con firma INVÁLIDA', [
                'x-signature' => $request->header('x-signature'),
                'x-request-id' => $request->header('x-request-id'),
                'request_data' => $request->all(),
            ]);
            return response()->json(['success' => false, 'message' => 'Firma inválida'], 401);
        }

        $type = $request->input('type');
        $paymentId = $request->input('data.id') ?? $request->input('id');
        $merchantOrderId = $request->input('data.id') ?? $request->input('data_id');

        // Log::info('📊 Datos del webhook extraídos', [
        //     'type' => $type,
        //     'payment_id' => $paymentId,
        //     'merchant_order_id' => $merchantOrderId,
        // ]);

        if ($type === 'payment' && $paymentId) {
            // Log::info('🔄 Sincronizando pago...', ['payment_id' => $paymentId]);
            $this->syncPayment((string) $paymentId);
        } elseif (in_array($type, ['merchant_order', 'topic_merchant_order_wh'], true) && $merchantOrderId) {
            // Log::info('🔄 Sincronizando merchant order...', ['merchant_order_id' => $merchantOrderId]);
            $this->syncMerchantOrder((string) $merchantOrderId);
        }

        return response()->json(['success' => true]);
    }

    protected function validateWebhookSignature(Request $request): bool
    {
        $secret = MercadoPagoConfig::getWebhookSecret();

        if (!$secret) {
            return true;
        }

        $xSignature = $request->header('x-signature');
        $xRequestId = $request->header('x-request-id');

        if (!$xSignature || !$xRequestId) {
            return false;
        }

        $signatureParts = [];
        foreach (explode(',', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, null);
            if ($key && $value) {
                $signatureParts[trim($key)] = trim($value);
            }
        }

        $timestamp = $signatureParts['ts'] ?? null;
        $receivedHash = $signatureParts['v1'] ?? null;
        $dataId = $request->input('data.id') ?? $request->input('data_id') ?? $request->input('id');

        if (!$timestamp || !$receivedHash || !$dataId) {
            return false;
        }

        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$timestamp};";
        $calculatedHash = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($calculatedHash, $receivedHash);
    }

    protected function syncMerchantOrder(string $merchantOrderId): void
    {
        // Log::info('🛍️ syncMerchantOrder iniciado', ['merchant_order_id' => $merchantOrderId]);

        $accessToken = MercadoPagoConfig::getAccessToken();

        if (!$accessToken) {
            // Log::error('❌ Mercado Pago NO CONFIGURADO - No hay access token');
            return;
        }

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->get("https://api.mercadopago.com/merchant_orders/{$merchantOrderId}");

        if (!$response->successful()) {
            // Log::error('❌ No se pudo consultar merchant order en Mercado Pago API', [
            //     'merchant_order_id' => $merchantOrderId,
            //     'status' => $response->status(),
            //     'response' => $response->json(),
            // ]);
            return;
        }

        $merchantOrderData = $response->json();
        $payments = $merchantOrderData['payments'] ?? [];
        
        // Log::info('✅ Merchant order consultado exitosamente', [
        //     'merchant_order_id' => $merchantOrderId,
        //     'total_payments' => count($payments),
        //     'statuses' => array_unique(array_column($payments, 'status')),
        // ]);

        foreach ($payments as $payment) {
            if (!empty($payment['id'])) {
                // Log::info('→ Sincronizando pago desde merchant order', ['payment_id' => $payment['id']]);
                $this->syncPayment((string) $payment['id']);
            }
        }
    }

    protected function syncPayment(string $paymentId): void
    {
        // Log::info('💳 syncPayment iniciado', ['payment_id' => $paymentId]);
        
        $accessToken = MercadoPagoConfig::getAccessToken();

        if (!$accessToken) {
            // Log::error('❌ Mercado Pago NO CONFIGURADO - No hay access token');
            return;
        }

        //  Log::info('✅ Access token de Mercado Pago disponible');

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (!$response->successful()) {
            Log::error('❌ Error al consultar pago en Mercado Pago API', [
                'payment_id' => $paymentId,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            return;
        }

        $payment = $response->json();
        // Log::info('✅ Pago consultado exitosamente en Mercado Pago', [
        //     'payment_id' => $paymentId,
        //     'status' => $payment['status'] ?? 'UNKNOWN',
        //     'external_reference' => $payment['external_reference'] ?? null,
        // ]);

        $order = Order::find($payment['external_reference'] ?? null);

        if (!$order) {
            Log::error('❌ Pago recibido pero NO HAY ORDEN ASOCIADA', [
                'payment_id' => $paymentId,
                'external_reference' => $payment['external_reference'] ?? null,
            ]);
            return;
        }

        // Log::info('✅ Orden encontrada', [
        //     'order_id' => $order->id,
        //     'order_number' => $order->order_number,
        // ]);

        $existingPay = Pay::where('id_pago', (string) $payment['id'])->first();
        $previousStatus = $existingPay?->estado;

        // Log::info('📝 Información anterior del pago', [
            // 'existing_pay_id' => $existingPay?->id,
        //     'previous_status' => $previousStatus,
        // ]);

        $pay = Pay::updateOrCreate(
            ['id_pago' => (string) $payment['id']],
            [
                'order_id' => $order->id,
                'payment_id' => $payment['external_reference'] ?? null,
                'descripcion' => $payment['description'] ?? $order->order_number,
                'monto_transaccion' => $payment['transaction_amount'] ?? 0,
                'monto_recibido_neto' => $payment['transaction_details']['net_received_amount'] ?? 0,
                'monto_a_pagar' => $payment['transaction_amount'] ?? 0,
                'codigo_autorizacion' => $payment['authorization_code'] ?? null,
                'estado' => $payment['status'] ?? null,
                'fecha_aprobacion' => $payment['date_approved'] ?? null,
                'fecha_creacion' => $payment['date_created'] ?? now()->toDateTimeString(),
                'fecha_ultima_actualizacion' => $payment['date_last_updated'] ?? null,
                'metodo_pago' => $payment['payment_method_id'] ?? $payment['payment_method']['id'] ?? null,
                'numero_tarjeta' => isset($payment['card']['first_six_digits'], $payment['card']['last_four_digits'])
                    ? $payment['card']['first_six_digits'] . '******' . $payment['card']['last_four_digits']
                    : null,
                'ip_direccion' => $payment['additional_info']['ip_address'] ?? null,
                'url_notificacion' => $payment['notification_url'] ?? null,
            ]
        );

        $currentStatus = $payment['status'] ?? null;

        // Log::info('✅ Registro de pago creado/actualizado', [
        //     'pay_id' => $pay->id,
        //     'id_pago' => $pay->id_pago,
        //     'current_status' => $currentStatus,
        // ]);

        $paymentStatus = $this->mapPaymentStatus($currentStatus);
        $orderStatus = $paymentStatus === 'pagado' ? 'procesando' : 'pendiente';

        // Log::info('🔄 Actualizando estado de la orden', [
        //     'order_id' => $order->id,
        //     'payment_status' => $paymentStatus,
        //     'order_status' => $orderStatus,
        // ]);

        $order->update([
            'payment_status' => $paymentStatus,
            'order_status' => $orderStatus,
            'payment_id' => (string) $payment['id'],
            'metodo_pago' => 'mercado_pago',
        ]);

        // Log::info('✅ Orden actualizada exitosamente', [
        //     'order_id' => $order->id,
        //     'new_payment_status' => $paymentStatus,
        // ]);

        if ($currentStatus === 'approved' && $previousStatus !== 'approved') {
            // Log::info('📧 Enviando correos de orden aprobada...');
            $this->sendApprovedOrderMail($order->fresh('items'), $pay);
        } else {
            // Log::info('ℹ️ Correos NO enviados', [
            //     'current_status' => $currentStatus,
            //     'previous_status' => $previousStatus,
            //     'reason' => $currentStatus === 'approved' ? 'Ya se enviaron antes' : 'El pago no está aprobado',
            // ]);
        }
    }

    protected function sendApprovedOrderMail(Order $order, Pay $pay): void
    {
        $adminEmail = config('mail.admin_email') ?: config('mail.from.address');

        if (!$adminEmail) {
            Log::warning('No hay correo configurado para notificar orden aprobada', [
                'order_id' => $order->id,
                'payment_id' => $pay->id_pago,
            ]);
        } else {
            $this->deliverApprovedOrderMail(
                $adminEmail,
                new OrdenAprobada($order, $pay),
                'Correo de orden aprobada enviado',
                'No se pudo enviar el correo de orden aprobada',
                $order,
                $pay,
            );
        }

        if ($order->customer_email) {
            $this->deliverApprovedOrderMail(
                $order->customer_email,
                new OrdenClienteAprobada($order, $pay),
                'Correo de confirmacion de pedido enviado al cliente',
                'No se pudo enviar el correo de confirmacion al cliente',
                $order,
                $pay,
            );
        }
    }

    protected function deliverApprovedOrderMail(
        string $recipient,
        mixed $mailable,
        string $successMessage,
        string $errorMessage,
        Order $order,
        Pay $pay,
    ): void {
        try {
            Mail::to($recipient)->send($mailable);

            // Log::info('✅ ' . $successMessage, [
            //     'order_id' => $order->id,
            //     'payment_id' => $pay->id_pago,
            //     'recipient' => $recipient,
            // ]);
        } catch (\Throwable $exception) {
            Log::error('❌ ' . $errorMessage, [
                'order_id' => $order->id,
                'payment_id' => $pay->id_pago,
                'recipient' => $recipient,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    protected function mapPaymentStatus(?string $status): string
    {
        return match ($status) {
            'approved' => 'pagado',
            'pending', 'in_process' => 'pendiente_pago',
            'rejected', 'cancelled', 'refunded', 'charged_back' => 'rechazado',
            default => 'pendiente_pago',
        };
    }

    /**
     * Manejar webhook de PayPal
     */
    public function handlePayPalWebhook(Request $request)
    {
        // Log::info('✅ Webhook de PayPal recibido', $request->all());

        // Verificar la firma del webhook (PayPal usa un sistema diferente)
        if (!$this->validatePayPalWebhookSignature($request)) {
            Log::error('❌ Webhook de PayPal con firma INVÁLIDA');
            return response()->json(['success' => false, 'message' => 'Firma inválida'], 401);
        }

        $eventType = $request->input('event_type');
        $resource = $request->input('resource', []);

        // Log::info('📌 Evento de PayPal', [
        //     'event_type' => $eventType,
        //     'resource_id' => $resource['id'] ?? null,
        // ]);

        // Manejar diferentes tipos de eventos de PayPal
        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':
                // Log::info('→ Procesando CHECKOUT.ORDER.APPROVED');
                $this->handlePayPalOrderApproved($resource);
                break;
            case 'PAYMENT.CAPTURE.COMPLETED':
                // Log::info('→ Procesando PAYMENT.CAPTURE.COMPLETED');
                $this->handlePayPalPaymentCaptured($resource);
                break;
            case 'PAYMENT.CAPTURE.PENDING':
                // Log::info('→ Procesando PAYMENT.CAPTURE.PENDING');
                $this->handlePayPalPaymentPending($resource);
                break;
            case 'PAYMENT.CAPTURE.DENIED':
            case 'PAYMENT.CAPTURE.DECLINED':
                // Log::info('→ Procesando PAYMENT.CAPTURE.DENIED/DECLINED');
                $this->handlePayPalPaymentFailed($resource);
                break;
            default:
                // Log::info('ℹ️ Evento de PayPal no manejado', ['event_type' => $eventType]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Validar firma del webhook de PayPal
     */
    protected function validatePayPalWebhookSignature(Request $request): bool
    {
        // PayPal usa headers específicos para validar webhooks:
        // PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-TRANSMISSION-SIG, etc.
        // Para una implementación completa, se debe verificar usando la API de PayPal
        // Por ahora, permitimos todos los webhooks en desarrollo
        // En producción, se debe implementar la validación completa

        $transmissionId = $request->header('PAYPAL-TRANSMISSION-ID');
        $transmissionTime = $request->header('PAYPAL-TRANSMISSION-TIME');
        $transmissionSig = $request->header('PAYPAL-TRANSMISSION-SIG');
        $certUrl = $request->header('PAYPAL-CERT-URL');
        $authAlgo = $request->header('PAYPAL-AUTH-ALGO');

        // Si está en modo sandbox o no hay configuración completa, permitir
        if (PayPalConfig::getMode() === 'sandbox' || !$transmissionId) {
            return true;
        }

        // En producción, aquí se debe implementar la validación completa
        // usando la API de PayPal para verificar el webhook
        // Referencia: https://developer.paypal.com/api/rest/webhooks/

        return true;
    }

    /**
     * Manejar orden aprobada de PayPal
     */
    protected function handlePayPalOrderApproved(array $resource): void
    {
        // Log::info('🎯 Orden de PayPal aprobada recibida', $resource);

        $paypalOrderId = $resource['id'] ?? null;

        if (!$paypalOrderId) {
            // Log::error('❌ Webhook de PayPal sin ID de orden');
            return;
        }

        $order = Order::where('payment_id', $paypalOrderId)->first();

        if (!$order) {
            // Log::error('❌ Orden de PayPal no encontrada', ['paypal_order_id' => $paypalOrderId]);
            return;
        }

        // Log::info('✅ Orden de PayPal aprobada, capturando pago...', [
        //     'order_id' => $order->id,
        //     'order_number' => $order->order_number,
        //     'paypal_order_id' => $paypalOrderId,
        // ]);

        // Capturar el pago automáticamente
        $accessToken = PayPalConfig::getAccessToken();

        if (!$accessToken) {
            // Log::error('❌ No se puede capturar pago de PayPal: no hay token', [
            //     'order_id' => $order->id,
            // ]);
            return;
        }

        $apiUrl = PayPalConfig::getApiUrl();

        try {
            $response = Http::withToken($accessToken)
                ->contentType('application/json')
                ->acceptJson()
                ->timeout(30)
                ->withBody('', 'application/json')
                ->post($apiUrl . "/v2/checkout/orders/{$paypalOrderId}/capture");

            if ($response->successful()) {
                $data = $response->json();
                $captureStatus = $data['status'] ?? 'UNKNOWN';

                // Log::info('✅ Pago de PayPal capturado exitosamente', [
                //     'order_id' => $order->id,
                //     'paypal_order_id' => $paypalOrderId,
                //     'capture_status' => $captureStatus,
                //     'capture_data' => $data,
                // ]);

                if ($captureStatus === 'COMPLETED') {
                    // Extraer información del capture
                    $capture = $data['purchase_units'][0]['payments']['captures'][0] ?? null;
                    $payer = $data['payer'] ?? null;

                    // Log::info('📝 Información del capture extraída', [
                    //     'capture_id' => $capture['id'] ?? null,
                    //     'amount' => $capture['amount']['value'] ?? null,
                    // ]);

                    // Crear registro en la tabla pays
                    $pay = Pay::updateOrCreate(
                        ['id_pago' => $paypalOrderId],
                        [
                            'order_id' => $order->id,
                            'payment_id' => $order->id,
                            'descripcion' => 'Pago PayPal - Orden #' . $order->order_number,
                            'monto_transaccion' => $capture ? (float) $capture['amount']['value'] : $order->total,
                            'monto_recibido_neto' => $capture ? (float) ($capture['seller_receivable_breakdown']['net_amount']['value'] ?? $capture['amount']['value']) : $order->total,
                            'monto_a_pagar' => $order->total,
                            'codigo_autorizacion' => $capture['id'] ?? null,
                            'estado' => 'approved',
                            'fecha_aprobacion' => $capture['create_time'] ?? now()->toDateTimeString(),
                            'fecha_creacion' => $capture['create_time'] ?? now()->toDateTimeString(),
                            'fecha_ultima_actualizacion' => $capture['update_time'] ?? now()->toDateTimeString(),
                            'metodo_pago' => 'paypal',
                            'numero_tarjeta' => null,
                            'ip_direccion' => null,
                            'url_notificacion' => null,
                        ]
                    );

                    // Log::info('✅ Registro de pago PayPal creado', ['pay_id' => $pay->id]);

                    $order->update([
                        'payment_status' => 'pagado',
                        'order_status' => 'procesando',
                    ]);

                    // Log::info('✅ Orden actualizada a PAGADO', ['order_id' => $order->id]);

                    // Enviar correos de confirmación
                    // Log::info('📧 Enviando correos de confirmación...');
                    $this->sendApprovedOrderMail($order->fresh('items'), $pay);
                } else {
                    Log::warning('⚠️ Capture NO completado', ['capture_status' => $captureStatus]);
                    $order->update([
                        'payment_status' => 'pendiente_pago',
                        'order_status' => 'pendiente',
                    ]);
                }
            } else {
                Log::error('❌ Error al capturar pago de PayPal desde webhook', [
                    'order_id' => $order->id,
                    'paypal_order_id' => $paypalOrderId,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                $order->update([
                    'payment_status' => 'pendiente_pago',
                    'order_status' => 'pendiente',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('❌ Excepción al capturar pago de PayPal', [
                'order_id' => $order->id,
                'paypal_order_id' => $paypalOrderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Manejar pago capturado de PayPal
     */
    protected function handlePayPalPaymentCaptured(array $resource): void
    {
        // El resource contiene información del capture
        // Buscar la orden relacionada
        $customId = $resource['custom_id'] ?? null;
        $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        // Log::info('📋 Payment Captured event recibido', [
        //     'capture_id' => $resource['id'] ?? null,
        //     'paypal_order_id' => $paypalOrderId,
        // ]);

        if (!$paypalOrderId) {
            Log::error('❌ Webhook de PayPal sin ID de orden relacionado');
            return;
        }

        $order = Order::where('payment_id', $paypalOrderId)->first();

        if (!$order) {
            Log::error('❌ Orden de PayPal no encontrada para capture', ['paypal_order_id' => $paypalOrderId]);
            return;
        }

        // Log::info('✅ Pago de PayPal capturado', [
        //     'order_id' => $order->id,
        //     'paypal_order_id' => $paypalOrderId,
        //     'capture_id' => $resource['id'] ?? null,
        // ]);

        // Crear o actualizar registro en la tabla pays
        $pay = Pay::updateOrCreate(
            ['id_pago' => $paypalOrderId],
            [
                'order_id' => $order->id,
                'payment_id' => $order->id,
                'descripcion' => 'Pago PayPal - Orden #' . $order->order_number,
                'monto_transaccion' => $resource['amount']['value'] ?? $order->total,
                'monto_recibido_neto' => $resource['seller_receivable_breakdown']['net_amount']['value'] ?? $resource['amount']['value'] ?? $order->total,
                'monto_a_pagar' => $order->total,
                'codigo_autorizacion' => $resource['id'] ?? null,
                'estado' => 'approved',
                'fecha_aprobacion' => $resource['create_time'] ?? now()->toDateTimeString(),
                'fecha_creacion' => $resource['create_time'] ?? now()->toDateTimeString(),
                'fecha_ultima_actualizacion' => $resource['update_time'] ?? now()->toDateTimeString(),
                'metodo_pago' => 'paypal',
                'numero_tarjeta' => null,
                'ip_direccion' => null,
                'url_notificacion' => null,
            ]
        );

        // Log::info('✅ Registro de pago PayPal capturado creado/actualizado', [
        //     'pay_id' => $pay->id,
        //     'id_pago' => $pay->id_pago,
        // ]);

        // Actualizar el estado de la orden a aprobado
        $order->update([
            'payment_status' => 'pagado',
            'order_status' => 'procesando',
        ]);

        // Log::info('✅ Orden actualizada a PAGADO', ['order_id' => $order->id]);

        // Enviar correos de confirmación
        // Log::info('📧 Enviando correos de confirmación...');
        $this->sendApprovedOrderMail($order->fresh('items'), $pay);
    }

    /**
     * Manejar pago pendiente de PayPal (En revisión)
     */
    protected function handlePayPalPaymentPending(array $resource): void
    {
        $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        // Log::info('⏳ Pago de PayPal pendiente de revisión', [
        //     'paypal_order_id' => $paypalOrderId,
        //     'reason' => $resource['status_details']['reason'] ?? 'PENDING_REVIEW',
        // ]);

        if (!$paypalOrderId) {
            // Log::error('❌ Webhook de PayPal PENDING sin ID de orden relacionado');
            return;
        }

        $order = Order::where('payment_id', $paypalOrderId)->first();

        if (!$order) {
            Log::error('❌ Orden de PayPal no encontrada para pago PENDING', ['paypal_order_id' => $paypalOrderId]);
            return;
        }

        // Log::info('✅ Pago de PayPal pendiente de revisión detectado', [
        //     'order_id' => $order->id,
        //     'paypal_order_id' => $paypalOrderId,
        //     'reason' => $resource['status_details']['reason'] ?? 'PENDING_REVIEW',
        // ]);

        // Crear o actualizar registro en la tabla pays con estado PENDING
        $pay = Pay::updateOrCreate(
            ['id_pago' => $paypalOrderId],
            [
                'order_id' => $order->id,
                'payment_id' => $order->id,
                'descripcion' => 'Pago PayPal Pendiente - Orden #' . $order->order_number,
                'monto_transaccion' => $resource['amount']['value'] ?? $order->total,
                'monto_recibido_neto' => $resource['seller_receivable_breakdown']['net_amount']['value'] ?? $resource['amount']['value'] ?? $order->total,
                'monto_a_pagar' => $order->total,
                'codigo_autorizacion' => $resource['id'] ?? null,
                'estado' => 'pending_review',
                'fecha_aprobacion' => now()->toDateTimeString(),
                'fecha_creacion' => $resource['create_time'] ?? now()->toDateTimeString(),
                'fecha_ultima_actualizacion' => $resource['update_time'] ?? now()->toDateTimeString(),
                'metodo_pago' => 'paypal',
                'numero_tarjeta' => null,
                'ip_direccion' => null,
                'url_notificacion' => null,
            ]
        );

        // Log::info('✅ Registro de pago PENDING creado', ['pay_id' => $pay->id]);

        // Actualizar el estado de la orden a pendiente de revisión
        $order->update([
            'payment_status' => 'pendiente_revision',
            'order_status' => 'pendiente',
        ]);

        // Log::info('✅ Orden actualizada a estado PENDIENTE_REVISION', [
        //     'order_id' => $order->id,
        //     'payment_status' => 'pendiente_revision',
        // ]);
    }

    /**
     * Manejar pago fallido de PayPal
     */
    protected function handlePayPalPaymentFailed(array $resource): void
    {
        $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;

        Log::error('❌ Pago de PayPal rechazado', [
            'paypal_order_id' => $paypalOrderId,
            'reason' => $resource['status_details']['reason'] ?? 'UNKNOWN',
        ]);

        if (!$paypalOrderId) {
            Log::error('❌ Webhook de PayPal rechazado sin ID de orden relacionado');
            return;
        }

        $order = Order::where('payment_id', $paypalOrderId)->first();

        if (!$order) {
            Log::error('❌ Orden de PayPal no encontrada para rechazo', ['paypal_order_id' => $paypalOrderId]);
            return;
        }

        // Log::info('🔄 Actualizando orden a estado RECHAZADO', [
        //     'order_id' => $order->id,
        //     'paypal_order_id' => $paypalOrderId,
        // ]);

        $order->update([
            'payment_status' => 'rechazado',
            'order_status' => 'cancelado',
        ]);

        // Log::info('✅ Orden marcada como RECHAZADA', [
        //     'order_id' => $order->id,
        // ]);
    }
}
