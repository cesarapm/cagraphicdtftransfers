# DTF Gang Sheet - Flujo de Trabajo Completo

## 🎯 Modelo de Negocio

La aplicación DTF Gang Sheet funciona con el siguiente modelo:

1. **Registro Gratuito** → Usuarios se registran sin costo
2. **Acceso al Builder** → Usuarios autenticados pueden crear gang sheets gratis
3. **Aprobación y Cotización** → Usuario envía su diseño y recibe precio
4. **Pago por Proyecto** → Se cobra solo cuando el usuario aprueba y paga
5. **Producción y Envío** → Una vez pagado, entra en producción

**NO hay productos ni suscripciones** - Solo cobro por gang sheets aprobados

---

## 📊 Estructura de Datos

### Tabla: `dtf_pricing`
Precios predefinidos por tamaño de hoja:

| Nombre | Tamaño | Precio Base | Descuento | Umbral Cobertura |
|--------|--------|-------------|-----------|------------------|
| 22" × 120" (10 ft) | 22 × 120 | $165.00 | 10% | 80% |
| 22" × 60" (5 ft) | 22 × 60 | $95.00 | 10% | 80% |
| 13" × 19" | 13 × 19 | $28.00 | 5% | 75% |

**Lógica de Descuentos:**
- Si la cobertura (% del área usada) supera el umbral, se aplica descuento
- Ejemplo: Hoja 22x60 con 85% cobertura = $95 - 10% = $85.50

### Tabla: `gang_sheets` (actualizada)

#### Campos Existentes
- `id`, `user_id`, `customer_id`, `order_id`
- `name`, `width`, `height`, `dpi`
- `images_data` (JSON con posiciones de imágenes)
- `preview_path`, `final_path`
- `total_area`, `image_count`
- `status`, `notes`

#### Campos Nuevos - Pricing
- `price` - Precio calculado final
- `coverage_percentage` - % de cobertura del área

#### Campos Nuevos - Aprobación
- `requires_approval` - Si requiere aprobación (default: true)
- `submitted_at` - Cuándo usuario lo envió
- `approved_at` - Cuándo admin lo aprobó
- `approved_by` - ID del admin que aprobó
- `approval_notes` - Notas del aprobador

#### Campos Nuevos - Pago
- `payment_status` - pending | paid | failed | refunded
- `payment_id` - ID de transacción del gateway
- `payment_method` - Método usado (stripe, paypal, etc)
- `paid_at` - Cuándo se pagó

#### Campos Nuevos - Producción
- `production_status` - pending | in_production | completed | shipped
- `production_started_at` - Cuándo inició producción
- `completed_at` - Cuándo se completó
- `tracking_number` - Número de rastreo de envío

---

## 🔄 Flujo Completo del Usuario

### 1. Registro y Acceso
```
Usuario → Registro (gratis) → Email + Password → Acceso al sistema
```

**Rutas:**
- `/register` - Formulario de registro
- `/login` - Iniciar sesión
- `/gang-sheet-builder` - Requiere autenticación

**Middleware:** `auth`

### 2. Creación del Gang Sheet

```
Usuario Autenticado → Gang Sheet Builder → Diseña su hoja
```

**Acciones en el Builder:**
1. Selecciona tamaño de hoja (22x120, 22x60, 13x19, custom)
2. Sube imágenes (drag & drop o upload)
3. Arrastra y posiciona imágenes en el canvas
4. Ajusta tamaños y rotaciones
5. Usa "Auto Build" para optimizar espacio
6. Vista previa en tiempo real

**Estado:** `draft` (borrador)

### 3. Envío para Aprobación

Cuando el usuario está satisfecho con su diseño:

```
Usuario → Botón "Submit for Approval" → Sistema calcula precio → Estado: processing
```

**Proceso Automático:**
```php
$gangSheet->calculatePrice();      // Calcula precio según tamaño y cobertura
$gangSheet->submitForApproval();   // Marca submitted_at, cambia status
```

