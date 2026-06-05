<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class PaymentMethodsController extends Controller
{
    /**
     * Obtener los métodos de pago disponibles
     */
    public function index(): JsonResponse
    {
        $paymentMethods = [];

        // Verificar si MercadoPago está habilitado
        $mercadopagoEnabled = Setting::get('mercadopago_enabled', '0');
        if ($mercadopagoEnabled === '1' || $mercadopagoEnabled === 1 || $mercadopagoEnabled === true) {
            $paymentMethods[] = [
                'id' => 'mercado_pago',
                'name' => 'Mercado Pago',
                'description' => 'Paga con tarjeta, saldo o métodos disponibles en Mercado Pago y regresa al checkout con el estado de tu compra.',
                'icon' => 'mdi-credit-card-outline',
                'enabled' => true,
            ];
        }

        // Verificar si PayPal está habilitado
        $paypalEnabled = Setting::get('paypal_enabled', '0');
        if ($paypalEnabled === '1' || $paypalEnabled === 1 || $paypalEnabled === true) {
            $paymentMethods[] = [
                'id' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Paga de forma segura con tu cuenta PayPal o tarjeta de crédito.',
                'icon' => 'mdi-paypal',
                'enabled' => true,
            ];
        }

        // Verificar si Transferencia está habilitada
        $transferenciaEnabled = Setting::get('transferencia_enabled', '0');
        if ($transferenciaEnabled === '1' || $transferenciaEnabled === 1 || $transferenciaEnabled === true) {
            $paymentMethods[] = [
                'id' => 'transferencia',
                'name' => 'Transferencia Bancaria',
                'description' => 'Genera tu orden y continúa con la confirmación manual del depósito o transferencia.',
                'icon' => 'mdi-bank-transfer-out',
                'enabled' => true,
                'bank_info' => [
                    'banco' => Setting::get('banco_nombre', ''),
                    'beneficiario' => Setting::get('banco_beneficiario', ''),
                    'cuenta' => Setting::get('banco_cuenta', ''),
                    'clabe' => Setting::get('banco_clabe', ''),
                    'swift' => Setting::get('banco_swift', ''),
                    'whatsapp' => Setting::get('whatsapp_transferencia', ''),
                    'instrucciones' => Setting::get('transferencia_instrucciones', ''),
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'payment_methods' => $paymentMethods,
            'default_method' => !empty($paymentMethods) ? $paymentMethods[0]['id'] : null,
        ]);
    }

    /**
     * Obtener información de transferencia bancaria
     */
    public function getBankInfo(): JsonResponse
    {
        $enabled = Setting::get('transferencia_enabled', false);

        if (!$enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Transferencia bancaria no disponible',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'bank_info' => [
                'banco' => Setting::get('banco_nombre', ''),
                'beneficiario' => Setting::get('banco_beneficiario', ''),
                'cuenta' => Setting::get('banco_cuenta', ''),
                'clabe' => Setting::get('banco_clabe', ''),
                'swift' => Setting::get('banco_swift', ''),
                'whatsapp' => Setting::get('whatsapp_transferencia', ''),
                'instrucciones' => Setting::get('transferencia_instrucciones', ''),
            ],
        ]);
    }
}
