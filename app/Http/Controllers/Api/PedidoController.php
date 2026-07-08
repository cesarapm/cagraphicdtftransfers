<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DtfSize;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pay;
use App\Models\Product;
use App\Services\MercadoPagoConfig;
use App\Services\PayPalConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            $orderId = $validated['orden_id'];
            Log::info('Iniciando cancelación de orden por pago fallido', ['order_id' => $orderId]);

            $order = Order::findOrFail($orderId);
            
            Log::info('Orden encontrada', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
            ]);

            // Permitir eliminar si está en: pendiente_pago, rechazado o cancelado
            $deletableStatuses = ['pendiente_pago', 'rechazado', 'cancelado'];
            
            if (in_array($order->payment_status, $deletableStatuses, true)) {
                Log::info('Orden está en estado eliminable, procediendo a borrar', [
                    'order_id' => $order->id,
                    'current_status' => $order->payment_status,
                ]);

                // Contar items antes de eliminar
                $itemCount = $order->items()->count();
                Log::info('Items encontrados en la orden', [
                    'order_id' => $order->id,
                    'item_count' => $itemCount,
                ]);

                // Eliminar la orden (esto activará el evento deleting del modelo)
                $deleteResult = $order->delete();
                
                Log::info('Orden eliminada correctamente', [
                    'order_id' => $orderId,
                    'order_number' => $order->order_number,
                    'items_removed' => $itemCount,
                    'delete_result' => $deleteResult,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Orden cancelada y eliminada correctamente.',
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'items_removed' => $itemCount,
                    ],
                ]);
            } else {
                Log::warning('Orden no está en estado eliminable, no se puede borrar', [
                    'order_id' => $order->id,
                    'current_status' => $order->payment_status,
                    'allowed_statuses' => $deletableStatuses,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'La orden no está en estado eliminable (pendiente_pago, rechazado o cancelado). Estado actual: ' . $order->payment_status,
                    'order' => [
                        'id' => $order->id,
                        'current_status' => $order->payment_status,
                        'allowed_statuses' => $deletableStatuses,
                    ],
                ], 400);
            }
        } catch (\Throwable $exception) {
            Log::error('Error al cancelar orden por pago fallido', [
                'order_id' => $validated['orden_id'] ?? null,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar la orden.',
                'error' => config('app.debug') ? $exception->getMessage() : null,
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
        $isAuthenticated = auth()->check();

        // Datos del cliente: requeridos si NO está autenticado
        $customerRules = [
            'customer_first_name' => $isAuthenticated ? 'nullable|string|max:255' : 'required|string|max:255',
            'customer_last_name' => $isAuthenticated ? 'nullable|string|max:255' : 'required|string|max:255',
            'customer_email' => $isAuthenticated ? 'nullable|email|max:255' : 'required|email|max:255',
            'customer_phone' => $isAuthenticated ? 'nullable|string|max:30' : 'required|string|max:30',
        ];

        // Si está autenticado, usar datos del usuario autenticado
        if ($isAuthenticated && auth()->user()) {
            $user = auth()->user();
            if (empty($request->input('customer_first_name'))) {
                $request->merge(['customer_first_name' => $user->first_name ?? '']);
            }
            if (empty($request->input('customer_last_name'))) {
                $request->merge(['customer_last_name' => $user->last_name ?? '']);
            }
            if (empty($request->input('customer_email'))) {
                $request->merge(['customer_email' => $user->email ?? '']);
            }
            if (empty($request->input('customer_phone'))) {
                $request->merge(['customer_phone' => $user->phone ?? '']);
            }
        }

        return $request->validate(array_merge($customerRules, [
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip_code' => 'required|string|max:20',
            'subtotal' => 'required|numeric|min:0',
            'shipping_cost' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'save_customer_profile' => 'nullable|boolean',
            'payment_method' => 'required|in:mercado_pago,paypal,transferencia',
            'items' => 'required|array|min:1',
            'items.*.type' => 'nullable|string|in:size,product,gang,gang_sheet,custom', // Extensible types
            'items.*.product_id' => 'required|integer',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.image' => 'nullable|string',
            'items.*.gangSheetid' => 'nullable|integer', // Para gang sheets personalizadas

        ]), [
            'customer_first_name.required' => 'El nombre es obligatorio.',
            'customer_last_name.required' => 'El apellido es obligatorio.',
            'customer_email.required' => 'El correo electrónico es obligatorio.',
            'customer_email.email' => 'Escribe un correo electrónico válido.',
            'customer_phone.required' => 'El teléfono es obligatorio.',
            'shipping_address.required' => 'La dirección de envío es obligatoria.',
            'shipping_city.required' => 'La ciudad es obligatoria.',
            'shipping_state.required' => 'El estado es obligatorio.',
            'shipping_zip_code.required' => 'El código postal es obligatorio.',
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_method.in' => 'El método de pago seleccionado no es válido.',
            'items.required' => 'Tu carrito está vacío.',
            'items.min' => 'Tu carrito debe tener al menos un producto.',
            'items.*.product_id.required' => 'Cada producto debe tener un ID válido.',

        ]);
    }

    private function storeOrder(array $validated, string $paymentMethod, string $paymentStatus, string $orderStatus): Order
    {
        return DB::transaction(function () use ($validated, $paymentMethod, $paymentStatus, $orderStatus) {
            $calculatedSubtotal = collect($validated['items'])
                ->sum(fn(array $item) => (float) $item['unit_price'] * (int) $item['quantity']);
            $shippingCost = (float) $validated['shipping_cost'];
            $tax = 0;
            $calculatedTotal = $calculatedSubtotal + $shippingCost + $tax;
            $customer = null;

            // Si está autenticado, usar el cliente autenticado
            if (auth()->check() && auth()->user()) {
                $customer = auth()->user();
                // Actualizar datos del cliente si viene información
                if (!empty($validated['customer_phone'])) {
                    $customer->update([
                        'phone' => $validated['customer_phone'],
                        'address' => $validated['shipping_address'],
                        'city' => $validated['shipping_city'],
                        'state' => $validated['shipping_state'],
                        'zip_code' => $validated['shipping_zip_code'],
                        'last_ordered_at' => now(),
                    ]);
                }
            } else {
                // Si no está autenticado, buscar/crear cliente por email
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
                // Usar el campo 'type' para determinar qué tipo de item es
                // Si no viene type, intentar detectar automáticamente
                $itemType = $item['type'] ?? null;
                $productId = null;
                $dtfSizeId = null;
                $dtfGangId = null;
                $sheetSizeId = null;
                $gangSheetId = null;

                // Log::info('🔍 PROCESSING ITEM FROM FRONTEND', [
                //     'received_product_id' => $item['product_id'] ?? 'NOT_PROVIDED',
                //     'received_type' => $itemType,
                //     'full_item' => $item,
                // ]);

                // Si no viene type, intentar detectar basándose en si existe en Product o DtfSize
                if (empty($itemType)) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $itemType = 'product';
                        $productId = $item['product_id'];
                    } else {
                        $dtfSize = DtfSize::find($item['product_id']);
                        if ($dtfSize) {
                            $itemType = 'size';
                            $dtfSizeId = $item['product_id'];
                        } else {
                            $itemType = 'unknown';
                        }
                    }

                    // Log::info('📝 Auto-detected item type (no type provided)', [
                    //     'detected_type' => $itemType,
                    //     'product_id' => $item['product_id'],
                    //     'product_name' => $item['product_name'],
                    // ]);
                } else {
                    // Inicializar todas las variables a null
                    $productId = null;
                    $dtfSizeId = null;
                    $dtfGangId = null;
                    $sheetSizeId = null;
                    $gangSheetId = null;

                    // Mapear el product_id al campo correcto según el tipo
                    if ($itemType === 'size') {
                        // DTF Transfer Sizes → dtf_size_id
                        $dtfSizeId = $item['product_id'];
                        $dtfSize = DtfSize::find($dtfSizeId);

                        // Log::info('📝 Processing DTF Size Item', [
                        //     'dtf_size_id' => $dtfSizeId,
                        //     'product_name' => $item['product_name'],
                        //     'found_in_db' => $dtfSize ? 'Yes' : 'No',
                        // ]);

                        if (!$dtfSize) {
                            Log::error('❌ DTF Size not found', [
                                'dtf_size_id' => $dtfSizeId,
                                'product_name' => $item['product_name'],
                            ]);
                            throw new \Exception("DTF Size with ID {$dtfSizeId} not found in the system.");
                        }
                    } elseif ($itemType === 'gang') {
                        // Gang Sheets → dtf_gang_id
                        $dtfGangId = $item['product_id'];
                        $dtfGang = \App\Models\DtfGang::find($dtfGangId);

                        // Log::info('📝 Processing Gang Sheet Item', [
                        //     'dtf_gang_id' => $dtfGangId,
                        //     'product_name' => $item['product_name'],
                        //     'found_in_db' => $dtfGang ? 'Yes' : 'No',
                        // ]);

                        if (!$dtfGang) {
                            Log::error('❌ DtfGang not found', [
                                'dtf_gang_id' => $dtfGangId,
                                'product_name' => $item['product_name'],
                            ]);
                            throw new \Exception("Gang with ID {$dtfGangId} not found in the system.");
                        }
                    } elseif ($itemType === 'gang_sheet') {
                        // Check if it's a custom gang sheet design or a predefined size
                        $sheetSizeId = $item['product_id'];
                        

                        // Log::info('📝 Processing Gang Sheet Item', [
                        //     'item' => $item,
                        // ]);

                        if (!empty($item['gangSheetid'])) {
                            // Custom Gang Sheet Design → gang_sheet_id
                            $gangSheetId = $item['gangSheetid'];
                            $gangSheet = \App\Models\GangSheet::find($gangSheetId);

                            // Log::info('📝 Processing Custom Gang Sheet Design', [
                            //     'gang_sheet_id' => $gangSheetId,
                            //     'product_name' => $item['product_name'],
                            //     'found_in_db' => $gangSheet ? 'Yes' : 'No',
                            // ]);

                            if (!$gangSheet) {
                                Log::error('❌ GangSheet not found', [
                                    'gang_sheet_id' => $gangSheetId,
                                    'product_name' => $item['product_name'],
                                ]);
                                throw new \Exception("Custom Gang Sheet with ID {$gangSheetId} not found in the system.");
                            }
                        } else {
                            // Predefined Gang Sheet Sizes → sheet_size_id

                            $sheetSizeId = $item['product_id'];
                            $sheetSize = \App\Models\SheetSize::find($sheetSizeId);

                            // Log::info('📝 Processing Predefined Gang Sheet Size Item', [
                            //     'sheet_size_id' => $sheetSizeId,
                            //     'product_name' => $item['product_name'],
                            //     'found_in_db' => $sheetSize ? 'Yes' : 'No',
                            // ]);

                            if (!$sheetSize) {
                                Log::error('❌ SheetSize not found', [
                                    'sheet_size_id' => $sheetSizeId,
                                    'product_name' => $item['product_name'],
                                ]);
                                throw new \Exception("Sheet Size with ID {$sheetSizeId} not found in the system.");
                            }
                        }
                    } else {
                        // Productos regulares (product, custom, etc.) → product_id
                        $productId = $item['product_id'];
                        $product = Product::find($productId);

                        // Log::info('📝 Processing Regular Product Item', [
                        //     'product_id' => $productId,
                        //     'item_type' => $itemType,
                        //     'product_name' => $item['product_name'],
                        //     'found_in_db' => $product ? 'Yes' : 'No',
                        // ]);

                        if (!$product) {
                            Log::error('❌ Product not found', [
                                'product_id' => $productId,
                                'product_name' => $item['product_name'],
                            ]);
                            throw new \Exception("Product with ID {$productId} not found in the system.");
                        }
                    }
                }

                // Procesar imagen si existe
                $imagePath = null;
                if (!empty($item['image'])) {
                    $imagePath = $this->saveDtfImage($item['image'], $order->id);
                    // Log::info('📸 Image saved', ['image_path' => $imagePath]);
                }

                // Crear el OrderItem con los datos apropiados
                $createdItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'dtf_size_id' => $dtfSizeId,
                    'dtf_gang_id' => $dtfGangId,
                    'sheet_size_id' => $sheetSizeId,
                    'gang_sheet_id' => $gangSheetId,
                    'item_type' => $itemType,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => (float) $item['unit_price'] * (int) $item['quantity'],
                    'image' => $imagePath,
                ]);

                // Si es un gang_sheet con gangSheetid, actualizar el GangSheet con la relación a la orden
                if ($itemType === 'gang_sheet' && !empty($gangSheetId)) {
                    \App\Models\GangSheet::where('id', $gangSheetId)->update([
                        'order_id' => $order->id,
                        'customer_id' => $customer?->id,
                    ]);

                    // Log::info('🔗 GangSheet Updated with Order & Customer', [
                    //     'gang_sheet_id' => $gangSheetId,
                    //     'order_id' => $order->id,
                    //     'customer_id' => $customer?->id,
                    // ]);
                }

                // Log::info('✅ OrderItem Created Successfully', [
                //     'order_item_id' => $createdItem->id,
                //     'item_type' => $createdItem->item_type,
                //     'product_id' => $createdItem->product_id,
                //     'dtf_size_id' => $createdItem->dtf_size_id,
                //     'dtf_gang_id' => $createdItem->dtf_gang_id,
                //     'sheet_size_id' => $createdItem->sheet_size_id,
                //     'gang_sheet_id' => $createdItem->gang_sheet_id,
                //     'product_name' => $createdItem->product_name,
                //     'quantity' => $createdItem->quantity,
                //     'unit_price' => $createdItem->unit_price,
                //     'total' => $createdItem->total,
                //     'image' => $createdItem->image,
                // ]);
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
            'items' => $order->items->map(fn(OrderItem $item) => [
                'title' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'currency_id' => 'USD',
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


        // Log::info('Crear pedido PayPal request received', [
        //     'user_id' => auth()->id(),
        //     'session_id' => session()->getId(),
        //     'request_data' => $request->all(),
        // ]);
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
                        'currency_code' => 'USD',
                        'value' => number_format((float) $order->total, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'USD',
                                'value' => number_format((float) $order->subtotal, 2, '.', ''),
                            ],
                            'shipping' => [
                                'currency_code' => 'USD',
                                'value' => number_format((float) $order->shipping_cost, 2, '.', ''),
                            ],
                        ],
                    ],
                    'items' => $order->items->map(fn(OrderItem $item) => [
                        'name' => $item->product_name,
                        'quantity' => (string) $item->quantity,
                        'unit_amount' => [
                            'currency_code' => 'USD',
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

    /**
     * Guardar imagen de orden item desde base64 a storage
     * 
     * @param string $base64Image Imagen en formato base64
     * @param int $orderId ID de la orden
     * @return string Ruta relativa del archivo guardado
     */
    private function saveDtfImage(string $base64Image, int $orderId): ?string
    {
        try {
            // Validar que sea base64
            if (!preg_match('/^data:image\/(\w+);base64,(.+)$/', $base64Image, $matches)) {
                Log::warning('Invalid base64 image format for order item', [
                    'order_id' => $orderId,
                ]);
                return null;
            }

            $imageType = $matches[1]; // webp, png, jpg, etc.
            $imageData = $matches[2];
            $decodedImage = base64_decode($imageData, true);

            if ($decodedImage === false) {
                Log::warning('Failed to decode base64 image for order item', [
                    'order_id' => $orderId,
                ]);
                return null;
            }

            // Crear directorio si no existe (usando disk 'public')
            $directory = 'orderitems';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }

            // Generar nombre único: orderitem_[order_id]_[timestamp].[ext]
            $filename = 'orderitem_' . $orderId . '_' . time() . '.' . $imageType;
            $path = $directory . '/' . $filename;

            // Guardar el archivo en el disk 'public'
            Storage::disk('public')->put($path, $decodedImage);

            // Retornar la ruta relativa para guardar en BD (sin prefijo 'storage/')
            $relativePath = $path; // Solo: orderitems/orderitem_30_1783490418.webp

            // Log::info('✅ Image saved successfully', [
            //     'order_id' => $orderId,
            //     'file_path' => $relativePath,
            //     'file_size' => strlen($decodedImage),
            // ]);

            return $relativePath;
        } catch (\Exception $exception) {
            Log::error('Error saving order item image', [
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);
            return null;
        }
    }
}