**Email al Usuario:**
- "Tu gang sheet ha sido enviado para revisión"
- "Recibirás una cotización pronto"
- Vista previa del diseño

**Email al Admin:**
- "Nuevo gang sheet pendiente de aprobación"
- Enlace al panel de revisión
- Precio calculado automáticamente

### 4. Revisión y Aprobación (Admin)

```
Admin → Panel Filament → Lista de Gang Sheets Pendientes → Revisa → Aprueba/Rechaza
```

**Opciones del Admin:**

#### A) Aprobar
```php
$gangSheet->approve($user, "Diseño aprobado. Procede al pago.");
```
- Marca `approved_at` y `approved_by`
- Estado cambia a `completed`
- Envía email a usuario con precio final y enlace de pago

#### B) Rechazar o Solicitar Cambios
```php
$gangSheet->update([
    'status' => 'draft',
    'approval_notes' => 'Por favor ajusta el tamaño de la imagen en la esquina...'
]);
```
- Regresa a draft para que usuario edite
- Email con comentarios del admin

### 5. Pago

```
Usuario → Recibe aprobación → Ve precio final → Procede al pago
```

**Pantalla de Pago:**
- Resumen del gang sheet
- Vista previa del diseño
- Desglose de precio:
  - Precio base según tamaño
  - Descuento por cobertura (si aplica)
  - Total a pagar

**Métodos de Pago (a implementar):**
- Stripe
- PayPal  
- Mercado Pago
- Transferencia/Efectivo (manual)

**Después del Pago Exitoso:**
```php
$gangSheet->markAsPaid($paymentId, $paymentMethod);
```
- `payment_status` = 'paid'
- `production_status` = 'pending'
- Email de confirmación al usuario
- Notificación al equipo de producción

### 6. Producción

```
Admin/Operador → Panel de Producción → Inicia producción
```

```php
$gangSheet->startProduction();
```
- `production_status` = 'in_production'
- Email al usuario: "Tu pedido está en producción"

### 7. Completado y Envío

```
Operador → Completa producción → Ingresa tracking number
```

```php
$gangSheet->markAsCompleted($trackingNumber);
```
- `production_status` = 'completed' o 'shipped'
- Email al usuario con número de rastreo
- Link para rastrear envío

---

## 🎨 Estados del Gang Sheet

### Status (General)
- **draft** - Usuario aún está diseñando
- **processing** - Enviado para aprobación, esperando admin
- **completed** - Aprobado por admin
- **failed** - Rechazado o error

### Payment Status
- **pending** - Esperando pago
- **paid** - Pagado exitosamente
- **failed** - Pago falló
- **refunded** - Reembolsado

### Production Status
- **pending** - Esperando pago o inicio de producción
- **in_production** - En proceso de impresión
- **completed** - Impreso y listo
- **shipped** - Enviado al cliente

---

## 💰 Cálculo de Precios

### Método en GangSheet Model

```php
public function calculatePrice(): void
{
    // 1. Buscar precio por dimensiones exactas
    $pricing = DtfPricing::findByDimensions($this->width, $this->height);
    
    // 2. Si no existe, calcular proporcional
    if (!$pricing) {
        // Usar precio por pulgada cuadrada
        $area = $this->width * $this->height;
        $pricePerSquareInch = $basePrice / $baseArea;
        $this->price = $area * $pricePerSquareInch;
        return;
    }
    
    // 3. Calcular cobertura
    $coveragePercentage = $this->getCoveragePercentage();
    $this->coverage_percentage = $coveragePercentage;
    
    // 4. Aplicar descuento si aplica
    $this->price = $pricing->calculatePrice($coveragePercentage);
}
```

### Ejemplo Real

**Gang Sheet: 22" × 60"**
- Precio base: $95.00
- Cobertura: 85% (supera el 80%)
- Descuento: 10%
- **Precio final: $85.50**

