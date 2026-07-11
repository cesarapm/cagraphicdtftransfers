<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiscountCodeController extends Controller
{
    /**
     * Validar un código de descuento
     * POST /api/discount-codes/validate
     * Body: { code: "SUMMER2024", subtotal: 100.00 }
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $code = DiscountCode::where('code', strtoupper($request->code))->first();

        if (!$code) {
            return response()->json([
                'valid' => false,
                'message' => 'Código de descuento no encontrado.',
            ], 404);
        }

        // Obtener el cliente autenticado (puede ser null si no está registrado)
        $user = auth('sanctum')->user();

        // Validación consistente: si hay cliente autenticado, validar restricciones por usuario
        if ($user) {
            // Buscar el Customer correspondiente al User autenticado
            $customer = Customer::where('email', strtolower((string) $user->email))->first();
            
            if ($customer) {
                $validation = $code->isValidForCustomer($customer->id);
            } else {
                // Si no hay Customer, validar solo restricciones globales
                $validation = $code->isValidGlobally();
            }
        } else {
            // Si no está registrado, validar solo restricciones globales
            $validation = $code->isValidGlobally();
        }

        if (!$validation['is_valid']) {
            return response()->json([
                'valid' => false,
                'errors' => $validation['errors'],
            ], 422);
        }

        // Calcular el descuento
        $discount = $code->calculateDiscount($request->subtotal);
        $finalPrice = $code->getFinalPrice($request->subtotal);

        return response()->json([
            'valid' => true,
            'code_id' => $code->id,
            'code' => $code->code,
            'description' => $code->description,
            'discount_type' => $code->discount_type,
            'discount_value' => $code->discount_value,
            'discount_amount' => round($discount, 2),
            'final_price' => round($finalPrice, 2),
            'message' => "¡Código aplicado! Descuento: \${$discount}",
        ]);
    }

    /**
     * Registrar el uso de un código de descuento (cuando se completa la orden)
     * POST /api/discount-codes/{codeId}/use
     * Para clientes registrados (User/Sanctum)
     */
    public function markAsUsed(Request $request, DiscountCode $discountCode): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes estar registrado para usar códigos de descuento.',
            ], 401);
        }

        // Obtener el Customer correspondiente al User autenticado por email
        $customer = Customer::where('email', strtolower((string) $user->email))->first();
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el perfil del cliente.',
            ], 422);
        }

        // Verificar que no haya ya usado este código
        $validation = $discountCode->isValidForCustomer($customer->id);
        if (!$validation['is_valid']) {
            return response()->json([
                'success' => false,
                'errors' => $validation['errors'],
            ], 422);
        }

        // Registrar el uso del código
        $discountCode->markAsUsedByCustomer($customer->id);

        return response()->json([
            'success' => true,
            'message' => 'Código de descuento registrado.',
        ]);
    }
}
