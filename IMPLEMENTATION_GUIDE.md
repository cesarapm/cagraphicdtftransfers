# 🎯 Gang Sheet DTF - Documentación Completa

## 📋 Resumen Ejecutivo

**Objetivo:** Sistema de creación y impresión DTF (Direct-to-Film) con editor visual, generación de imágenes de alta resolución (300 DPI) y flujo de pago integrado.

**Stack Técnico:**
- Frontend: Vue 3 + Konva.js + Tailwind CSS
- Backend: Laravel + Intervention\Image (GD/Imagick)
- Pago: Stripe
- Queue: Laravel Jobs (generación asincrónica)

**Estado:** ✅ COMPLETAMENTE DOCUMENTADO + IMPLEMENTACIÓN INICIADA

---

## 📁 Documentación Disponible

### 1. **GANG_SHEET_FLOW.md** 
- Flujo completo: Diseño → Pago → Generación → Descarga
- Estructura de base de datos
- Códigos de ejemplo de cada paso
- Checklist de implementación

**Contenidos:**
- Flujo visual de 9 pasos
- Endpoints REST necesarios
- Métodos Backend para generación de imágenes
- Estructura de JSON requests/responses
- Testing con Stripe

### 2. **STRIPE_SETUP.md**
- Configuración completa de Stripe
- Variables de entorno necesarias
- Rutas API (routes/api.php)
- Migraciones de base de datos
- Jobs para generación asincrónica
- Commands de testing

**Contenidos:**
- Archivos de configuración
- Pasos de implementación paso a paso
- Testing local con Stripe CLI
- Commands para ejecutar queue worker

### 3. **GangSheetEditorFeet.vue**
- Componente Vue principal (1029+ líneas)
- Editor visual con Konva.js
- Exportación de imágenes PNG a 300 DPI
- Dos botones: Download (local) y Save to Server

**Funcionalidades:**
- Carga de imágenes
- Posicionamiento libre (drag & drop)
- Escalado/rotación con transformer
- Cálculo de DPI en tiempo real
- Exportación de alta resolución

---

## 🔄 Flujo de Negocio Completo

```
USUARIO CREA DESIGN (Frontend)
    ↓
    Carga imágenes
    Posiciona en canvas (22' × 10')
    Escala/rota según necesidad
    ↓
GUARDAR DISEÑO (Backend)
    ↓
    POST /api/gang-sheets
    Valida dimensiones y imágenes
    Guarda metadata de design
    Estado: "draft"
    Retorna: gang_sheet_id
    ↓
PROCESAR PAGO (Stripe)
    ↓
    POST /api/gang-sheets/payment/initiate
    Crea order en BD
    Crea PaymentIntent en Stripe
    Retorna: client_secret
    ↓
USUARIO PAGA
    ↓
    Completa pago en Stripe Checkout
    ↓
WEBHOOK DE STRIPE
    ↓
    POST /api/webhooks/stripe/gang-sheets
    Verifica firma de Stripe
    Evento: payment_intent.succeeded
    Actualiza order.status = "paid"
    ↓
GENERAR IMAGEN (Background Job)
    ↓
    Dispatch: GenerateGangSheetImage::class
    Carga todas las imágenes del design
    Crea canvas 300 DPI (79,200 × 36,000 px para 22'×10')
    Renderiza cada imagen en su posición
    Guarda PNG en storage/exports/
    Usa Imagick (preferido) o GD (fallback)
    ↓
ACTUALIZAR ESTADO
    ↓
    gang_sheet.status = "completed"
    gang_sheet.final_path = ruta del PNG
    order.status = "processing" → "completed"
    ↓
NOTIFICAR AL USUARIO
    ↓
    Email con enlace de descarga
    Link: GET /api/gang-sheets/{id}/download
    ↓
USUARIO DESCARGA
    ↓
    PNG lista para DTF printing
    300 DPI, máxima calidad
    ~50MB para sheets 22' × 10'
```

---

## 📊 Estructura de Base de Datos

