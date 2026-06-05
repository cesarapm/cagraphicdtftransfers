<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'email' => 'nullable|email|required_without:token',
            'token' => 'nullable|string|required_without:email',
        ]);

        $order = Order::with(['items', 'latestPay'])
            ->where('order_number', $validated['order_number'])
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'No encontramos una orden con esos datos.',
            ], 404);
        }

        $providedToken = $validated['token'] ?? null;
        $providedEmail = isset($validated['email']) ? strtolower($validated['email']) : null;

        $isValidToken = is_string($providedToken) && hash_equals($order->tracking_token, $providedToken);
        $isValidEmail = is_string($providedEmail) && strtolower((string) $order->customer_email) === $providedEmail;

        if (!$isValidToken && !$isValidEmail) {
            return response()->json([
                'message' => 'Los datos de seguimiento no son validos.',
            ], 403);
        }

        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_full_name' => $order->customer_full_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'shipping_address' => $order->shipping_address,
                'shipping_city' => $order->shipping_city,
                'shipping_state' => $order->shipping_state,
                'shipping_zip_code' => $order->shipping_zip_code,
                'subtotal' => (float) $order->subtotal,
                'shipping_cost' => (float) $order->shipping_cost,
                'tax' => (float) $order->tax,
                'total' => (float) $order->total,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'status_tone' => $order->status_tone,
                'metodo_pago' => $order->metodo_pago,
                'notes' => $order->notes,
                'created_at' => optional($order->created_at)?->toIso8601String(),
                'tracking_url' => $order->tracking_url,
                'items' => $order->items->map(fn ($item) => [
                    'product_name' => $item->product_name,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total' => (float) $item->total,
                ])->values()->all(),
                'payment' => $order->latestPay ? [
                    'id_pago' => $order->latestPay->id_pago,
                    'estado' => $order->latestPay->estado,
                    'metodo_pago' => $order->latestPay->metodo_pago,
                    'monto_transaccion' => (float) $order->latestPay->monto_transaccion,
                    'fecha_aprobacion' => $order->latestPay->fecha_aprobacion,
                    'codigo_autorizacion' => $order->latestPay->codigo_autorizacion,
                ] : null,
            ],
        ]);
    }
}