**Gang Sheet: 13" × 19"**  
- Precio base: $28.00
- Cobertura: 50% (no alcanza el 75%)
- Descuento: 0%
- **Precio final: $28.00**

---

## 🔐 Seguridad y Permisos

### Usuarios Regulares
- Pueden crear gang sheets
- Ver solo sus propios gang sheets
- Editar solo en estado `draft` y no aprobados
- No pueden aprobar ni cambiar precios

### Administradores (Filament)
- Ver todos los gang sheets
- Aprobar/rechazar
- Ver panel de producción
- Modificar precios manualmente si es necesario
- Acceso a reportes y métricas

### Middleware Requerido

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/gang-sheet-builder', [GangSheetController::class, 'builder']);
    Route::post('/api/gang-sheets/save', [GangSheetController::class, 'save']);
    Route::post('/api/gang-sheets/{id}/submit', [GangSheetController::class, 'submit']);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/api/gang-sheets/{id}/approve', [GangSheetController::class, 'approve']);
    Route::post('/api/gang-sheets/{id}/reject', [GangSheetController::class, 'reject']);
});
```

---

## 📧 Emails Automáticos

### 1. Usuario Envía Gang Sheet
**Para:** Usuario  
**Asunto:** "Tu Gang Sheet ha sido enviado"  
**Contenido:**
- Confirmación de envío
- Vista previa del diseño
- "Recibirás una cotización pronto"

**Para:** Admin  
**Asunto:** "Nuevo Gang Sheet pendiente de aprobación"  
**Contenido:**
- Datos del usuario
- Vista previa
- Enlace directo al panel de revisión

### 2. Admin Aprueba
**Para:** Usuario  
**Asunto:** "Tu Gang Sheet ha sido aprobado - Procede al pago"  
**Contenido:**
- Diseño aprobado
- **Precio final: $X.XX**
- Enlace al checkout
- Fecha límite de pago (opcional)

### 3. Usuario Paga
**Para:** Usuario  
**Asunto:** "Pago confirmado - Tu pedido está en cola"  
**Contenido:**
- Confirmación de pago
- Número de orden
- Tiempo estimado de producción

**Para:** Producción  
**Asunto:** "Nuevo pedido pagado - Gang Sheet #123"  
**Contenido:**
- Detalles del gang sheet
- Descarga de archivos de alta resolución
- Prioridad

### 4. Producción Iniciada
**Para:** Usuario  
**Asunto:** "Tu pedido está en producción"  
**Contenido:**
- Estado actualizado
- Fecha estimada de envío

### 5. Pedido Completado
**Para:** Usuario  
**Asunto:** "Tu pedido ha sido enviado"  
**Contenido:**
- Número de rastreo
- Link de rastreo
- Fecha estimada de entrega

---

## 🎨 Actualizaciones Necesarias en el Frontend

### Gang Sheet Builder Component

#### Agregar Estado UI
```javascript
const gangSheetState = ref('draft'); // draft | submitted | approved | paid
const requiresPayment = ref(false);
const calculatedPrice = ref(null);
```

#### Botones Condicionales

**Cuando está en Draft:**
```vue
<button @click="saveGangSheet" class="btn-secondary">
  Save Draft
</button>
<button @click="submitForApproval" class="btn-primary">
  Submit for Approval
</button>
```

**Cuando está Submitted (processing):**
```vue
<div class="alert-info">
  Your gang sheet is being reviewed. You'll receive a quote soon.
</div>
```

**Cuando está Approved:**
```vue
<div class="alert-success">
  ✅ Approved! Price: ${{ calculatedPrice }}
</div>
<button @click="proceedToPayment" class="btn-success">
  Proceed to Payment
</button>
```

**Cuando está Paid:**
```vue
<div class="alert-success">
  ✅ Payment confirmed! Order #{{ orderId }}
  <br>Status: {{ productionStatus }}
