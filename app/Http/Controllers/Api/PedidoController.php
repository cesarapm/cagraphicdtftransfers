<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pay;
use App\Services\MercadoPagoConfig;
use App\Services\PayPalConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    public function crearPedido(Request $request)
    {
        try {
            $validated = $this->validateCheckoutPayload($request);
            $order = $this->storeOrder($validated, 'mercado_pago', 'pendiente_pago', 'pendiente');
            $preference = $this->crearPreferenciaPago($order);

            if (!empty($preference['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo iniciar el pago con Mercado Pago.',
                ], 502);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orden creada y lista para pagar.',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'metodo_pago' => $order->metodo_pago,
                ],
                'checkout_url' => $preference['init_point'] ?? $preference['sandbox_init_point'] ?? null,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Error al crear pedido con Mercado Pago', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear el pedido.',
            ], 500);
        }
    }

    public function crearPedidoTransferencia(Request $request)
    {
        try {
            $validated = $this->validateCheckoutPayload($request);
            $order = $this->storeOrder($validated, 'transferencia', 'pendiente_pago', 'pendiente');

            return response()->json([
                'success' => true,
                'message' => 'Orden creada con pago por transferencia.',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'metodo_pago' => $order->metodo_pago,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Error al crear pedido por transferencia', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear el pedido de transferencia.',
            ], 500);
        }
    }

    public function ordenescancelar($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->update([
                'payment_status' => 'rechazado',
                'order_status' => 'cancelado',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Orden cancelada correctamente.',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Error al cancelar orden', [
                'order_id' => $id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo cancelar la orden.',
            ], 500);
        }
    }

    public function cancelarOrdenPorPagoFallido(Request $request)
    {
        $validated = $request->validate([
            'orden_id' => 'required|exists:orders,id',
        ]);

        try {
            $order = Order::findOrFail($validated['orden_id']);

            if (in_array($order->payment_status, ['pendiente_pago'], true)) {
                $order->update([
                    'payment_status' => 'rechazado',
                    'order_status' => 'cancelado',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizada tras pago fallido.',
                'order' => [
                    'id' => $order->id,
                    'status' => $order->status,
                ],
            ]);
        } catch (\Throwable $exception) {
            Log::error('Error al cancelar orden por pago fallido', [
                'order_id' => $validated['orden_id'],
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar la orden.',
            ], 500);
        }
    }

    public function ordenesRecientes(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
            ]);

            $orders = Order::where('customer_email', $validated['email'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'order_number', 'total', 'payment_status', 'order_status', 'metodo_pago', 'created_at']);

            $formattedOrders = $orders->map(function ($order) {
                // Generar etiqueta de estado según payment_status
                $status_label = '⏳ Pendiente de pago';
                
                if ($order->payment_status === 'pagado') {
                    $status_label = '✅ Pagado';
                } elseif ($order->payment_status === 'rechazado') {
                    $status_label = '❌ Rechazado';
                } elseif ($order->payment_status === 'pendiente_pago') {
                    if ($order->metodo_pago === 'transferencia') {
                        $status_label = '🏦 Pendiente de transferencia';
                    } else {
                        $status_label = '⏳ Pendiente de pago';
                    }
                }

                return [
                    'order_number' => $order->order_number,
                    'total' => (float) $order->total,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'metodo_pago' => $order->metodo_pago,
                    'status_label' => $status_label,
                    'created_at' => $order->created_at->format('d/m/Y'),
                ];
            });

            return response()->json([
                'success' => true,
                'orders' => $formattedOrders,
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Error al consultar órdenes recientes', [
                'email' => $request->input('email'),
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudieron cargar las órdenes.',
                'orders' => [],
            ], 500);
        }
    }

    private function validateCheckoutPayload(Request $request): array
    {
        return $request->validate([
            'customer_first_name' => 'required|string|max:255',
            'customer_last_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:30',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip_code' => 'required|string|max:20',
            'subtotal' => 'required|numeric|min:0',
            'shipping_cost' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'save_customer_profile' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ], [
            'customer_first_name.required' => 'El nombre es obligatorio.',
            'customer_last_name.required' => 'El apellido es obligatorio.',
            'customer_email.required' => 'El correo electrónico es obligatorio.',
            'customer_email.email' => 'Escribe un correo electrónico válido.',
            'customer_phone.required' => 'El teléfono es obligatorio.',
            'shipping_address.required' => 'La dirección de envío es obligatoria.',
            'shipping_city.required' => 'La ciudad es obligatoria.',
            'shipping_state.required' => 'El estado es obligatorio.',
            'shipping_zip_code.required' => 'El código postal es obligatorio.',
            'items.required' => 'Tu carrito está vacío.',
            'items.*.product_id.exists' => 'Uno de los productos ya no está disponible.',
        ]);
    }

    private function storeOrder(array $validated, string $paymentMethod, string $paymentStatus, string $orderStatus): Order
    {
        return DB::transaction(function () use ($validated, $paymentMethod, $paymentStatus, $orderStatus) {
            $calculatedSubtotal = collect($validated['items'])
                ->sum(fn (array $item) => (float) $item['unit_price'] * (int) $item['quantity']);
            $shippingCost = (float) $validated['shipping_cost'];
            $tax = 0;
            $calculatedTotal = $calculatedSubtotal + $shippingCost + $tax;
            $customer = null;

            if (!empty($validated['save_customer_profile'])) {
                $customer = Customer::updateOrCreate(
                    ['email' => strtolower((string) $validated['customer_email'])],
                    [
                        'first_name' => $validated['customer_first_name'],
                        'last_name' => $validated['customer_last_name'],
                        'phone' => $validated['customer_phone'],
                        'address' => $validated['shipping_address'],
                        'city' => $validated['shipping_city'],
                        'state' => $validated['shipping_state'],
                        'zip_code' => $validated['shipping_zip_code'],
                        'last_ordered_at' => now(),
                    ]
                );
            }

            $order = Order::create([
                'customer_id' => $customer?->id,
                'customer_first_name' => $validated['customer_first_name'],
                'customer_last_name' => $validated['customer_last_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_zip_code' => $validated['shipping_zip_code'],
                'subtotal' => $calculatedSubtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $calculatedTotal,
                'metodo_pago' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'notes' => $validated['notes'] ?? null,
                'envio' => [
                    'address' => $validated['shipping_address'],
                    'city' => $validated['shipping_city'],
                    'state' => $validated['shipping_state'],
                    'zip_code' => $validated['shipping_zip_code'],
                ],
            ]);

            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => (float) $item['unit_price'] * (int) $item['quantity'],
                ]);
            }

            return $order->fresh('items');
        });
    }

    private function crearPreferenciaPago(Order $order): array
    {
        $accessToken = MercadoPagoConfig::getAccessToken();

        if (!$accessToken) {
            return ['error' => 'Mercado Pago no está configurado'];
        }

        $frontendUrl = rtrim(config('app.frontend_url', config('app.url')), '/');
        $notificationUrl = MercadoPagoConfig::getNotificationUrl() ?: url('/api/mercado-pago/webhook');

        $backUrls = [
            'success' => $frontendUrl . '/checkout/exito',
            'failure' => $frontendUrl . '/checkout/error',
            'pending' => $frontendUrl . '/checkout/pendiente',
        ];

        $payload = [
            'items' => $order->items->map(fn (OrderItem $item) => [
                'title' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'currency_id' => 'MXN',
            ])->values()->all(),
            'external_reference' => (string) $order->id,
            'notification_url' => $notificationUrl,
            'back_urls' => $backUrls,
            'statement_descriptor' => 'IZAGUIRREQU',
        ];

        if (str_starts_with($backUrls['success'], 'https://')) {
            $payload['auto_return'] = 'approved';
        }

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(30)
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Mercado Pago rechazó la preferencia', [
            'order_id' => $order->id,
            'status' => $response->status(),
            'response' => $response->json(),
            'payload' => $payload,
        ]);

        return [
            'error' => $response->json('message') ?: 'Error al crear preferencia',
            'details' => $response->json(),
        ];
    }

    /**
     * Crear pedido con PayPal
     */
    public function crearPedidoPayPal(Request $request)
    {
        try {
            $validated = $this->validateCheckoutPayload($request);
            $order = $this->storeOrder($validated, 'paypal', 'pendiente_pago', 'pendiente');
            $paypalOrder = $this->crearOrdenPayPal($order);

            if (!empty($paypalOrder['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo iniciar el pago con PayPal.',
                ], 502);
            }

            // Guardar el PayPal Order ID en la orden para referencia futura
            $order->update([
                'payment_id' => $paypalOrder['id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Orden creada y lista para pagar con PayPal.',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'metodo_pago' => $order->metodo_pago,
                ],
                'checkout_url' => $paypalOrder['approval_url'] ?? null,
                'paypal_order_id' => $paypalOrder['id'] ?? null,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Error al crear pedido con PayPal', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear el pedido con PayPal.',
            ], 500);
        }
    }

    /**
     * Capturar pago de PayPal después de la aprobación del usuario
     */
    public function capturarPagoPayPal(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'paypal_order_id' => 'required|string',
            ]);

            $order = Order::findOrFail($validated['order_id']);

            // Capturar el pago en PayPal
            $result = $this->ejecutarPagoPayPal($validated['paypal_order_id']);

            if (!empty($result['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo capturar el pago de PayPal.',
                ], 502);
            }

            // Actualizar el estado de la orden según el resultado
            $paymentStatus = $result['status'] ?? 'COMPLETED';
            
            if ($paymentStatus === 'COMPLETED') {
                // Extraer información del capture
                $capture = $result['purchase_units'][0]['payments']['captures'][0] ?? null;

                // Crear registro en la tabla pays
                Pay::updateOrCreate(
                    ['id_pago' => $validated['paypal_order_id']],
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

                $order->update([
                    'payment_status' => 'pagado',
                    'order_status' => 'procesando',
                    'payment_id' => $validated['paypal_order_id'],
                ]);
            } else {
                $order->update([
                    'payment_status' => 'pendiente_pago',
                    'order_status' => 'pendiente',
                    'payment_id' => $validated['paypal_order_id'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado correctamente.',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                ],
                'payment_status' => $paymentStatus,
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Error al capturar pago de PayPal', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo capturar el pago.',
            ], 500);
        }
    }

    /**
     * Crear orden en PayPal
     */
    private function crearOrdenPayPal(Order $order): array
    {
        $accessToken = PayPalConfig::getAccessToken();

        if (!$accessToken) {
            return ['error' => 'PayPal no está configurado'];
        }

        $apiUrl = PayPalConfig::getApiUrl();
        $frontendUrl = rtrim(config('app.frontend_url', config('app.url')), '/');

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => (string) $order->id,
                    'description' => 'Orden #' . $order->order_number,
                    'amount' => [
                        'currency_code' => 'MXN',
                        'value' => number_format((float) $order->total, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'MXN',
                                'value' => number_format((float) $order->subtotal, 2, '.', ''),
                            ],
                            'shipping' => [
                                'currency_code' => 'MXN',
                                'value' => number_format((float) $order->shipping_cost, 2, '.', ''),
                            ],
                        ],
                    ],
                    'items' => $order->items->map(fn (OrderItem $item) => [
                        'name' => $item->product_name,
                        'quantity' => (string) $item->quantity,
                        'unit_amount' => [
                            'currency_code' => 'MXN',
                            'value' => number_format((float) $item->unit_price, 2, '.', ''),
                        ],
                    ])->values()->all(),
                ],
            ],
            'application_context' => [
                'brand_name' => config('app.name', 'Ecommerce'),
                'return_url' => $frontendUrl . '/checkout/exito?order_id=' . $order->id,
                'cancel_url' => $frontendUrl . '/checkout/error?order_id=' . $order->id,
                'user_action' => 'PAY_NOW',
            ],
        ];

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(30)
            ->post($apiUrl . '/v2/checkout/orders', $payload);

        if ($response->successful()) {
            $data = $response->json();
            $approvalUrl = null;

            // Extraer la URL de aprobación
            foreach ($data['links'] ?? [] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            return [
                'id' => $data['id'],
                'status' => $data['status'],
                'approval_url' => $approvalUrl,
            ];
        }

        Log::error('PayPal rechazó la orden', [
            'order_id' => $order->id,
            'status' => $response->status(),
            'response' => $response->json(),
            'payload' => $payload,
        ]);

        return [
            'error' => $response->json('message') ?: 'Error al crear orden en PayPal',
            'details' => $response->json(),
        ];
    }

    /**
     * Capturar/ejecutar pago en PayPal
     */
    private function ejecutarPagoPayPal(string $paypalOrderId): array
    {
        $accessToken = PayPalConfig::getAccessToken();

        if (!$accessToken) {
            return ['error' => 'PayPal no está configurado'];
        }

        $apiUrl = PayPalConfig::getApiUrl();

        $response = Http::withToken($accessToken)
            ->contentType('application/json')
            ->acceptJson()
            ->timeout(30)
            ->withBody('', 'application/json')
            ->post($apiUrl . "/v2/checkout/orders/{$paypalOrderId}/capture");

        if ($response->successful()) {
            $data = $response->json();
            return [
                'id' => $data['id'],
                'status' => $data['status'],
                'payer' => $data['payer'] ?? null,
                'purchase_units' => $data['purchase_units'] ?? null,
            ];
        }

        Log::error('PayPal no pudo capturar el pago', [
            'paypal_order_id' => $paypalOrderId,
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        return [
            'error' => $response->json('message') ?: 'Error al capturar pago en PayPal',
            'details' => $response->json(),
        ];
    }
}
