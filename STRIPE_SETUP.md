# Configuración para Gang Sheet + Pago

## 🔧 Archivo: `.env`

```env
# Stripe
STRIPE_PUBLIC_KEY=pk_test_xxxxx
STRIPE_SECRET_KEY=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx

# Queue (para generar imágenes en background)
QUEUE_CONNECTION=database  # o redis, sqs, etc
```

---

## 📍 Archivo: `routes/api.php`

```php
<?php

use App\Http\Controllers\GangSheetController;
use App\Http\Controllers\Api\GangSheetPaymentController;
use Illuminate\Support\Facades\Route;

// Gang Sheet CRUD
Route::middleware('api')->group(function () {
    Route::post('/gang-sheets', [GangSheetController::class, 'store']);
    Route::get('/gang-sheets', [GangSheetController::class, 'index']);
    Route::get('/gang-sheets/{id}', [GangSheetController::class, 'show']);
    Route::put('/gang-sheets/{id}', [GangSheetController::class, 'update']);
    Route::delete('/gang-sheets/{id}', [GangSheetController::class, 'destroy']);
    
    // Upload individual image
    Route::post('/gang-sheets/upload', [GangSheetController::class, 'uploadImage']);
    
    // Descargar imagen final (después de pago)
    Route::get('/gang-sheets/{id}/download', [GangSheetController::class, 'downloadFinal']);
    
    // Generar imagen final (para testing, normalmente se hace via webhook)
    Route::post('/gang-sheets/{id}/generate', [GangSheetController::class, 'generateFinal']);
});

// Pago + Stripe
Route::middleware('api')->group(function () {
    // Iniciar pago
    Route::post('/gang-sheets/payment/initiate', [GangSheetPaymentController::class, 'initiatePayment']);
    
    // Webhook de Stripe (sin autenticación)
    Route::post('/webhooks/stripe/gang-sheets', [GangSheetPaymentController::class, 'handleStripeWebhook'])
        ->withoutMiddleware('api');  // No requiere auth
});
```

---

## 📦 Archivo: `config/stripe.php` (crear)

```php
<?php

return [
    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
];
```

---

## 💾 Migraciones Necesarias

### 1. Agregar campos a `gang_sheets` table

**Archivo:** `database/migrations/xxxx_add_payment_fields_to_gang_sheets.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gang_sheets', function (Blueprint $table) {
            // Si no existen
            if (!Schema::hasColumn('gang_sheets', 'status')) {
                $table->string('status')->default('draft'); // draft, processing, completed, failed
            }
            if (!Schema::hasColumn('gang_sheets', 'final_path')) {
                $table->string('final_path')->nullable();
            }
            if (!Schema::hasColumn('gang_sheets', 'error_message')) {
                $table->text('error_message')->nullable();
            }
            
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('gang_sheets', function (Blueprint $table) {
            $table->dropColumn(['status', 'final_path', 'error_message']);
        });
    }
};
```

### 2. Crear tabla `orders`

**Archivo:** `database/migrations/xxxx_create_orders_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gang_sheet_id')->constrained('gang_sheets')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('email');
            $table->string('status')->default('pending'); // pending, paid, processing, shipped, delivered, failed
            $table->string('payment_method')->default('stripe'); // stripe, paypal, manual
            $table->string('stripe_payment_id')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('stripe_payment_id');
            $table->index(['gang_sheet_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

### 3. Crear tabla `jobs` para queue

```bash
php artisan queue:table
php artisan migrate
```

---

## 📋 Pasos para Implementar

### 1. Actualizar `.env`
```bash
# Agregar Stripe keys
STRIPE_PUBLIC_KEY=pk_test_xxxxx
STRIPE_SECRET_KEY=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx

# Configurar queue
QUEUE_CONNECTION=database
```

### 2. Crear migraciones
```bash
php artisan make:migration add_payment_fields_to_gang_sheets --table=gang_sheets
php artisan make:migration create_orders_table
php artisan queue:table
```

### 3. Ejecutar migraciones
```bash
php artisan migrate
```

### 4. Instalar Stripe SDK
```bash
composer require stripe/stripe-php
```

### 5. Crear Model `Order`
```bash
php artisan make:model Order
```

Contenido:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'gang_sheet_id',
        'customer_id',
        'amount',
        'email',
        'status',
        'payment_method',
        'stripe_payment_id',
        'notes',
    ];

    public function gangSheet()
    {
        return $this->belongsTo(GangSheet::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
```

### 6. Actualizar Model `GangSheet`
```php
public function order()
{
    return $this->hasOne(Order::class);
}
```

### 7. Configurable Stripe Webhook en Dashboard
```
https://yourdomain.com/api/webhooks/stripe/gang-sheets
```

Seleccionar eventos:
- `payment_intent.succeeded`
- `payment_intent.payment_failed`

### 8. Ejecutar Queue Worker
```bash
# Development
php artisan queue:work

# Production (supervisord/systemd)
php artisan queue:work --daemon --tries=3 --timeout=3600
```

---

## 🧪 Testing

### Test pago local
```bash
# 1. Crear gang sheet
curl -X POST http://localhost:8000/api/gang-sheets \
  -F "width=22" \
  -F "height=10" \
  -F "unit=feet" \
  -F "images=[]"

# Respuesta: {"success": true, "data": {"id": 1}}

# 2. Iniciar pago
curl -X POST http://localhost:8000/api/gang-sheets/payment/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "gang_sheet_id": 1,
    "amount": 99.99,
    "email": "customer@example.com"
  }'

# Respuesta: {"success": true, "client_secret": "pi_xxx..."}

# 3. Simular webhook de Stripe (usar Stripe CLI para testing real)
curl -X POST http://localhost:8000/api/webhooks/stripe/gang-sheets \
  -H "Content-Type: application/json" \
  -H "Stripe-Signature: valid_signature" \
  -d '{
    "type": "payment_intent.succeeded",
    "data": {
      "object": {
        "id": "pi_xxx",
        "metadata": {
          "gang_sheet_id": 1,
          "order_id": 1
        }
      }
    }
  }'

# 4. Ver estado del job
php artisan queue:work

# 5. Descargar imagen final
curl -X GET http://localhost:8000/api/gang-sheets/1/download
```

---

## 🚀 Stripe CLI para Webhook Testing

```bash
# Instalar Stripe CLI
brew install stripe/stripe-cli/stripe  # macOS
# o https://github.com/stripe/stripe-cli/releases

# Login con tu account Stripe
stripe login

# Forward webhooks a tu local
stripe listen --forward-to localhost:8000/api/webhooks/stripe/gang-sheets

# Trigger test webhook
stripe trigger payment_intent.succeeded
```

---

## 📝 Notas

- El pago se maneja en **GangSheetPaymentController**
- La generación de imagen se hace en **background job** (Queue)
- Las imágenes se guardan en `storage/app/public/exports/`
- Los webhooks de Stripe son **asincronos** (no requieren respuesta rápida)
- El usuario puede descargar la imagen via `GET /api/gang-sheets/{id}/download`
