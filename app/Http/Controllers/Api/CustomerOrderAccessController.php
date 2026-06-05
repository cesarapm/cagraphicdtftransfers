<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOrdersAccessCode;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerOrderAccessController extends Controller
{
    public function requestCode(Request $request, RateLimiter $rateLimiter): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim((string) $validated['email']));
        $rateLimitKey = 'customer-order-access-request:' . sha1($email . '|' . $request->ip());

        if ($rateLimiter->tooManyAttempts($rateLimitKey, 5)) {
            return response()->json([
                'message' => 'Espera un momento antes de solicitar otro codigo.',
            ], 429);
        }

        $rateLimiter->hit($rateLimitKey, 600);

        $latestOrder = Order::query()
            ->whereRaw('LOWER(customer_email) = ?', [$email])
            ->latest('created_at')
            ->first();

        if (!$latestOrder) {
            return response()->json([
                'message' => 'No encontramos pedidos asociados a ese correo.',
            ], 404);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($this->cacheKey($email), [
            'code' => hash('sha256', $code),
            'email' => $email,
            'requested_at' => now()->toIso8601String(),
        ], now()->addMinutes(15));

        try {
            Mail::to($email)->send(new CustomerOrdersAccessCode($latestOrder, $code));
        } catch (\Throwable $exception) {
            Cache::forget($this->cacheKey($email));

            Log::error('No se pudo enviar el codigo de acceso a pedidos', [
                'email' => $email,
                'order_id' => $latestOrder->id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'No pudimos enviar el codigo por correo en este momento.',
            ], 502);
        }

        return response()->json([
            'message' => 'Te enviamos un codigo de acceso al correo de tu compra.',
        ]);
    }

    public function verifyCode(Request $request, RateLimiter $rateLimiter): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|size:6',
        ]);

        $email = strtolower(trim((string) $validated['email']));
        $rateLimitKey = 'customer-order-access-verify:' . sha1($email . '|' . $request->ip());

        if ($rateLimiter->tooManyAttempts($rateLimitKey, 8)) {
            return response()->json([
                'message' => 'Demasiados intentos. Solicita un codigo nuevo.',
            ], 429);
        }

        $rateLimiter->hit($rateLimitKey, 600);
        $cachedAccess = Cache::get($this->cacheKey($email));

        if (!$cachedAccess || !hash_equals($cachedAccess['code'] ?? '', hash('sha256', (string) $validated['code']))) {
            return response()->json([
                'message' => 'El codigo no es valido o ya vencio.',
            ], 422);
        }

        Cache::forget($this->cacheKey($email));

        $orders = Order::with('items')
            ->whereRaw('LOWER(customer_email) = ?', [$email])
            ->latest('created_at')
            ->limit(10)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No encontramos pedidos para ese correo.',
            ], 404);
        }

        $customer = Customer::whereRaw('LOWER(email) = ?', [$email])->first();
        $latestOrder = $orders->first();

        return response()->json([
            'customer' => [
                'first_name' => $customer?->first_name ?? $latestOrder->customer_first_name,
                'last_name' => $customer?->last_name ?? $latestOrder->customer_last_name,
                'email' => $email,
                'phone' => $customer?->phone ?? $latestOrder->customer_phone,
                'address' => $customer?->address ?? $latestOrder->shipping_address,
                'city' => $customer?->city ?? $latestOrder->shipping_city,
                'state' => $customer?->state ?? $latestOrder->shipping_state,
                'zip_code' => $customer?->zip_code ?? $latestOrder->shipping_zip_code,
            ],
            'orders' => $orders->map(fn (Order $order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'status_tone' => $order->status_tone,
                'metodo_pago' => $order->metodo_pago,
                'total' => (float) $order->total,
                'created_at' => optional($order->created_at)?->toIso8601String(),
                'tracking_url' => $order->tracking_url,
                'items' => $order->items->map(fn ($item) => [
                    'product_name' => $item->product_name,
                    'quantity' => (int) $item->quantity,
                    'total' => (float) $item->total,
                ])->values()->all(),
            ])->values()->all(),
        ]);
    }

    private function cacheKey(string $email): string
    {
        return 'customer-order-access-code:' . sha1($email);
    }
}