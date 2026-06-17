# Casos de Uso y Ejemplos Prácticos - PNG Transparente DTF

## 📚 Casos de Uso Reales

### Caso 1: Gang Sheet Simple (2 imágenes + fondo blanco)

**Escenario:**
- Usuario sube 2 imágenes de camisetas
- Sistema automáticamente llena canvas con blanco
- Usuario descarga para impresión DTF

**Antes (Problema):**
```
Canvas:
┌─────────────────────────────┐
│░░░░░░░░░░░░░░░░░░░░░░░░░░░ │ ← Fondo blanco
│░░ [Camiseta 1] ░░░░░░░░░░░░ │
│░░░░░░░ [Camiseta 2] ░░░░░░░░ │
│░░░░░░░░░░░░░░░░░░░░░░░░░░░ │
└─────────────────────────────┘

Problema: Fondo blanco sólido (no se puede imprimir bien)
```

**Después (Solución):**
```
Canvas:
┌─────────────────────────────┐
│                             │ ← Transparente (invisible)
│    [Camiseta 1]            │
│            [Camiseta 2]    │
│                             │
└─────────────────────────────┘

Solución: Fondo transparente (perfecto para DTF)
```

**Logs de ejecución:**
```
🖼️ === INICIO EXPORTACIÓN DTF CON TRANSPARENCIA ===
📊 Total imágenes en canvas: 2
📏 Tamaño inicial: 52800 x 24000 px
✅ Canvas creado con transparencia habilitada (sin fondo blanco)
📷 Imagen 1/2: shirt1.png
✅ Dibujada exitosamente
📷 Imagen 2/2: shirt2.png
✅ Dibujada exitosamente
🔍 Verificando si hay fondo blanco...
⚠️ Fondo blanco detectado, procesando...
🔄 Procesando fondo blanco...
  ├─ Procesando píxeles: R>240, G>240, B>240
  ├─ Aplicando transparencia gradual (anti-aliasing)
  └─ Píxeles blancos: 1,245,000 → convertidos a transparentes
✅ Fondo blanco procesado con transparencia
🎨 Convirtiendo canvas a PNG transparente...
✅ PNG exportado, tamaño: 15.3 MB
```

---

### Caso 2: Detectar Fondo Blanco Incorrecto

