# Gang Sheet DTF - Flujo Completo de Pago + Generación

## 📋 Resumen del Flujo

```
1. Usuario crea DESIGN en Vue (posiciones, escalas)
   ↓
2. Click "Save to Server" → Guarda en BD (estado: draft)
   ↓
3. Sistema retorna GANG_SHEET_ID
   ↓
4. Redirigir a CHECKOUT con gang_sheet_id
   ↓
5. Usuario paga (Stripe/PayPal/etc)
   ↓
6. Después de pago exitoso → Webhook/Callback
   ↓
7. Backend genera imagen PNG de alta resolución (300 DPI)
   ↓
8. Backend crea ORDEN con gang_sheet_id
   ↓
9. Usuario descarga imagen final
```

---

## 1️⃣ USUARIO CREA DESIGN (Frontend)

**Archivo:** `resources/js/components/GangSheetEditorFeet.vue`

El usuario:
- Sube imágenes
- Las posiciona en el canvas
- Las escala/rota
- Click "Download PNG" → Descarga preview local (opcional)
- Click "Save to Server" → Guarda en backend

---

## 2️⃣ GUARDAR DESIGN EN BACKEND

**Endpoint:** `POST /api/gang-sheets`

**Request (FormData):**
```javascript
{
  "width": 22,
  "height": 10,
  "unit": "feet",
  "name": "Gang Sheet 22x10ft",
  "images": [
    {
      "x": 0.69,      // en pies (coordenada original)
      "y": 0.69,      // en pies
      "width": 2,     // en pies
      "height": 1.5,  // en pies
      "name": "logo.png"
    },
    // ... más imágenes
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Gang sheet saved successfully",
  "data": {
    "id": 123,                    // ← IMPORTANTE: GUARDAR ESTE ID
    "customer_id": null,          // anonymous por ahora
    "width": 22,
    "height": 10,
    "unit": "feet",
    "status": "draft",            // Aún no pagado
    "images_data": [...],
    "created_at": "2026-06-10..."
  }
}
```

**El frontend hace:**
```javascript
// Después de respuesta exitosa:
const gangSheetId = result.data.id;

// Redirigir a checkout
window.location.href = `/checkout?gang_sheet_id=${gangSheetId}`;
```

---

## 3️⃣ PÁGINA DE PAGO (Checkout)

**Archivo:** Crear: `resources/views/gang-sheets/checkout.blade.php`

```blade
<div>
  <h1>Confirm Your Gang Sheet</h1>
  
  <div class="gang-sheet-preview">
    <!-- Mostrar preview del design -->
    <img src="{{ $gangSheet->preview_path }}" />
  </div>
  
  <p>Size: {{ $gangSheet->width }}' × {{ $gangSheet->height }}'</p>
  <p>Images: {{ $gangSheet->image_count }}</p>
  <p>Price: ${{ calculatePrice($gangSheet) }}</p>
  
  <!-- Botón de pago (Stripe) -->
  <form action="/api/gang-sheets/checkout" method="POST">
    @csrf
    <input type="hidden" name="gang_sheet_id" value="{{ $gangSheet->id }}">
    <button type="submit">Pay Now</button>
  </form>
</div>
```

---

## 4️⃣ PROCESAR PAGO (Backend)

**Archivo:** `app/Http/Controllers/GangSheetController.php`

**Método nuevo necesario:**
```php
public function checkout(Request $request)
{
    $validated = $request->validate([
        'gang_sheet_id' => 'required|exists:gang_sheets,id',
        'payment_method' => 'required|in:stripe,paypal',
        'amount' => 'required|numeric',
    ]);
    
    $gangSheet = GangSheet::findOrFail($validated['gang_sheet_id']);
    
    // Verificar que pertenece al usuario (si está autenticado)
    if (auth()->check() && $gangSheet->customer_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    try {
        // Crear ORDER con status "pending"
        $order = Order::create([
            'gang_sheet_id' => $gangSheet->id,
            'customer_id' => auth()->id() ?? null,
            'amount' => $validated['amount'],
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
        ]);
        
        // Procesar pago con Stripe
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => (int)($validated['amount'] * 100), // centavos
            'currency' => 'usd',
            'metadata' => [
                'order_id' => $order->id,
                'gang_sheet_id' => $gangSheet->id,
            ],
        ]);
        
        $order->update(['stripe_payment_id' => $paymentIntent->id]);
        
        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
```

---

## 5️⃣ WEBHOOK DESPUÉS DEL PAGO

**Endpoint:** `POST /api/webhooks/stripe`

```php
public function handleStripeWebhook(Request $request)
{
    $event = \Stripe\Event::constructFrom($request->all());
    
    if ($event->type === 'payment_intent.succeeded') {
        $paymentIntent = $event->data->object;
        $gangSheetId = $paymentIntent->metadata['gang_sheet_id'] ?? null;
        
        // Actualizar order a "paid"
        $order = Order::where('stripe_payment_id', $paymentIntent->id)->first();
        if ($order) {
            $order->update(['status' => 'paid']);
            
            // AQUÍ: Generar imagen de alta resolución
            $this->generateAndSaveFinalImage($order->gangSheet);
            
            // Actualizar gang sheet a "processing" → "completed"
            $order->gangSheet->update(['status' => 'completed']);
            
            // Enviar email al usuario con enlace de descarga
            // ...
        }
    }
    
    return response()->json(['success' => true]);
}

private function generateAndSaveFinalImage(GangSheet $gangSheet)
{
    // Esto usa los métodos que creamos en GangSheetController
    // generateHighResImage, generateWithImagick, generateWithGD
    
    try {
        $finalPath = $this->generateHighResImage($gangSheet);
        $gangSheet->update(['final_path' => $finalPath]);
        
        \Log::info("Generated final image for gang sheet {$gangSheet->id}: {$finalPath}");
    } catch (\Exception $e) {
        \Log::error("Failed to generate image for gang sheet {$gangSheet->id}: {$e->getMessage()}");
        throw $e;
    }
}
```

