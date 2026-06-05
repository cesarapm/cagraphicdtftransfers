# 📋 Guía Rápida - Sistema de Métodos de Pago

## 🎯 Configuración en Filament

### Acceso
1. Ingresa a `/admin`
2. Ve a **Configuración > Métodos de Pago**

### MercadoPago
1. Habilita el toggle "Habilitar Mercado Pago"
2. Completa:
   - Access Token (obtenerlo de https://www.mercadopago.com.mx/developers)
   - URL de Notificación (ej: `https://tudominio.com/api/mercado-pago/webhook`)
   - Webhook Secret
3. Guardar

### PayPal
1. Habilita el toggle "Habilitar PayPal"
2. Completa:
   - Client ID (obtenerlo de https://developer.paypal.com/dashboard/applications)
   - Client Secret
   - Modo de Operación:
     - **Sandbox**: Para pruebas y desarrollo
     - **Live**: Para producción con pagos reales
3. Guardar
https://developer.paypal.com/api/rest/


### Transferencia Bancaria
1. Habilita el toggle "Habilitar Transferencia Bancaria"
2. Completa:
   - Nombre del Banco (ej: "Banco Nacional")
   - Beneficiario (ej: "Tu Negocio S.A.")
   - Número de Cuenta
   - CLABE Interbancaria
   - Código SWIFT (opcional)
   - **WhatsApp**: número sin + ni espacios (ej: `5551234567`)
   - Instrucciones adicionales
3. Guardar

## 🛍️ Experiencia del Cliente

### Checkout
- Solo verá los métodos de pago que hayas habilitado
- Puede elegir entre:
  - 💳 MercadoPago (si está habilitado)
  - 💰 PayPal (si está habilitado)
  - 🏦 Transferencia Bancaria (si está habilitada)

### Compra con Transferencia
Al completar una orden con transferencia:

1. **Modal de éxito** mostrará:
   - Número de orden
   - Total a pagar
   - 📱 Datos bancarios completos:
     - Banco
     - Beneficiario
     - Cuenta
     - CLABE
   - Instrucciones personalizadas

2. **Botón de WhatsApp**:
   - Abre chat con tu número configurado
   - Mensaje pre-llenado con:
     - Número de orden
     - Datos del cliente
     - Productos
     - Total

## 🔧 API Endpoints

```
GET /api/payment-methods
- Retorna métodos habilitados
- Incluye datos bancarios si aplica

GET /api/payment-methods/bank-info
- Retorna solo info bancaria
```

## 💡 Consejos

1. **Control total de métodos de pago**
   - Puedes habilitar todos, algunos o ninguno
   - Si todos están deshabilitados, el checkout mostrará un mensaje informativo
   - Los cambios se reflejan inmediatamente en el frontend

2. **PayPal Sandbox vs Live**
   - Usa **Sandbox** para pruebas con credenciales de prueba
   - Cambia a **Live** cuando tengas credenciales de producción
   - Las credenciales de Sandbox y Live son diferentes

3. **WhatsApp para Transferencias**
   - Asegúrate de que el número sea correcto
   - Formato: solo números sin + ni espacios
   - Ejemplo: `5551234567`

4. **Instrucciones personalizadas**
   - Usa este campo para agregar detalles importantes
   - Ejemplo: "Envía tu comprobante en las próximas 24 horas"

5. **Testing**
   - Habilita solo un método a la vez para probar
   - Verifica que los redirects funcionen correctamente (MercadoPago, PayPal)
   - Prueba el flujo de WhatsApp para transferencias

## ⚡ Cache y Actualización

El sistema usa cache de Laravel para optimizar el rendimiento. Los cambios en Filament se reflejan inmediatamente gracias a la limpieza automática de cache al guardar.

Si por alguna razón necesitas limpiar el cache manualmente:

```bash
php artisan cache:clear
```

O desde Tinker (para cache específico):

```bash
php artisan tinker
>>> Cache::flush();
```

El modelo `Setting` gestiona automáticamente el cache, limpiándolo cuando se actualiza cualquier configuración.

## 🚀 Características

- ✅ Configuración 100% desde Filament (sin tocar código)
- ✅ 3 métodos de pago: MercadoPago, PayPal y Transferencia Bancaria
- ✅ Métodos pueden habilitarse/deshabilitarse dinámicamente
- ✅ Frontend se actualiza automáticamente
- ✅ PayPal con soporte para Sandbox y Live
- ✅ WhatsApp integrado con mensaje pre-llenado para transferencias
- ✅ Modal con información bancaria completa
- ✅ Instrucciones personalizables
- ✅ Responsive y profesional

## 📱 Flujo Completo

```
Cliente → Checkout → Selecciona método de pago
                                  ↓
                [MercadoPago]  [PayPal]  [Transferencia]
                      ↓            ↓            ↓
                Redirige a MP  Redirige   Modal con datos
                               a PayPal         ↓
                                          Botón WhatsApp
                                                ↓
                                          Chat pre-llenado
```

## 🎨 Personalización

Para cambiar colores o estilos del checkout, edita:
- `resources/js/pages/Checkout.vue` (sección `<style>`)

Para cambiar el mensaje de WhatsApp:
- Función `buildTransferWhatsAppUrl` en Checkout.vue

---

**¡Listo para usar!** 🎉

Consulta SETUP.md para más configuraciones del proyecto.
