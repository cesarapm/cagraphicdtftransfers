<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerOrderAccessController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderTrackingController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\PaymentMethodsController;
use App\Http\Controllers\Api\SheetSizeController;
use App\Http\Controllers\Api\DtfSizeController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\GangSheetController;
use App\Http\Controllers\Api\DtfGangController;
use App\Http\Controllers\Api\DiscountCodeController;
use Illuminate\Support\Facades\Route;

// ⚠️ Public routes - No authentication required
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);

// Sheet Sizes - ORDER MATTERS! Specific routes first
Route::get('/sheet-sizes/{id}', [SheetSizeController::class, 'show'])->where('id', '[0-9]+');
Route::get('/sheet-sizes/by-unit/{unit}', [SheetSizeController::class, 'getByUnit']);
Route::get('/sheet-sizes', [SheetSizeController::class, 'index']);

// DTF Sizes
Route::get('/dtf-sizes', [DtfSizeController::class, 'index']);

Route::get('/dtf-gangs', [DtfGangController::class, 'index']);

// Promotions - Public (view active promotions)
Route::get('/promotions', [PromotionController::class, 'index']);
Route::get('/promotions/{type}/{id}', [PromotionController::class, 'getForItem']);

// DTF Cart - Public endpoint for checkout
Route::post('/checkout', [CartController::class, 'checkout']);

// Gang Sheets - Public endpoints
Route::post('/gang-sheets/save', [GangSheetController::class, 'save']);
Route::get('/gang-sheets/{id}', [GangSheetController::class, 'show']);
Route::get('/gang-sheets/{id}/download', [GangSheetController::class, 'download']); // Descargar PNG

// Métodos de pago
Route::get('/payment-methods', [PaymentMethodsController::class, 'index']);
Route::get('/payment-methods/bank-info', [PaymentMethodsController::class, 'getBankInfo']);

// Códigos de descuento - Public validation
Route::post('/discount-codes/validate', [DiscountCodeController::class, 'validate']);

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

    // Promotions - Protected (admin endpoints)
    Route::prefix('promotions')->group(function () {
        Route::post('/{type}/{id}', [PromotionController::class, 'store']);
        Route::delete('/{type}/{id}', [PromotionController::class, 'destroy']);
    });

    // Descuentos - Protected (registrar uso)
    Route::post('/discount-codes/{discountCode}/use', [DiscountCodeController::class, 'markAsUsed']);
});

// Ruta pública para cancelar orden cuando el pago falla
Route::post('/cancelar-orden-pago-fallido', [PedidoController::class, 'cancelarOrdenPorPagoFallido']);
