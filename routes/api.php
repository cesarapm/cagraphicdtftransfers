<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerOrderAccessController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderTrackingController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\PaymentMethodsController;
use App\Http\Controllers\GangSheetController;
use Illuminate\Support\Facades\Route;

// ⚠️ Public routes - No authentication required
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);

// Métodos de pago
Route::get('/payment-methods', [PaymentMethodsController::class, 'index']);
Route::get('/payment-methods/bank-info', [PaymentMethodsController::class, 'getBankInfo']);

// ⚠️ Webhook de Mercado Pago - DEBE estar fuera del middleware de autenticación
Route::post('/mercado-pago/webhook', [WebhookController::class, 'handleWebhook']);

// ⚠️ Webhook de PayPal - DEBE estar fuera del middleware de autenticación
Route::post('/paypal/webhook', [WebhookController::class, 'handlePayPalWebhook']);

// 🔒 Protected routes - Require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Auth customer endpoint
    Route::get('/customer', [AuthController::class, 'customer']);
    
    // Gang Sheets API - Protected (customers must be logged in)
    Route::prefix('gang-sheets')->group(function () {
        Route::get('/', [GangSheetController::class, 'index']);
        Route::get('/{id}', [GangSheetController::class, 'show']);
        Route::post('/save', [GangSheetController::class, 'store']);
        Route::put('/{id}', [GangSheetController::class, 'update']);
        Route::delete('/{id}', [GangSheetController::class, 'destroy']);
        Route::post('/{id}/generate', [GangSheetController::class, 'generateFinal']);
        Route::post('/upload-image', [GangSheetController::class, 'uploadImage']);
    });

    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/crear-pedido', [PedidoController::class, 'crearPedido']);
    Route::post('/crear-pedido/transferencia', [PedidoController::class, 'crearPedidoTransferencia']);
    Route::post('/crear-pedido/paypal', [PedidoController::class, 'crearPedidoPayPal']);
    Route::post('/paypal/capturar-pago', [PedidoController::class, 'capturarPagoPayPal']);
    Route::get('/pedidos/seguimiento', [OrderTrackingController::class, 'show']);
    Route::get('/ordenes-recientes', [PedidoController::class, 'ordenesRecientes']);
    Route::post('/clientes/pedidos/acceso', [CustomerOrderAccessController::class, 'requestCode']);
    Route::post('/clientes/pedidos/verificar', [CustomerOrderAccessController::class, 'verifyCode']);
    Route::delete('/ordenes/{id}', [PedidoController::class, 'ordenescancelar']);
});

// Ruta pública para cancelar orden cuando el pago falla
Route::post('/cancelar-orden-pago-fallido', [PedidoController::class, 'cancelarOrdenPorPagoFallido']);