</div>
```

#### Método Submit
```javascript
const submitForApproval = async () => {
  if (images.value.length === 0) {
    alert('Please add images first');
    return;
  }

  try {
    const response = await fetch(`/api/gang-sheets/${gangSheetId}/submit`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });

    if (response.ok) {
      const result = await response.json();
      gangSheetState.value = 'submitted';
      calculatedPrice.value = result.price;
      alert('Gang sheet submitted for approval!');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error submitting gang sheet');
  }
};
```

---

## 🛠️ Recursos Filament (Admin)

### GangSheetResource

#### Lista Principal
**Columnas:**
- ID
- Usuario
- Tamaño (width × height)
- Precio
- Estado (status badge)
- Payment Status (badge)
- Production Status (badge)
- Submitted At
- Acciones

**Filtros:**
- Por estado
- Por payment status
- Por production status
- Por fecha
- Por usuario

#### Vista de Detalle
**Tabs:**
1. **Overview** - Información general, preview del diseño
2. **Images** - Lista de imágenes con posiciones
3. **Pricing** - Desglose de precio, cobertura, descuentos
4. **Approval** - Notas, historial de aprobación
5. **Payment** - Información de pago
6. **Production** - Estado de producción, tracking

#### Acciones Personalizadas
```php
Actions\Action::make('approve')
    ->requiresConfirmation()
    ->action(fn (GangSheet $record) => $record->approve(auth()->user()))
    ->visible(fn (GangSheet $record) => !$record->approved_at),

Actions\Action::make('start_production')
    ->requiresConfirmation()
    ->action(fn (GangSheet $record) => $record->startProduction())
    ->visible(fn (GangSheet $record) => $record->payment_status === 'paid'),
```

---

## 📈 Reportes y Métricas

### Dashboard Widgets

1. **Gang Sheets Pendientes de Aprobación**
   - Número total
   - Lista rápida con enlaces

2. **Ingresos del Mes**
   - Total de gang sheets pagados
   - Promedio por pedido
   - Gráfica de tendencia

3. **Estado de Producción**
   - Pedidos en cola (paid pero pending)
   - En producción
   - Completados esta semana

4. **Usuarios Activos**
   - Registros nuevos
   - Usuarios con gang sheets activos

---

## ✅ Checklist de Implementación

### Backend ✅
- [x] Migración `add_pricing_and_approval_to_gang_sheets_table`
- [x] Migración `create_dtf_pricing_table`
- [x] Modelo `DtfPricing`
- [x] Seeder `DtfPricingSeeder`
- [x] Métodos en modelo `GangSheet`
- [x] Relaciones en modelo `User`

### Frontend 🔄
- [ ] Agregar estado del gang sheet en UI
- [ ] Botón "Submit for Approval"
- [ ] Pantalla de pago/checkout
- [ ] Notificaciones de estado
- [ ] Vista de historial de gang sheets del usuario

### Admin (Filament) 🔄
- [ ] GangSheetResource con filtros y acciones
- [ ] Panel de aprobación
- [ ] Panel de producción
- [ ] Widgets de dashboard

### Notificaciones 📧
- [ ] Email: Gang sheet submitted (usuario + admin)
- [ ] Email: Gang sheet approved (usuario)
- [ ] Email: Payment confirmed (usuario + producción)
- [ ] Email: Production started (usuario)
- [ ] Email: Order shipped (usuario)

### Pagos 💳
- [ ] Integración con Stripe/PayPal
- [ ] Webhook handlers
- [ ] Página de checkout
- [ ] Confirmación de pago

---

## 🚀 Próximos Pasos

1. **Actualizar GangSheetController** con métodos `submit()`, `approve()`, etc.
2. **Crear página de checkout** para procesar pagos
3. **Configurar notificaciones** por email
4. **Crear recursos Filament** para administración
5. **Agregar middleware de autenticación** a rutas del builder
6. **Testing** del flujo completo

---

**Última actualización:** 3 de junio de 2026  
**Versión:** 2.0.0  
**Estado:** Sistema de cobro por proyecto implementado - Frontend pendiente
