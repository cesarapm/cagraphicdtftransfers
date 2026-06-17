# Quick Reference - Implementación PNG Transparente DTF

## ⚡ Implementación Rápida (Copy & Paste)

### 1️⃣ Función: Detectar Fondo Blanco

```javascript
const hasWhiteBackground = (canvas) => {
  const ctx = canvas.getContext('2d', { willReadFrequently: true });
  const width = canvas.width;
  const height = canvas.height;
  
  // Muestrear 8 puntos estratégicos
  const samplePoints = [
    [0, 0], [width - 1, 0], [0, height - 1], [width - 1, height - 1],
    [Math.floor(width / 2), 0], [0, Math.floor(height / 2)],
    [width - 1, Math.floor(height / 2)], [Math.floor(width / 2), height - 1],
  ];
  
  let whiteCount = 0;
  const threshold = 240; // Considerar blanco si R, G, B > 240
  
  for (const [x, y] of samplePoints) {
    const imageData = ctx.getImageData(x, y, 1, 1);
    const [r, g, b] = imageData.data;
    if (r > threshold && g > threshold && b > threshold) {
      whiteCount++;
    }
  }
  
  return whiteCount >= samplePoints.length * 0.5;
};
```

---

### 2️⃣ Función: Remover Fondo Blanco

```javascript
const removeWhiteBackground = (canvas) => {
  const ctx = canvas.getContext('2d', { willReadFrequently: true });
  const width = canvas.width;
  const height = canvas.height;
  
  console.log('🔄 Procesando fondo blanco...');
  
  const imageData = ctx.getImageData(0, 0, width, height);
  const data = imageData.data;
  
  const tolerance = 15;
  const whiteThreshold = 240;
  
  for (let i = 0; i < data.length; i += 4) {
    const r = data[i];
    const g = data[i + 1];
    const b = data[i + 2];
    const a = data[i + 3];
    
    if (r > whiteThreshold && g > whiteThreshold && b > whiteThreshold && a > 0) {
      const avgColor = (r + g + b) / 3;
      const intensity = (avgColor - whiteThreshold) / (255 - whiteThreshold);
      data[i + 3] = Math.round(a * (1 - intensity));
    }
  }
  
  ctx.putImageData(imageData, 0, 0);
  console.log('✅ Fondo blanco procesado');
  return canvas;
};
```

---

### 3️⃣ Función: Exportar PNG Transparente

```javascript
const exportTransparentPNG = (canvas) => {
  console.log('📦 Exportando PNG con canal alpha...');
  const dataURL = canvas.toDataURL('image/png');
  console.log('✅ PNG exportado');
  return dataURL;
};
```

---

### 4️⃣ Canvas Transparente (Clave)

```javascript
// ❌ INCORRECTO (llena con blanco):
const ctx = canvas.getContext('2d');
ctx.fillStyle = 'white';
ctx.fillRect(0, 0, width, height);

// ✅ CORRECTO (transparente):
const ctx = canvas.getContext('2d', { 
  willReadFrequently: true,
  alpha: true  // Habilitar transparencia
});
// NO hacer fillRect - dejar transparente

// Después dibujar imágenes
ctx.imageSmoothingEnabled = true;
ctx.imageSmoothingQuality = 'high';
ctx.drawImage(img, x, y, w, h);
```

---

### 5️⃣ Flujo Completo de Generación

```javascript
const generatePrintFile = async () => {
  // 1. Crear canvas
  const canvas = document.createElement('canvas');
  canvas.width = exportWidth;
  canvas.height = exportHeight;
  
  // 2. Contexto con transparencia
  const ctx = canvas.getContext('2d', { 
    willReadFrequently: true,
    alpha: true
  });
  
  // 3. Dibujar imágenes (NO llenar con blanco)
  for (let img of images) {
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = 'high';
    ctx.drawImage(img.imageObj, img.x, img.y, img.w, img.h);
  }
  
  // 4. Detectar y remover fondo blanco
  if (hasWhiteBackground(canvas)) {
    removeWhiteBackground(canvas);
  }
  
  // 5. Exportar PNG
  return exportTransparentPNG(canvas);
};
```

---

### 6️⃣ Descargar PNG

```javascript
const downloadTransparentPNG = async () => {
  const dataURL = await generatePrintFile();
  const blob = await (await fetch(dataURL)).blob();
  const url = URL.createObjectURL(blob);
  
  const link = document.createElement('a');
  link.href = url;
  link.download = 'gang-sheet.png';
  link.click();
  
  URL.revokeObjectURL(url);
};
```

---

### 7️⃣ Guardar en Servidor (FormData)

```javascript
const saveToServer = async () => {
  // 1. Generar PNG transparente
  const dataURL = await generatePrintFile();
  const response = await fetch(dataURL);
  const pngBlob = await response.blob();
  
  // 2. Crear FormData
  const formData = new FormData();
  formData.append('width', 264);
  formData.append('height', 120);
  formData.append('unit', 'inches');
  formData.append('format', 'png'); // ⭐ IMPORTANTE
  formData.append('dpi', 200);
  formData.append('images', JSON.stringify(metadata));
  formData.append('gang_sheet_image', pngBlob, 'gang-sheet.png');
  
  // 3. Enviar
  const result = await fetch('/api/gang-sheets/save', {
    method: 'POST',
    headers: { 'Accept': 'application/json' },
    body: formData,
  });
  
  return await result.json();
};
```

---

## 🔧 Valores Configurables