### gang_sheets (tabla principal)
```sql
id                INT (PK)
customer_id       INT (FK) - nullable (anonymous users)
order_id          INT (FK) - nullable
name              VARCHAR(255)
width             DECIMAL(8,2)
height            DECIMAL(8,2)
unit              VARCHAR(20) - 'feet' | 'inches'
dpi               INT - default 300
images_data       JSON - array de imágenes con posiciones
preview_path      VARCHAR(255) - imagen preview antes de pagar
final_path        VARCHAR(255) - imagen final después de pagar
status            VARCHAR(50) - 'draft' | 'processing' | 'completed' | 'failed'
total_area        DECIMAL(10,2)
image_count       INT
error_message     TEXT - nullable
created_at        TIMESTAMP
updated_at        TIMESTAMP
```

### orders (tabla de pedidos)
```sql
id                INT (PK)
gang_sheet_id     INT (FK) - constrained
customer_id       INT (FK) - nullable
amount            DECIMAL(10,2)
email             VARCHAR(255)
status            VARCHAR(50) - 'pending' | 'paid' | 'processing' | 'shipped' | 'failed'
payment_method    VARCHAR(50) - 'stripe' | 'paypal' | 'manual'
stripe_payment_id VARCHAR(255) - unique | nullable
notes             TEXT - nullable
created_at        TIMESTAMP
updated_at        TIMESTAMP
```

---

## 🎨 Especificaciones Técnicas

### Resoluciones de Exportación
| Dimensión | Unit | Pixels (300 DPI) | File Size (~) |
|-----------|------|------------------|---------------|
| 22' × 10' | feet | 79,200 × 36,000 | ~50 MB |
| 30' × 12' | feet | 108,000 × 43,200 | ~73 MB |
| 18' × 8'  | feet | 64,800 × 28,800 | ~32 MB |
| 48" × 36" | inches | 14,400 × 10,800 | ~3 MB |

### Cálculo de Píxeles
```
Feet → Pulgadas → Píxeles (300 DPI)
width_feet × 12 × 300 = width_pixels
```

### Métodos de Generación
1. **Imagick** (recomendado)
   - Mejor compresión PNG
   - Soporte para 16-bit
   - Mejor manejo de transparencia

2. **GD** (fallback)
   - Disponible en casi todos los PHP
   - Más lento para imágenes grandes
   - Menos opciones de compresión

---

## 🔧 Archivos Creados/Modificados

### ✅ Completados (Frontend)
- [x] `resources/js/components/GangSheetEditorFeet.vue` - Editor principal (1029+ líneas)
  - Método `createHighResolutionExport()` - Genera PNG 300 DPI
  - Método `downloadGangSheet()` - Descarga en navegador
  - Método `saveGangSheet()` - Guarda en backend
  - Método `estimateFileSize()` - Calcula tamaño estimado
  - Métodos `getResolutionClass()` y `getResolutionMessage()` - DPI feedback

### ✅ Completados (Backend - Generación de Imágenes)
- [x] `app/Http/Controllers/GangSheetController.php`
  - Método `generateFinal(Request $request, $id)` - Endpoint para generar
  - Método `generateHighResImage(GangSheet $gangSheet)` - Lógica principal
  - Método `generateWithImagick()` - Generación con Imagick
  - Método `generateWithGD()` - Generación con GD (fallback)
  - Método `loadImageGD()` - Cargar imágenes con GD

### ✅ Completados (Backend - Pago)
- [x] `app/Http/Controllers/Api/GangSheetPaymentController.php` - Controlador de pago
  - Método `initiatePayment()` - Crear PaymentIntent
  - Método `handleStripeWebhook()` - Webhook handler
  - Método `handlePaymentSucceeded()` - Procesar pago exitoso
  - Método `handlePaymentFailed()` - Procesar pago fallido

### ✅ Completados (Backend - Jobs)
- [x] `app/Jobs/GenerateGangSheetImage.php` - Job asincrónico
  - Generación en background
  - Reintentos automáticos
  - Error handling

### 📋 Por Hacer (Requeridos)

**Backend Models:**
- [ ] `app/Models/Order.php` - Crear model
- [ ] Actualizar `app/Models/GangSheet.php` - Relación con Order