**Escenario:**
- Fondo NOT completamente blanco (gris claro #F0F0F0)
- Sistema debe detectar y remover

**Cálculos internos:**
```
Píxel: { R: 240, G: 240, B: 240 }
Condición: R > 240 && G > 240 && B > 240 → FALSE

Mejor ajuste para detectar:
const whiteThreshold = 230; // Detectar más grises

Píxel: { R: 240, G: 240, B: 240 }
Condición: R > 230 && G > 230 && B > 230 → TRUE ✅

Procesamiento:
  avgColor = (240 + 240 + 240) / 3 = 240
  intensity = (240 - 230) / (255 - 230) = 10 / 25 = 0.4
  newAlpha = 255 * (1 - 0.4) = 153 (semi-transparente)
```

---

### Caso 3: Imagen con Transparencia Original

**Escenario:**
- Usuario carga PNG con transparencia (ejemplo: logo con fondo transparente)
- Sistema debe preservar transparencia existente

**Validación automática:**
```javascript
// Píxel original (parte transparente del logo):
{
  R: 0,
  G: 0,
  B: 0,
  A: 0  // ← Ya es transparente
}

// En removeWhiteBackground():
if (a > 0) {  // ← Solo procesa píxeles con alpha > 0
  // Procesar
}
// Este píxel tiene A=0, así que NO se procesa
// ✅ Transparencia original preservada
```

---

### Caso 4: Gang Sheet Grande (Reducción automática de DPI)

**Escenario:**
- Hoja de 264" × 120" (22 × 10 pies)
- Usuario selecciona 300 DPI
- Canvas sería 79,200 × 36,000 px = 2,851 megapixels (¡DEMASIADO!)

**Automático fallback:**
```
📏 Tamaño solicitado: 79200 x 36000 px
💾 Megapixels: 2851.2 MP
MAX_MEGAPIXELS: 250

Detección: 2851.2 > 250 → REDUCIR DPI

Cálculo:
  scaleFactor = √(250 / 2851.2) = 0.295
  newDPI = floor(300 * 0.295) = 88

⚠️ DPI ajustado: 300 → 88
📏 Tamaño ajustado: 23,232 x 10,560 px
💾 Megapixels ajustados: 245.4 MP

✅ Dentro de límites, proceeding...
```

---

### Caso 5: Gang Sheet con Múltiples Capas

**Escenario:**
- Usuario tiene 10 imágenes diferentes
- Algunas con bordes complejos
- Algunas con sombras

**Procesamiento inteligente:**
```
Canvas (transparente):

┌────────────────────────────────┐
│                                │
│  [Logo1]  [Logo2]  [Logo3]    │
│                                │
│  [Shirt1] [Shirt2] [Shirt3]   │
│                                │ ← Bordes con Anti-aliasing
│  [Hat1]   [Hat2]   [Hat3]     │    preservados ✅
│                                │ ← Sombras preservadas ✅
│  [Graphic1]                    │
│                                │
└────────────────────────────────┘

removeWhiteBackground() únicamente:
- Remueve píxeles BLANCOS (R>240, G>240, B>240, A>0)
- Preserva TODO lo demás:
  ✅ Logos originales
  ✅ Sombras
  ✅ Anti-aliasing
  ✅ Transparencias originales
```

---

## 🎬 Ejemplos de Código Paso a Paso

### Ejemplo 1: Detectar Fondo Blanco

```javascript
// Paso 1: Crear un canvas con contenido
const canvas = document.createElement('canvas');
canvas.width = 1200;
canvas.height = 800;
const ctx = canvas.getContext('2d');

// Simular fondo blanco + imagen
ctx.fillStyle = 'white';
ctx.fillRect(0, 0, canvas.width, canvas.height);

// Dibujar una imagen
const img = new Image();
img.src = 'shirt.png';
img.onload = () => {
  ctx.drawImage(img, 100, 100, 600, 600);
  
  // Paso 2: Detectar fondo blanco
  console.log('¿Tiene fondo blanco?', hasWhiteBackground(canvas));
  // Output: true ✅
};
```

---

### Ejemplo 2: Remover Fondo Blanco Paso a Paso

```javascript
const canvas = /* canvas con fondo blanco */;

// Antes de remover:
const ctx = canvas.getContext('2d', { willReadFrequently: true });
const beforeData = ctx.getImageData(0, 0, 100, 100);
console.log('Píxel [0,0] ANTES:', {
  r: beforeData.data[0],  // 255 (blanco)
  g: beforeData.data[1],  // 255 (blanco)
  b: beforeData.data[2],  // 255 (blanco)
  a: beforeData.data[3]   // 255 (opaco)
});
// Output: { r: 255, g: 255, b: 255, a: 255 }

// Remover fondo blanco
removeWhiteBackground(canvas);

// Después de remover:
const afterData = ctx.getImageData(0, 0, 100, 100);
console.log('Píxel [0,0] DESPUÉS:', {
  r: afterData.data[0],  // 255 (sin cambios RGB)
  g: afterData.data[1],  // 255 (sin cambios RGB)
  b: afterData.data[2],  // 255 (sin cambios RGB)
  a: afterData.data[3]   // 0 (¡Transparente!)
});
// Output: { r: 255, g: 255, b: 255, a: 0 }
// ✅ RGB preservado, alpha = 0 (transparente)
```

---

### Ejemplo 3: Exportar PNG vs JPEG

```javascript
const canvas = /* canvas generado */;

// ❌ INCORRECTO: Exportar como JPEG
const jpegURL = canvas.toDataURL('image/jpeg', 0.95);
// Problemas:
// - No hay canal alpha
// - Compresión con pérdida
// - Fondo blanco no se puede remover
// - No apto para DTF

// ✅ CORRECTO: Exportar como PNG
const pngURL = canvas.toDataURL('image/png');
// Ventajas:
// ✅ Preserva canal alpha (transparencia)
// ✅ Sin pérdida (máxima calidad)
// ✅ Perfectamente apto para DTF
// ✅ Archivo más grande pero de máxima calidad

// Comparación de tamaño:
console.log('JPEG:', jpegURL.length / 1024 / 1024, 'MB');  // Menor
console.log('PNG:', pngURL.length / 1024 / 1024, 'MB');    // Mayor (pero mejor calidad)
```

---

### Ejemplo 4: Flujo Completo de Guardado

```javascript
async function saveGangSheetTransparent() {
  try {
    // 1. Generar PNG transparente
    console.log('1️⃣ Generando PNG...');
    const pngURL = await generatePrintFile();
    console.log('✅ PNG generado');

    // 2. Convertir a Blob
    console.log('2️⃣ Convirtiendo a Blob...');
    const response = await fetch(pngURL);
    const pngBlob = await response.blob();
    console.log('✅ Blob creado:', (pngBlob.size / 1024 / 1024).toFixed(2), 'MB');

    // 3. Preparar FormData
    console.log('3️⃣ Preparando FormData...');
    const formData = new FormData();
    formData.append('width', 264);
    formData.append('height', 120);
    formData.append('unit', 'inches');
    formData.append('format', 'png');  // ⭐ CRÍTICO
    formData.append('dpi', 200);
    formData.append('gang_sheet_image', pngBlob, 'gang-sheet.png');
    console.log('✅ FormData preparado');

    // 4. Enviar al servidor
    console.log('4️⃣ Enviando al servidor...');
    const saveResponse = await fetch('/api/gang-sheets/save', {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json',
      },
    });
    console.log('✅ Enviado (status:', saveResponse.status + ')');

    // 5. Procesar respuesta
    console.log('5️⃣ Procesando respuesta...');
    const data = await saveResponse.json();
    console.log('✅ Guardado! ID:', data.data.id);

    return data.data;

  } catch (error) {
    console.error('❌ Error:', error.message);
    throw error;
  }
}

// Uso:
saveGangSheetTransparent()
  .then(gangSheet => {
    alert(`✅ Gang Sheet guardado! ID: ${gangSheet.id}`);
  })
  .catch(error => {
    alert(`❌ Error: ${error.message}`);
  });
```

---

### Ejemplo 5: Validación de PNG Transparente

```javascript
// Verificar que PNG descargado tiene transparencia
async function verifyPngTransparency(pngUrl) {
  return new Promise((resolve) => {
    const img = new Image();
    img.onload = () => {
      // Dibujar en canvas
      const canvas = document.createElement('canvas');
      canvas.width = 100;
      canvas.height = 100;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, 100, 100);

      // Verificar píxeles
      const imageData = ctx.getImageData(0, 0, 100, 100);
      const data = imageData.data;

      let transparentPixels = 0;
      for (let i = 3; i < data.length; i += 4) {
        if (data[i] < 255) {  // Alpha < 255 = transparente
          transparentPixels++;
        }
      }

      const hasTransparency = transparentPixels > (100 * 100 * 0.1); // >10% transparencia

      console.log('📊 Análisis PNG:');
      console.log('  Píxeles transparentes:', transparentPixels, '/', 10000);
      console.log('  ¿Tiene transparencia?:', hasTransparency ? '✅ SÍ' : '❌ NO');

      resolve({
        hasTransparency,
        transparentPixelCount: transparentPixels,
        coverage: (transparentPixels / 10000 * 100).toFixed(2) + '%',
      });
    };
    img.src = pngUrl;
  });
}

// Uso:
const pngUrl = URL.createObjectURL(pngBlob);
verifyPngTransparency(pngUrl).then(result => {
  if (result.hasTransparency) {
    console.log('✅ PNG tiene transparencia!');
    console.log('   Cobertura:', result.coverage);
  } else {
    console.log('❌ PNG sin transparencia detectada');
  }
});
```

---

## 📊 Comparativa Visual

### Antes vs Después

```
ANTES (Problema):
┌─────────────────────────────┐
│ FFFFFF FFFFFF FFFFFF FFFFFF │ ← Fondo blanco puro
│ FFFFFF [Imagen] FFFFFF      │   (no se puede imprimir)
│ FFFFFF FFFFFF FFFFFF FFFFFF │
└─────────────────────────────┘
Formato: JPEG o PNG sin alpha
Resultado DTF: ❌ Fondo blanco impreso (SE VE MAL)

DESPUÉS (Solución):
┌─────────────────────────────┐
│ 000000 000000 000000 000000 │ ← Transparente (alfa=0)
│ 000000 [Imagen] 000000      │   (no se imprime)
│ 000000 000000 000000 000000 │
└─────────────────────────────┘
Formato: PNG con alpha
Resultado DTF: ✅ Sin fondo blanco (SE VE BIEN)
```

---

### Tabla de Valores

```
Píxel Original          Después removeWhiteBackground()   Resultado
─────────────────────────────────────────────────────────────────
R:255 G:255 B:255 A:255 → R:255 G:255 B:255 A:0       ✅ Transparente
R:240 G:240 B:240 A:255 → R:240 G:240 B:240 A:150     ✅ Semi-transparente
R:200 G:200 B:200 A:255 → R:200 G:200 B:200 A:255     ✅ Sin cambios (no es blanco)
R:255 G:200 B:255 A:255 → R:255 G:200 B:255 A:255     ✅ Sin cambios (no es blanco)
R:0   G:0   B:0   A:255 → R:0   G:0   B:0   A:255     ✅ Sin cambios (negro)
R:0   G:0   B:0   A:0   → R:0   G:0   B:0   A:0       ✅ Sin cambios (ya transparente)
```

---

## 🔍 Debugging Avanzado

### Inspeccionar Canvas Transparente

```javascript
// Verificar que canvas está en modo transparencia
const canvas = document.createElement('canvas');
const ctx = canvas.getContext('2d', { alpha: true });

// Llenar con transparencia
const imageData = ctx.createImageData(100, 100);
const data = imageData.data;
// Todos los píxeles tienen A=0 por defecto

// Verificar
console.log('Píxel [0,0]:', {
  r: data[0],  // 0
  g: data[1],  // 0
  b: data[2],  // 0
  a: data[3]   // 0 ← ✅ Transparente
});

ctx.putImageData(imageData, 0, 0);
```

### Monitorear Cambios de Alpha

```javascript
function analyzeAlphaChanges(canvas) {
  const ctx = canvas.getContext('2d', { willReadFrequently: true });
  const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data;

  const alphaStats = {
    transparent: 0,      // A = 0
    semiTransparent: 0,  // 0 < A < 255
    opaque: 0            // A = 255
  };

  for (let i = 3; i < data.length; i += 4) {
    const alpha = data[i];
    if (alpha === 0) alphaStats.transparent++;
    else if (alpha === 255) alphaStats.opaque++;
    else alphaStats.semiTransparent++;
  }

  console.log('📊 Análisis de Alfa:');
  console.log('  Transparentes:', alphaStats.transparent);
  console.log('  Semi-transparentes:', alphaStats.semiTransparent);
  console.log('  Opacos:', alphaStats.opaque);

  return alphaStats;
}

// Antes de removeWhiteBackground():
console.log('ANTES:');
analyzeAlphaChanges(canvas);

// Después de removeWhiteBackground():
console.log('DESPUÉS:');
analyzeAlphaChanges(canvas);
```

---

## ✅ Checklist de Validación

Para cada gang sheet generado, verificar:

- [ ] ¿Canvas creado con `{ alpha: true }`?
- [ ] ¿Sin `fillRect` blanco?
- [ ] ¿Imagen PNG importada correctamente?
- [ ] ¿`imageSmoothingQuality = 'high'`?
- [ ] ¿Fondo blanco detectado?
- [ ] ¿Fondo blanco removido?
- [ ] ¿Exportado como PNG (no JPEG)?
- [ ] ¿Archivo descargado tiene transparencia?
- [ ] ¿FormData contiene `format: 'png'`?
- [ ] ¿Backend guarda PNG (no lo convierte)?
- [ ] ¿Verificación de alfa exitosa?

---

**Última actualización:** 2026-06-17  
**Version:** 1.0  
**Status:** ✅ Ejemplos listos para copiar y pegar