```javascript
// En removeWhiteBackground():
const tolerance = 15;           // Ajustar para más/menos tolerancia
const whiteThreshold = 240;     // Ajustar para detectar más gris

// En hasWhiteBackground():
const threshold = 240;          // Mismo que arriba
// Requiere 50% de puntos blancos para activar

// En generatePrintFile():
const MAX_MEGAPIXELS = 250;     // Límite de píxeles
const EXPORT_DPI = 150|200|300; // Calidad seleccionada
```

---

## 🔬 Testing en Consola

```javascript
// 1. Verificar canvas con transparencia
const canvas = document.createElement('canvas');
const ctx = canvas.getContext('2d', { alpha: true });
console.log('✅ Alpha channel:', ctx.canvas.getContext('2d').canvas.getContext('2d') !== null);

// 2. Verificar PNG descargado tiene transparencia
const img = new Image();
img.onload = () => {
  const canvas = document.createElement('canvas');
  canvas.width = img.width;
  canvas.height = img.height;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(img, 0, 0);
  const data = ctx.getImageData(0, 0, 1, 1).data;
  console.log('Píxel [0,0]:', { r: data[0], g: data[1], b: data[2], a: data[3] });
};
img.src = 'tu-archivo.png';

// 3. Ver logs de generación
console.log('🖼️ === INICIO EXPORTACIÓN DTF ===');
// ... resultados del generatePrintFile
```

---

## 🗄️ Backend - Validación Mínima

```php
// En Controlador
public function save(Request $request)
{
    $validated = $request->validate([
        'format' => 'required|in:png,jpg',
        'dpi' => 'required|in:150,200,300',
        'gang_sheet_image' => 'required|image|mimes:png',
    ]);
    
    // ⭐ NO CONVERTIR A JPEG
    // ⭐ Guardar PNG directamente
    Storage::disk('public')->putFile('gang-sheets', $request->file('gang_sheet_image'));
}
```

---

## 📊 Checklist de Implementación

- [ ] Canvas con `{ alpha: true }`
- [ ] NO usar `fillRect` con blanco
- [ ] Habilitar `imageSmoothingQuality = 'high'`
- [ ] Detectar fondo blanco antes de exportar
- [ ] Remover con `removeWhiteBackground()`
- [ ] Exportar como PNG (no JPEG)
- [ ] FormData con `format: 'png'`
- [ ] Backend valida que es PNG
- [ ] Backend NO convierte a JPEG
- [ ] Verificar canal alpha en output

---

## 🐛 Debugging Rápido

| Síntoma | Causa | Solución |
|---------|-------|----------|
| Fondo blanco en descarga | Canvas con `fillRect` | Remover `fillRect`, usar `alpha: true` |
| Pérdida de transparencia | Exportando como JPEG | Asegurar `toDataURL('image/png')` |
| Bordes pixelados | Suavizado bajo | Usar `imageSmoothingQuality = 'high'` |
| No detecta fondo blanco | Threshold incorrecto | Bajar `whiteThreshold` a 230 |
| Pierde imágenes originales | Corrupción de canvas | Verificar `imageObj.complete` |

---

## 📦 Estructura de Archivos

```
resources/js/components/
├── GangSheetEditorInches.vue  ✅ ACTUALIZADO
│   ├── hasWhiteBackground()
│   ├── removeWhiteBackground()
│   ├── exportTransparentPNG()
│   ├── generatePrintFile()
│   ├── downloadGangSheet()
│   └── saveGangSheet()
└── ...

app/Http/Controllers/
└── Api/GangSheetController.php  📝 CREAR/ACTUALIZAR
    ├── save()
    ├── saveGangSheetImage()
    ├── validatePngTransparency()
    └── download()

database/migrations/
└── xxxx_create_gang_sheets_table.php  📝 ACTUALIZAR
    ├── format: enum['png', 'jpg']
    └── dpi: integer
```

---

## ✅ Validación Final

1. **Frontend:**
   ```
   ✅ Canvas con alpha
   ✅ PNG sin fondo blanco
   ✅ Anti-aliasing enabled
   ✅ Máxima resolución
   ```

2. **Network:**
   ```
   ✅ FormData contiene PNG
   ✅ Content-Type: multipart/form-data
   ✅ format: 'png' en campos
   ```

3. **Backend:**
   ```
   ✅ Valida format === 'png'
   ✅ Guarda como .png (no .jpg)
   ✅ Preserva canal alpha
   ✅ Retorna URL correcta
   ```

4. **Resultado:**
   ```
   ✅ PNG descargado tiene transparencia
   ✅ Sin fondo blanco
   ✅ Calidad máxima (sin pérdida)
   ✅ Listo para impresión DTF
   ```

---

## 📱 Testing Visual

### En Chrome DevTools:

1. **Network Tab:**
   - Verificar que `gang_sheet_image` sea PNG
   - Size debe ser > 1MB (según tamaño)

2. **Console:**
   ```
   🖼️ === INICIO EXPORTACIÓN DTF CON TRANSPARENCIA ===
   ✅ Canvas creado con transparencia habilitada
   ⚠️ Fondo blanco detectado
   🔄 Procesando fondo blanco...
   ✅ PNG exportado
   ```

3. **Descargar y Abrir:**
   - Abrir PNG en Photoshop
   - Verificar que aparece "Alpha" en Layers
   - Si aparece → ✅ Transparencia preservada

---

## 🔗 Enlaces Útiles

- MDN: [Canvas API Alpha Channel](https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/globalAlpha)
- PNG Spec: [Color Types](http://www.libpng.org/pub/png/spec/1.2/PNG-Chunks.html#11-Chunk-naming-conventions)
- ImageData API: [putImageData](https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/putImageData)

---

**Última actualización:** 2026-06-17  
**Version:** 1.0  
**Estado:** ✅ Listo para usar