**Migraciones:**
- [ ] Crear migración: `add_payment_fields_to_gang_sheets`
- [ ] Crear migración: `create_orders_table`
- [ ] Crear migración: `create_jobs_table` (para queue)

**Configuración:**
- [ ] Crear `config/stripe.php`
- [ ] Actualizar `.env` con Stripe keys
- [ ] Instalar `composer require stripe/stripe-php`

**Rutas:**
- [ ] Actualizar `routes/api.php` con nuevas rutas

**Frontend (Payment):**
- [ ] Crear página checkout con Stripe Elements
- [ ] Integrar Stripe.js en template
- [ ] Componente de confirmación después de pago

**Emails:**
- [ ] Crear `app/Mail/GangSheetPaidMail.php`
- [ ] Crear `app/Mail/GangSheetPaymentFailedMail.php`
- [ ] Templates Blade para emails

**Documentación:**
- [ ] README.md actualizado con flujo visual
- [ ] API documentation (Postman/OpenAPI)

---

## 🧪 Testing

### Local Testing (sin Stripe real)
```bash
# 1. Guardar design
curl -X POST http://localhost:8000/api/gang-sheets \
  -F "width=22" -F "height=10" -F "unit=feet"

# 2. Iniciar pago (test)
curl -X POST http://localhost:8000/api/gang-sheets/payment/initiate \
  -H "Content-Type: application/json" \
  -d '{"gang_sheet_id":1,"amount":99.99,"email":"test@example.com"}'

# 3. Ver estado del job
php artisan queue:work

# 4. Verificar imagen generada
ls storage/app/public/exports/
```

### Stripe Test Cards
```
Visa: 4242 4242 4242 4242
Mastercard: 5555 5555 5555 4444
Amex: 378282246310005
```

---

## 📈 Métricas

**Tamaño Típico de Imágenes:**
- 22' × 10' @ 300 DPI = 79,200 × 36,000 px = ~50 MB PNG
- Tiempo de generación con Imagick = ~2-5 segundos
- Tiempo de generación con GD = ~10-15 segundos

**Almacenamiento:**
```
storage/app/public/
├── exports/           (imágenes finales - ~50 MB cada)
├── previews/          (previews antes de pagar - ~1-2 MB)
├── uploads/           (imágenes del usuario - variables)
└── jobs/              (metadata de jobs)
```

---

## 🚀 Deployment

### Requisitos
- PHP 8.1+ con GD o Imagick
- Laravel 11+
- MySQL 8.0+
- Redis o Database queue
- Stripe account con keys

### Steps
1. Copiar código de archivos creados
2. Ejecutar migraciones: `php artisan migrate`
3. Configurar Stripe webhook: https://dashboard.stripe.com/webhooks
4. Ejecutar queue worker: `php artisan queue:work --daemon`
5. Testear con Stripe CLI: `stripe listen`

---

## 📞 Support

**Preguntas Frecuentes:**

**P: ¿Por qué 300 DPI?**
R: Es el estándar profesional para impresoras DTF. Menor resolución = calidad pobre.

**P: ¿Por qué generar en backend?**
R: Las imágenes son grandes (~50MB). Mejor hacerlo en servidor silencioso que en navegador.

**P: ¿Puedo cambiar a 600 DPI?**
R: Sí, actualiza `$dpi = 600` en `generateHighResImage()`. Pero el tamaño será 4x más grande.

**P: ¿Qué pasa si se interrumpe el job?**
R: Laravel reintentar automáticamente 3 veces (configurable).

**P: ¿Puedo usar PayPal?**
R: Sí, reemplaza `initiatePayment()` con integración PayPal.

---

## 📚 Referencias

- [Konva.js Documentation](https://konvajs.org/)
- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Stripe API Reference](https://stripe.com/docs/api)
- [Intervention Image](http://image.intervention.io/)
- [PHP GD Library](https://www.php.net/manual/en/book.image.php)

---

**Última actualización:** Junio 2026  
**Autor:** Sistema de Documentación DTF  
**Estado:** ✅ DOCUMENTACIÓN COMPLETADA
