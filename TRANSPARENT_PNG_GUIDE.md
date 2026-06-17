# Guía: Exportación DTF con Fondos Transparentes

## 📋 Resumen de Cambios

Se ha implementado un sistema completo para exportar gang sheets en PNG con **fondo transparente** para impresión DTF profesional. El sistema automáticamente detecta y remueve fondos blancos mientras preserva máxima calidad.

---

## 🎯 Características Implementadas

### 1. **Detección de Fondo Blanco** (`hasWhiteBackground()`)
```javascript
// Detecta si el canvas tiene fondo blanco sólido
// Analiza muestras de píxeles en los bordes
// Tolerancia configurable (240 = umbral de blanco)
```

**Comportamiento:**
- Muestrea 8 puntos estratégicos del canvas (4 esquinas + 4 centros de lados)
- Si ≥50% de puntos son blancos, activa procesamiento
- Threshold configurable: `const threshold = 240`

### 2. **Remoción de Fondo Blanco** (`removeWhiteBackground()`)
```javascript
// Remueve fondo blanco preservando anti-aliasing
// Mantiene transparencias existentes
// Procesa píxeles gradualmente para bordes suaves
```

**Algoritmo:**
```
Para cada píxel:
  Si (R > 240 && G > 240 && B > 240):
    Calcular intensidad = (promedio - 240) / 15
    Nuevo Alpha = Alpha original * (1 - intensidad)
```

**Ventajas:**
- ✅ Bordes suaves (anti-aliasing preservado)
- ✅ Transparencias gradientes
- ✅ No pixela la imagen
- ✅ Mantiene sombras y efectos

### 3. **Exportación PNG Transparente** (`exportTransparentPNG()`)
```javascript
// Exporta canvas como PNG sin pérdida
// Preserva canal alpha completamente
// Máxima calidad (sin compresión con pérdida)
```

**Especificaciones:**
- Formato: PNG (sin pérdida)
- Compresión: Máxima
- Canal Alpha: ✅ Habilitado
- DPI: Configurable (150, 200, 300)

### 4. **Flujo de Generación Mejorado** (`generatePrintFile()`)

#### Cambios Críticos:
```javascript
// ANTES (problema):
const ctx = canvas.getContext('2d', { willReadFrequently: false });
ctx.fillStyle = 'white';
ctx.fillRect(0, 0, exportWidth, exportHeight); // ❌ Llena con blanco

// DESPUÉS (solución):
const ctx = canvas.getContext('2d', { 
  willReadFrequently: true,
  alpha: true  // ✅ Habilitar canal alpha
});
// Canvas comienza transparente (sin fillRect)
ctx.imageSmoothingEnabled = true;
ctx.imageSmoothingQuality = 'high'; // Anti-aliasing
```

#### Proceso Completo:
1. ✅ Crear canvas con transparencia habilitada
2. ✅ Dibujar imágenes con suavizado alto
3. ✅ Detectar si hay fondo blanco
4. ✅ Remover fondo blanco si existe
5. ✅ Exportar como PNG transparente

---

## 🔧 Parámetros Configurables

### En `removeWhiteBackground()`:
```javascript
const tolerance = 15;           // Rango de tolerancia para tonos cercanos
const whiteThreshold = 240;     // Umbral mínimo para considerar "blanco"
```

### En `hasWhiteBackground()`:
```javascript
const threshold = 240;          // Umbral de detección
// Activa si ≥50% de puntos muestreados son blancos
```

### En `generatePrintFile()`:
```javascript
const MAX_MEGAPIXELS = 250;     // Límite máximo de píxeles
exportDPI: 150, 200, 300        // Calidades disponibles
```

---

## 📊 Optimizaciones Implementadas

### Rendimiento:
| Aspecto | Antes | Después |
|--------|-------|---------|
| Canvas Alpha | ❌ No | ✅ Sí |
| Fondo Blanco | ❌ Sí | ✅ Automático |
| Anti-aliasing | ⚠️ Defecto | ✅ Alto |
| Transparencia | ❌ No | ✅ Preservada |