---

## 6️⃣ DESCARGAR IMAGEN FINAL

**Endpoint:** `GET /api/gang-sheets/{id}/download`

```php
public function download($id)
{
    $gangSheet = GangSheet::findOrFail($id);
    
    // Verificar que está pagado
    if ($gangSheet->status !== 'completed') {
        return response()->json(['error' => 'Image not ready yet'], 400);
    }
    
    $filePath = Storage::disk('public')->path($gangSheet->final_path);
    
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'File not found'], 404);
    }
    
    return response()->download(
        $filePath,
        "gang-sheet-{$gangSheet->id}.png",
        ['Content-Type' => 'image/png']
    );
}
```

---

## 📊 ESTRUCTURA DE BASE DE DATOS

### GangSheets Table
```sql
CREATE TABLE gang_sheets (
    id BIGINT PRIMARY KEY,
    customer_id BIGINT NULLABLE,
    order_id BIGINT NULLABLE,
    name VARCHAR(255),
    width DECIMAL(8,2),
    height DECIMAL(8,2),
    unit VARCHAR(20), -- 'feet' or 'inches'
    dpi INT DEFAULT 300,
    images_data JSON,  -- Guardar array de imágenes con posiciones
    preview_path VARCHAR(255) NULLABLE,  -- Preview antes de pagar
    final_path VARCHAR(255) NULLABLE,     -- Imagen final después de pagar
    status VARCHAR(50) DEFAULT 'draft',   -- draft, processing, completed, failed
    total_area DECIMAL(10,2),
    image_count INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

### Orders Table (relacionada)
```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY,
    gang_sheet_id BIGINT,
    customer_id BIGINT NULLABLE,
    amount DECIMAL(10,2),
    status VARCHAR(50), -- pending, paid, processing, shipped, delivered
    payment_method VARCHAR(50), -- stripe, paypal
    stripe_payment_id VARCHAR(255) NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (gang_sheet_id) REFERENCES gang_sheets(id)
);
```

---

## 🔄 FLUJO RESUMIDO EN CÓDIGO

### Frontend (Vue)
```javascript
// 1. Usuario clickea "Save to Server"
async saveGangSheet() {
    const result = await fetch('/api/gang-sheets', {
        method: 'POST',
        body: formData // con imágenes y posiciones
    });
    const gangSheetId = result.data.id;
    
    // 2. Redirigir a pago
    window.location.href = `/checkout?gang_sheet_id=${gangSheetId}`;
}

// 3. Usuario completa pago
// → Stripe webhook ejecuta `handleStripeWebhook`
// → Backend genera imagen final
// → Usuario recibe email con enlace de descarga
```

### Backend (Laravel)
```
POST /api/gang-sheets                → Guardar design (estado: draft)
POST /api/gang-sheets/checkout       → Procesar pago
POST /api/webhooks/stripe           → Webhook de Stripe
  → generateHighResImage()           → Generar PNG 300 DPI
  → Actualizar gang_sheet (completed)
GET  /api/gang-sheets/{id}/download → Descargar imagen final
```

---

## 🎯 ESPECIFICACIONES TÉCNICAS

### Resolución de Exportación
- **Feet:** 22' × 10' = 79,200 × 36,000 px @ 300 DPI
- **Inches:** Escalar según dimensiones

### Métodos de Generación
1. **Imagick** (preferido) - Mejor calidad, compresión automática
2. **GD** (fallback) - Si Imagick no está disponible

### Almacenamiento
```
storage/app/public/
├── exports/
│   └── gang-sheet-123-1717940400.png  (archivo final)
├── previews/
│   └── preview_123abc.png              (preview antes de pagar)
└── uploads/
    └── user-images/
        └── logo.png, design.jpg, etc   (imágenes del usuario)
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [ ] Crear ruta POST `/api/gang-sheets` → Guardar design
- [ ] Crear ruta GET `/checkout` → Página de pago
- [ ] Crear ruta POST `/api/gang-sheets/checkout` → Procesar pago Stripe
- [ ] Configurar Stripe webhook en backend
- [ ] Crear ruta POST `/api/webhooks/stripe` → Manejar webhook
- [ ] Crear ruta GET `/api/gang-sheets/{id}/download` → Descargar imagen
- [ ] Implementar métodos `generateWithImagick` y `generateWithGD`
- [ ] Crear tabla `orders` si no existe
- [ ] Agregar campos a tabla `gang_sheets`: `final_path`, `status`
- [ ] Enviar email con enlace de descarga después de pago
- [ ] Pruebas end-to-end

---

## 🧪 TESTING

### Local (sin pago real)
```bash
# Usar Stripe test keys
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...

# Usar tarjeta de prueba: 4242 4242 4242 4242
```

### Test Webhook
```bash
curl -X POST http://localhost:8000/api/webhooks/stripe \
  -H "Content-Type: application/json" \
  -d '{
    "type": "payment_intent.succeeded",
    "data": {
      "object": {
        "id": "pi_123",
        "metadata": {"gang_sheet_id": 1}
      }
    }
  }'
```

---

## 📧 EMAIL DE CONFIRMACIÓN

Después de pago exitoso, enviar email:
```
Subject: Your Gang Sheet is Ready!

Hi [Customer],

Your gang sheet is ready for download:
- Size: 22' × 10'
- Resolution: 79,200 × 36,000 px (300 DPI)
- Format: PNG

Download: [link a /api/gang-sheets/123/download]

Ready for DTF printing!
```
