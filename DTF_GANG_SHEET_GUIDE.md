# DTF Gang Sheet Builder - Guía de Desarrollo

## 🎨 Características Implementadas

### 1. Editor Visual (Vue + Konva.js)
- ✅ Canvas interactivo con drag & drop
- ✅ Redimensionamiento de imágenes con transformadores
- ✅ Múltiples tamaños de hoja predefinidos
- ✅ Grid de referencia
- ✅ Vista previa en tiempo real
- ✅ Cálculo de cobertura automático

### 2. Backend Laravel
- ✅ Modelo `GangSheet` con relaciones
- ✅ Migración de base de datos
- ✅ API RESTful completa
- ✅ Controlador con validación
- ✅ Almacenamiento de imágenes

### 3. Funcionalidades del Editor

#### Upload de Imágenes
- Drag & drop desde el navegador
- Upload tradicional con botón
- Validación de formatos (PNG, JPG, JPEG, SVG)
- Vista previa de imágenes subidas

#### Manipulación del Canvas
- **Arrastrar**: Mueve imágenes libremente
- **Redimensionar**: Usa las esquinas para ajustar tamaño
- **Seleccionar**: Click para editar propiedades
- **Eliminar**: Botón para borrar imágenes seleccionadas

#### Auto Build (Bin Packing)
- Algoritmo First Fit Decreasing
- Ordenamiento por altura
- Márgenes de seguridad (0.25")
- Optimización de espacio automática

#### Tamaños de Hoja
- 22" × 120" (10 ft) - $165
- 22" × 60" (5 ft) - $95
- 13" × 19" - $28
- Tamaño personalizado

## 🔧 Próximas Mejoras Sugeridas

### 1. Generación de Imágenes de Alta Resolución
```php
// Implementar en GangSheetController::generateHighResImage()
// Usar Imagick para:
- Crear canvas a 300 DPI
- Cargar imágenes originales en alta resolución
- Posicionar según coordinates guardadas
- Exportar PNG con transparencia
- Aplicar compresión óptima
```

### 2. Eliminación de Fondos con IA
Opciones recomendadas:
- **remove.bg API**: Servicio externo de pago
- **rembg (Python)**: Librería open source
- **BackgroundRemover**: Paquete PHP con ML

Implementación sugerida:
```php
// Crear servicio BackgroundRemovalService
public function removeBackground($imagePath) {
    // Integrar API de remove.bg
    // O ejecutar script Python con rembg
    // Retornar imagen con fondo transparente
}
```

### 3. Mejoras en el Algoritmo de Bin Packing

Implementar algoritmos más eficientes:
- **Guillotine Algorithm**: Mejor para formas rectangulares
- **Shelf Algorithm**: Empaquetado por filas
- **MaxRects**: El más eficiente para DTF

```javascript
// resources/js/composables/useBinPacking.js
export function useAdvancedBinPacking() {
  const maxRectsAlgorithm = (images, sheetWidth, sheetHeight) => {
    // Implementar MaxRects BSSF
    // Retornar posiciones optimizadas
  }
}
```

### 4. Validación de Calidad de Imagen
```javascript
// En el componente GangSheetEditor
const validateImageQuality = (img, desiredSizeInches) => {
  const requiredPixels = desiredSizeInches * 300; // 300 DPI
  const actualPixels = img.width; // en pixels
  
  if (actualPixels < requiredPixels) {
    return {
      valid: false,
      warning: 'Low resolution - may appear pixelated',
      dpi: Math.round(actualPixels / desiredSizeInches)
    };
  }
  
  return { valid: true, dpi: 300 };
}
```

### 5. Sistema de Plantillas
Permitir guardar y reutilizar layouts comunes:
```php
// Nueva tabla: gang_sheet_templates
Schema::create('gang_sheet_templates', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->json('layout_data');
    $table->boolean('is_public');
    $table->timestamps();
});
```

### 6. Vista Previa 3D
Usar Three.js para mostrar cómo se vería el transfer aplicado:
```javascript
import * as THREE from 'three';

const show3DPreview = (gangSheetImage) => {
  // Crear escena 3D
  // Aplicar textura del gang sheet
  // Mostrar en camiseta/producto 3D
}
```

### 7. Precios Dinámicos
```php
// En GangSheet model
public function calculatePrice() {
    $basePrice = $this->getPriceBySize();
    $coverageDiscount = $this->getCoveragePercentage() > 80 ? 0.1 : 0;
    $rushFee = $this->rush_order ? 0.25 : 0;
    
    return $basePrice * (1 - $coverageDiscount + $rushFee);
}
```

### 8. Integración con Carrito de Compras
```javascript
// En GangSheetEditor
const addToCart = async () => {
  const gangSheetData = await saveGangSheet();
  
  cart.addItem({
    type: 'gang_sheet',
    gang_sheet_id: gangSheetData.id,
    price: calculatePrice(gangSheetData),
    preview: gangSheetData.preview_path
  });
  
  router.push({ name: 'Cart' });
}
```

### 9. Sistema de Aprobación de Clientes
```php
// Nueva ruta
Route::get('/gang-sheets/{id}/approve/{token}', [GangSheetController::class, 'customerApproval']);

// En el controlador
public function customerApproval($id, $token) {
    $gangSheet = GangSheet::where('id', $id)
        ->where('approval_token', $token)
        ->firstOrFail();
    
    return view('gang-sheets.approval', compact('gangSheet'));
}
```

### 10. Exportar a PDF para Impresión
```php
use Barryvdh\DomPDF\Facade\Pdf;

public function exportPDF($id) {
    $gangSheet = GangSheet::findOrFail($id);
    
    $pdf = PDF::loadView('gang-sheets.print', [
        'gangSheet' => $gangSheet,
        'highRes' => true
    ]);
    
    return $pdf->download('gang-sheet-' . $id . '.pdf');
}
```

## 📊 Modelo de Datos

### Tabla: gang_sheets
```
id                 - Primary Key
user_id           - Foreign Key (admin que creó)
customer_id       - Foreign Key (cliente)
order_id          - Foreign Key (pedido asociado)
name              - Nombre del gang sheet
width             - Ancho en pulgadas
height            - Alto en pulgadas
dpi               - Resolución (default: 300)
images_data       - JSON con posiciones y tamaños
preview_path      - Ruta de imagen preview
final_path        - Ruta de imagen final
total_area        - Área total usada
image_count       - Cantidad de imágenes
status            - draft/processing/completed/failed
notes             - Notas adicionales
created_at
updated_at
```

## 🎯 Rutas API

```
GET    /api/gang-sheets              - Listar todos
GET    /api/gang-sheets/{id}         - Ver uno específico
POST   /api/gang-sheets/save         - Guardar nuevo
PUT    /api/gang-sheets/{id}         - Actualizar
DELETE /api/gang-sheets/{id}         - Eliminar
POST   /api/gang-sheets/{id}/generate - Generar imagen final
POST   /api/gang-sheets/upload-image - Subir imagen
```

## 🚀 Uso

### Acceso al Editor
```
URL: http://localhost:8000/gang-sheet-builder
Ruta Vue: /gang-sheet-builder
```

### Flujo de Trabajo
1. Cliente selecciona tamaño de hoja
2. Sube imágenes de diseños
3. Arrastra y acomoda en el canvas (o usa Auto Build)
4. Guarda el gang sheet
5. Sistema genera preview
6. Opcionalmente genera imagen final a 300 DPI
7. Se agrega al pedido

## 🎨 Estilos y UI

El editor usa:
- **Tailwind CSS** para estilos
- **Konva.js** para el canvas interactivo
- **Vue 3 Composition API** para la lógica
- **SVG Icons** para íconos

### Personalización de Colores
```javascript
// En el componente, puedes cambiar:
const colors = {
  primary: '#0066ff',    // Azul principal
  selection: '#00ff00',  // Verde selección
  grid: '#dddddd',       // Gris grid
  background: '#ffffff'  // Blanco fondo
}
```

## 📦 Dependencias Instaladas

```json
{
  "konva": "^9.x",      // Canvas library
  "vue-konva": "^3.x"   // Vue wrapper for Konva
}
```

## 🐛 Solución de Problemas

### El canvas no se muestra
```bash
# Verifica que se instalaron las dependencias
npm install

# Reconstruye el proyecto
npm run build
```

### Las imágenes no se suben
```bash
# Verifica permisos de storage
php artisan storage:link
chmod -R 775 storage/app/public
```

### Error en las rutas API
```bash
# Limpia caché de rutas
php artisan route:clear
php artisan route:cache
```

## 📝 Notas de Desarrollo

- El DPI del canvas es 72 para display, se escala a 300 para export
- Las medidas en el código son en pulgadas (inches)
- El algoritmo de bin packing es básico, se puede mejorar significativamente
- La generación de imagen final requiere implementación con Imagick
- Se recomienda agregar validación de tamaño máximo de archivo
- Considerar agregar WebSocket para status en tiempo real de procesamiento

## 🔐 Seguridad

Pendientes de implementar:
- [ ] Autenticación para guardar gang sheets
- [ ] Rate limiting en uploads
- [ ] Validación de tipos MIME reales
- [ ] Sanitización de nombres de archivo
- [ ] Límites de tamaño de imagen
- [ ] Protección CSRF en formularios

## 📚 Recursos Adicionales

- [Konva.js Documentation](https://konvajs.org/)
- [Vue Konva](https://konvajs.org/docs/vue/)
- [Bin Packing Algorithms](https://codeincomplete.com/articles/bin-packing/)
- [ImageMagick PHP](https://www.php.net/manual/en/book.imagick.php)
- [DTF Printing Guide](https://dtftransfers.com/pages/dtf-printing-guide)

---

**Última actualización**: 2 de junio de 2026
**Versión**: 1.0.0
**Estado**: Beta - Funcional con mejoras pendientes