### Calidad:
```
PNG = Sin pérdida (ideal para DTF)
Canvas Alpha = Máxima precisión
Suavizado = High (calidad profesional)
DPI = 150-300 (configurable)
```

---

## 📝 Cambios en Funciones

### `downloadGangSheet(event)`
```javascript
✅ Genera PNG transparente
✅ Detecta y remueve fondo blanco automáticamente
✅ Preserva máxima calidad
✅ Muestra información detallada de exportación
✅ Validación mejorada de errores
```

### `saveGangSheet(event)`
```javascript
✅ Genera PNG compilado transparente (nuevo)
✅ Envía PNG compilado al servidor
✅ Incluye DPI en metadata
✅ Especifica formato: 'png' (no JPG)
✅ Mantiene archivos originales como respaldo
```

### `generatePrintFile()`
```javascript
✅ Canvas transparente (sin fondo blanco)
✅ Detección automática de fondos blancos
✅ Remoción inteligente con anti-aliasing
✅ Exportación PNG sin pérdida
✅ Logging detallado para debugging
```

---

## 🖼️ Flujo de Exportación

```
┌─────────────────────────────────────────┐
│  Usuario hace click "Download PNG"      │
└──────────┬──────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────┐
│  generatePrintFile()                    │
│  ├─ Crear canvas transparente          │
│  ├─ Dibujar imágenes (DPI seleccionado)│
│  ├─ Detectar fondo blanco              │
│  └─ Exportar PNG                       │
└──────────┬──────────────────────────────┘
           │
           ├─ hasWhiteBackground()
           │  └─ Muestrear píxeles
           │
           ├─ removeWhiteBackground()
           │  └─ Procesar transparencia
           │
           └─ exportTransparentPNG()
              └─ toDataURL('image/png')
           │
           ▼
┌─────────────────────────────────────────┐
│  Convertir a Blob                       │
│  Crear URL Object                       │
│  Descargar archivo                      │
└─────────────────────────────────────────┘
```

---

## 🚀 Flujo de Guardado en Servidor

```
┌─────────────────────────────────────────┐
│  Usuario hace click "Save to Server"    │
└──────────┬──────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────┐
│  saveGangSheet()                        │
│  ├─ Generar PNG transparente compilado │
│  ├─ Preparar metadata                  │
│  ├─ Crear FormData                     │
│  ├─ Incluir PNG compilado              │
│  ├─ Incluir archivos originales        │
│  └─ Enviar a /api/gang-sheets/save     │
└──────────┬──────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────┐
│  FormData Content:                      │
│  ├─ width, height, unit                │
│  ├─ format: 'png' ← IMPORTANTE          │
│  ├─ dpi: 150|200|300                   │
│  ├─ gang_sheet_image (PNG transparente)│
│  └─ image_files[] (originales)         │
└──────────┬──────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────┐
│  Backend (debe preservar PNG)           │
│  ├─ Validar format === 'png'           │
│  ├─ NO convertir a JPEG                │
│  ├─ Guardar PNG transparente           │
│  └─ Devolver ID                        │
└─────────────────────────────────────────┘
```

---

## 🔌 Recomendaciones para Backend (Laravel)

### En el Controlador `/api/gang-sheets/save`:

```php
// Validar que sea PNG
if ($request->input('format') === 'png') {
    // Guardar PNG directamente (preserva transparencia)
    $pngBlob = $request->file('gang_sheet_image');
    
    // NO convertir a JPEG
    // NO aplicar fondo blanco
    // Guardar como .png
    
    Storage::disk('gang_sheets')->put(
        "gang-sheet-{$gangSheet->id}.png",
        file_get_contents($pngBlob)
    );
}
```

### Configuración de Almacenamiento:

```php
// config/filesystems.php
'gang_sheets' => [
    'driver' => 'local',
    'root' => storage_path('app/gang-sheets'),
    'url' => '/storage/gang-sheets',
    'visibility' => 'public',
],
```

### Modelo GangSheet:

```php
class GangSheet extends Model {
    protected $fillable = [
        'width', 'height', 'unit', 'format', 'dpi',
        'image_path', 'images_metadata'
    ];
    
    // Preservar formato original
    public function getImagePath() {
        $extension = $this->format === 'png' ? 'png' : 'jpg';
        return "storage/gang-sheets/gang-sheet-{$this->id}.{$extension}";
    }
}
```

---

## 🧪 Testing de Transparencia

### Para verificar que la transparencia funciona:

```javascript
// En la consola del navegador:
const pngUrl = 'descarga/tu-archivo.png';
const img = new Image();
img.onload = () => {
  console.log('✅ PNG transparente cargado');
  // Verificar con inspector que tiene canal alpha
};
img.src = pngUrl;
```

### Verificar canal Alpha en Photoshop:
1. Abrir imagen descargada
2. Buscar "Alpha" en Layers
3. Si aparece canal Alpha → ✅ Transparencia preservada

---

## 📊 Especificaciones de Salida

### Archivos Descargados:
```
Nombre: gang-sheet-{width}x{height}in-{dpi}dpi.png
Formato: PNG (RGBA - con transparencia)
Compresión: Máxima (sin pérdida)
DPI: 150, 200 o 300 (configurable)
Fondo: Transparente (no blanco)
```

### Metadatos Guardados:
```json
{
  "width": 264,
  "height": 120,
  "unit": "inches",
  "format": "png",
  "dpi": 200,
  "images": [
    {
      "index": 0,
      "x": 1,
      "y": 1,
      "width": 12,
      "height": 10,
      "name": "shirt.png",
      "originalWidth": 2400,
      "originalHeight": 2000
    }
  ]
}
```

---

## ⚠️ Notas Importantes

### ✅ Comportamiento Correcto:
- [x] PNG siempre transparente (sin fondo blanco)
- [x] Anti-aliasing preservado en bordes
- [x] Sombras y efectos originales mantenidos
- [x] Resolución NO reducida
- [x] Dimensiones exactas preservadas
- [x] Si ya hay transparencia → NO se modifica

### ❌ Evitar:
- ❌ Convertir PNG a JPEG (pierde transparencia)
- ❌ Llenar canvas con blanco antes de dibujar
- ❌ Usar `getContext('2d', { alpha: false })`
- ❌ Descartar datos de transparencia
- ❌ Reducir resolución en backend

---

## 🎓 Ejemplo de Uso Completo

```javascript
// Usuario añade 2 imágenes
// Ajusta tamaños y posiciones
// Selecciona DPI 200 (excelente calidad)

// Hace click "Download PNG"
// Sistema ejecuta:
//   1. generatePrintFile() → PNG transparente
//   2. hasWhiteBackground() → Detecta si hay fondo blanco
//   3. removeWhiteBackground() → Remueve automáticamente
//   4. exportTransparentPNG() → PNG sin pérdida
//   5. Descarga como: gang-sheet-264x120in-200dpi.png

// Resultado: PNG listo para impresión DTF ✅
```

---

## 📞 Debugging

Si tienes problemas, revisa la consola del navegador:

```
🖼️ === INICIO EXPORTACIÓN DTF CON TRANSPARENCIA ===
📊 Total imágenes en canvas: 2
📏 Tamaño inicial: 52800 x 24000 px
📐 DPI solicitado: 200
💾 Megapixels: 1267.2 MP
✅ Canvas creado con transparencia habilitada
📷 Imagen 1/2: shirt.png
📍 Posición: (200, 200)
📏 Tamaño: 2400 × 2000
✅ Dibujada exitosamente
🔍 Verificando si hay fondo blanco...
⚠️ Fondo blanco detectado, procesando...
🔄 Procesando fondo blanco...
✅ Fondo blanco procesado con transparencia
🎨 Convirtiendo canvas a PNG transparente...
✅ PNG exportado
```

---

## 🎯 Próximos Pasos (Opcional)

1. **Optimización de Servidor:**
   - Almacenar PNG en CDN
   - Cachear imágenes compiladas

2. **Verificación Avanzada:**
   - Validar canal alpha en backend
   - Descartar PNG sin transparencia

3. **Mejoras UI:**
   - Mostrar preview con fondo transparente
   - Indicador visual de "fondo transparente"

---

**Última actualización:** 2026-06-17  
**Versión:** 1.0  
**Status:** ✅ Implementado y Testeado
