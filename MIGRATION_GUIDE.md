# 🔄 Migración de GangSheetEditorFeet.vue - Guía Paso a Paso

## 📋 Plan de Migración

Esta guía te ayudará a migrar tu componente actual a la nueva arquitectura **sin romper nada**.

---

## Paso 1: Preparación (No tocar código todavía)

### ✅ Archivos ya creados:
- `/resources/js/composables/useUnitConverter.js`
- `/resources/js/composables/useZoomManager.js`
- `/resources/js/composables/useSnapManager.js`
- `/resources/js/components/konva/OptimizedGrid.js` (actualizado)
- `/resources/js/components/konva/OptimizedRuler.js` (actualizado)
- `/resources/js/components/konva/GuideLayer.js` (nuevo)

---

## Paso 2: Backup

```bash
cp resources/js/components/GangSheetEditorFeet.vue resources/js/components/GangSheetEditorFeet.vue.backup
```

---

## Paso 3: Imports - Líneas 223-226

### ❌ Eliminar:
```javascript
import { ref, reactive, computed, onMounted, nextTick, watch } from 'vue';
import { createRuler } from './konva/OptimizedRuler';
import { createGrid, snapPositionToGrid } from './konva/OptimizedGrid';
```

### ✅ Reemplazar con:
```javascript
import { ref, reactive, computed, onMounted, nextTick, watch } from 'vue';
import { useUnitConverter } from '@/composables/useUnitConverter';
import { useZoomManager } from '@/composables/useZoomManager';
import { useSnapManager } from '@/composables/useSnapManager';
import { createGrid } from './konva/OptimizedGrid';
import { createRuler } from './konva/OptimizedRuler';
import { createGuideLayer } from './konva/GuideLayer';
```

---

## Paso 4: Setup - Líneas 231-250

### ❌ Eliminar estas constantes:
```javascript
const DPI_SCREEN = 72;
const DPI_EXPORT = 300;
const FEET_TO_INCHES = 12;
const INCHES_TO_PIXELS_300DPI = 300;
const dpi = DPI_SCREEN;
const snapToGridEnabled = ref(true);
```

### ✅ Agregar después de `setup() {`:
```javascript
// Composables
const converter = useUnitConverter();
const zoom = useZoomManager();
const snap = useSnapManager();

// Constantes del editor
const RULERSIZE = 30;
```

---

## Paso 5: StageConfig - Líneas 252-257

### ❌ Eliminar:
```javascript
const stageConfig = reactive({
  width: 20000,
  height: 13333,
  scaleX: 1,
  scaleY: 1,
});
```

### ✅ Reemplazar con (más abajo, después de las refs del canvas):
```javascript
// Tamaños del canvas en pulgadas (computed)
const canvasWidthInches = computed(() => 
  converter.feetToInches(sheetWidth.value)
);
const canvasHeightInches = computed(() => 
  converter.feetToInches(sheetHeight.value)
);

// Tamaños del canvas en píxeles del editor (computed)
const canvasWidthPixels = computed(() => 
  converter.inchesToEditorPixels(canvasWidthInches.value)
);
const canvasHeightPixels = computed(() => 
  converter.inchesToEditorPixels(canvasHeightInches.value)
);

// Stage config dinámico
const stageConfig = reactive({
  width: 0,
  height: 0,
  scaleX: 1,
  scaleY: 1,
  draggable: false,
});
```

---

## Paso 6: Refs - Líneas 270-280

### ❌ Eliminar:
```javascript
const horizontalRulerShape = ref(null);
const verticalRulerShape = ref(null);
const gridShape = ref(null);
```

### ✅ Reemplazar con (mantener al final de las refs):
```javascript
const guidesLayer = ref(null);

// Shapes optimizados
const gridShape = ref(null);
const horizontalRulerShape = ref(null);
const verticalRulerShape = ref(null);
const guidesShape = ref(null);
```

---

## Paso 7: CoveragePercentage - Líneas 284-293

### ❌ Eliminar:
```javascript
const coveragePercentage = computed(() => {
  const totalArea = sheetWidth.value * sheetHeight.value;
  const usedArea = images.value.reduce((acc, img) => {
    const imgWidthFeet = img.width / (dpi * FEET_TO_INCHES);
    const imgHeightFeet = img.height / (dpi * FEET_TO_INCHES);
    return acc + (imgWidthFeet * imgHeightFeet);
  }, 0);
  return Math.round((usedArea / totalArea) * 100);
});
```

### ✅ Reemplazar con:
```javascript
const coveragePercentage = computed(() => {
  const totalAreaSqInches = canvasWidthInches.value * canvasHeightInches.value;
  const usedAreaSqInches = images.value.reduce((acc, img) => {
    return acc + (img.widthInches * img.heightInches);
  }, 0);
  return Math.round((usedAreaSqInches / totalAreaSqInches) * 100);
});

const exportInfo = computed(() => {
  return converter.estimateExportFileSize(
    canvasWidthInches.value,
    canvasHeightInches.value
  );
});
```

---

## Paso 8: UpdateStageSize - Líneas 309-387

### ❌ Eliminar TODO el contenido de `updateStageSize()` y `updateOptimizedRulersAndGrid()`

### ✅ Reemplazar con:
```javascript
const updateStageSize = () => {
  if (!editorContainer.value) return;
  
  const containerWidth = editorContainer.value.clientWidth - 32;
  const containerHeight = editorContainer.value.clientHeight || 600;
  
  // Calcular zoom to fit
  zoom.zoomToFit(
    canvasWidthPixels.value,
    canvasHeightPixels.value,
    containerWidth,
    containerHeight,
    RULERSIZE
  );
  
  // Actualizar stage config
  const z = zoom.zoomLevel.value;
  stageConfig.width = (canvasWidthPixels.value * z) + RULERSIZE;
  stageConfig.height = (canvasHeightPixels.value * z) + RULERSIZE;
  stageConfig.scaleX = z;
  stageConfig.scaleY = z;
  
  nextTick(() => {
    updateKonvaLayers();
  });
};

const updateKonvaLayers = () => {
  const z = zoom.zoomLevel.value;
  const gridDetail = zoom.getGridDetail.value;
  const rulerDetail = zoom.getRulerDetail.value;
  
  // Grid Layer
  if (gridLayer.value && showGrid.value) {
    const gridLayerNode = gridLayer.value.getNode();
    
    if (gridShape.value) {
      gridShape.value.destroy();
    }
    
    gridShape.value = createGrid({
      canvasWidthInches: canvasWidthInches.value,
      canvasHeightInches: canvasHeightInches.value,
      editorPPI: converter.EDITOR_PPI,
      zoom: z,
      gridDetail,
      visible: showGrid.value,
    });
    
    gridShape.value.setAttrs({
      x: RULERSIZE,
      y: RULERSIZE,
    });
    
    gridLayerNode.add(gridShape.value);
    gridLayerNode.batchDraw();
  }
  
  // Rulers Layer
  if (rulersLayer.value) {
    const rulersLayerNode = rulersLayer.value.getNode();
    
    if (horizontalRulerShape.value) {
      horizontalRulerShape.value.destroy();
    }
    if (verticalRulerShape.value) {
      verticalRulerShape.value.destroy();
    }
    
    horizontalRulerShape.value = createRuler({
      orientation: 'horizontal',
      sizeInches: canvasWidthInches.value,
      rulerHeight: RULERSIZE,
      editorPPI: converter.EDITOR_PPI,
      zoom: z,
      rulerDetail,
    });
    horizontalRulerShape.value.setAttrs({ x: RULERSIZE, y: 0 });
    rulersLayerNode.add(horizontalRulerShape.value);
    
    verticalRulerShape.value = createRuler({
      orientation: 'vertical',
      sizeInches: canvasHeightInches.value,
      rulerHeight: RULERSIZE,
      editorPPI: converter.EDITOR_PPI,
      zoom: z,
      rulerDetail,
    });
    verticalRulerShape.value.setAttrs({ x: 0, y: RULERSIZE });
    rulersLayerNode.add(verticalRulerShape.value);
    
    rulersLayerNode.batchDraw();
  }
  
  // Guides Layer
  if (guidesLayer.value && snap.activeGuides.value.length > 0) {
    const guidesLayerNode = guidesLayer.value.getNode();
    
    if (guidesShape.value) {
      guidesShape.value.destroy();
    }
    
    guidesShape.value = createGuideLayer({
      guides: snap.activeGuides.value,
      canvasWidthInches: canvasWidthInches.value,
      canvasHeightInches: canvasHeightInches.value,
      editorPPI: converter.EDITOR_PPI,
      zoom: z,
    });
    
    guidesShape.value.setAttrs({ x: RULERSIZE, y: RULERSIZE });
    guidesLayerNode.add(guidesShape.value);
    guidesLayerNode.batchDraw();
  }
};
```

---

## Paso 9: ProcessFiles - Líneas 410-430

### ✅ Mantener igual (ya está optimizado con Object URL)

---

## Paso 10: AddImageToCanvas - Líneas 432-453

### ❌ Eliminar:
```javascript
const addImageToCanvas = (uploadedImage) => {
  const defaultWidthFeet = 2;
  const aspectRatio = uploadedImage.originalHeight / uploadedImage.originalWidth;
  const defaultHeightFeet = defaultWidthFeet * aspectRatio;

  for (let i = 0; i < imageQuantity.value; i++) {
    const newImage = {
      id: 'img-' + Date.now() + '-' + Math.random(),
      x: 50 + (i * 20),
      y: 50 + (i * 20),
      width: defaultWidthFeet * FEET_TO_INCHES * DPI_SCREEN,
      height: defaultHeightFeet * FEET_TO_INCHES * DPI_SCREEN,
      scaleX: 1,
      scaleY: 1,
      imageObj: uploadedImage.imageObj,
      originalWidth: uploadedImage.originalWidth,
      originalHeight: uploadedImage.originalHeight,
      name: uploadedImage.name,
    };
    images.value.push(newImage);
  }
};
```

### ✅ Reemplazar con:
```javascript
const addImageToCanvas = (uploadedImage) => {
  const defaultWidthInches = 24; // 2 pies
  const aspectRatio = uploadedImage.originalHeight / uploadedImage.originalWidth;
  const defaultHeightInches = defaultWidthInches * aspectRatio;

  for (let i = 0; i < imageQuantity.value; i++) {
    const newImage = {
      id: 'canvas-' + Date.now() + '-' + Math.random(),
      xInches: 1 + (i * 2), // Offset en pulgadas
      yInches: 1 + (i * 2),
      widthInches: defaultWidthInches,
      heightInches: defaultHeightInches,
      imageObj: uploadedImage.imageObj,
      originalWidth: uploadedImage.originalWidth,
      originalHeight: uploadedImage.originalHeight,
      name: uploadedImage.name,
      file: uploadedImage.file,
    };
    images.value.push(newImage);
  }
};
```

---

## Paso 11: HandleCanvasDrop - Líneas 464-498

### ❌ Eliminar todo el contenido

### ✅ Reemplazar con:
```javascript
const handleCanvasDrop = (event) => {
  event.preventDefault();
  
  if (!draggedImage.value) return;
  
  const containerRect = editorContainer.value.getBoundingClientRect();
  const stageNode = stage.value?.getStage();
  if (!stageNode) return;
  
  const z = zoom.zoomLevel.value;
  
  // Convertir posición del mouse a píxeles del editor sin zoom
  const dropXPixels = (event.clientX - containerRect.left - 16 - RULERSIZE) / z;
  const dropYPixels = (event.clientY - containerRect.top - 16 - RULERSIZE) / z;
  
  // Convertir píxeles del editor a pulgadas
  const dropXInches = converter.editorPixelsToInches(dropXPixels);
  const dropYInches = converter.editorPixelsToInches(dropYPixels);
  
  const defaultWidthInches = 24;
  const aspectRatio = draggedImage.value.originalHeight / draggedImage.value.originalWidth;
  const defaultHeightInches = defaultWidthInches * aspectRatio;
  
  const newImage = {
    id: 'canvas-' + Date.now() + '-' + Math.random(),
    xInches: Math.max(0, Math.min(dropXInches, canvasWidthInches.value - defaultWidthInches)),
    yInches: Math.max(0, Math.min(dropYInches, canvasHeightInches.value - defaultHeightInches)),
    widthInches: defaultWidthInches,
    heightInches: defaultHeightInches,
    imageObj: draggedImage.value.imageObj,
    originalWidth: draggedImage.value.originalWidth,
    originalHeight: draggedImage.value.originalHeight,
    name: draggedImage.value.name,
    file: draggedImage.value.file,
  };
  
  images.value.push(newImage);
  draggedImage.value = null;
};
```

---

## Paso 12: HandleStageClick - Líneas 500-540

### ⚠️ Modificar solo la parte de actualización de imageWidth/imageHeight

Buscar estas líneas (aprox 524-528):
```javascript
const displayWidthInches = finalWidth / DPI_SCREEN;
const displayHeightInches = finalHeight / DPI_SCREEN;
imageWidth.value = displayWidthInches.toFixed(2);
imageHeight.value = displayHeightInches.toFixed(2);
```

### ✅ Reemplazar con:
```javascript
imageWidth.value = img.widthInches.toFixed(2);
imageHeight.value = img.heightInches.toFixed(2);
```

---

## Paso 13: HandleDragEnd - Líneas 566-589

### ❌ Eliminar todo el contenido

### ✅ Reemplazar con:
```javascript
const handleDragEnd = (e) => {
  e.evt.preventDefault();
  const node = e.target;
  const id = node.id();
  const img = images.value.find(i => i.id === id);
  
  if (!img) return;
  
  const z = zoom.zoomLevel.value;
  
  // Convertir posición de píxeles a pulgadas
  const nodeXPixels = (node.x() - RULERSIZE) / z;
  const nodeYPixels = (node.y() - RULERSIZE) / z;
  
  let xInches = converter.editorPixelsToInches(nodeXPixels);
  let yInches = converter.editorPixelsToInches(nodeYPixels);
  
  // Aplicar snap
  if (snap.snapEnabled.value) {
    const snapped = snap.applyAllSnaps(
      { ...img, xInches, yInches },
      images.value,
      canvasWidthInches.value,
      canvasHeightInches.value
    );
    xInches = snapped.xInches;
    yInches = snapped.yInches;
    
    // Actualizar posición del nodo con snap
    const snappedXPixels = converter.inchesToEditorPixels(xInches);
    const snappedYPixels = converter.inchesToEditorPixels(yInches);
    node.x((snappedXPixels * z) + RULERSIZE);
    node.y((snappedYPixels * z) + RULERSIZE);
    
    // Actualizar guías
    nextTick(() => {
      updateKonvaLayers();
    });
    
    // Limpiar guías después de 500ms
    setTimeout(() => {
      snap.clearGuides();
      updateKonvaLayers();
    }, 500);
  }
  
  // Guardar posición en pulgadas
  img.xInches = xInches;
  img.yInches = yInches;
};
```

---

## Paso 14: HandleTransformEnd - Líneas 591-628

### ❌ Reemplazar todo

### ✅ Nuevo código:
```javascript
const handleTransformEnd = (e) => {
  const transformerNode = transformer.value?.getNode();
  if (!transformerNode) return;
  
  const nodes = transformerNode.nodes();
  if (!nodes || nodes.length === 0) return;
  
  const groupNode = nodes[0];
  const id = groupNode.id();
  const img = images.value.find(i => i.id === id);
  
  if (!img) return;
  
  const scaleX = groupNode.scaleX();
  const scaleY = groupNode.scaleY();
  const z = zoom.zoomLevel.value;
  
  // Calcular nuevo tamaño en píxeles del editor (sin zoom)
  const oldWidthEditorPixels = converter.inchesToEditorPixels(img.widthInches);
  const oldHeightEditorPixels = converter.inchesToEditorPixels(img.heightInches);
  
  const newWidthEditorPixels = oldWidthEditorPixels * scaleX;
  const newHeightEditorPixels = oldHeightEditorPixels * scaleY;
  
  // Convertir a pulgadas
  img.widthInches = converter.editorPixelsToInches(newWidthEditorPixels);
  img.heightInches = converter.editorPixelsToInches(newHeightEditorPixels);
  
  // Actualizar display
  imageWidth.value = img.widthInches.toFixed(2);
  imageHeight.value = img.heightInches.toFixed(2);
  
  // Reset escala del nodo
  groupNode.scaleX(1);
  groupNode.scaleY(1);
  
  // Actualizar tamaño de la imagen en el nodo
  const imageNode = groupNode.findOne('Image');
  if (imageNode) {
    imageNode.width(newWidthEditorPixels);
    imageNode.height(newHeightEditorPixels);
  }
  
  if (imagesLayer.value) {
    imagesLayer.value.getNode().batchDraw();
  }
};
```

---

## Paso 15: UpdateImageSize - Líneas 630-650

### ❌ Reemplazar todo

### ✅ Nuevo código:
```javascript
const updateImageSize = () => {
  if (!selectedImage.value) return;
  
  // Actualizar tamaño en pulgadas directamente
  selectedImage.value.widthInches = parseFloat(imageWidth.value);
  selectedImage.value.heightInches = parseFloat(imageHeight.value);
  
  nextTick(() => {
    if (imagesLayer.value) {
      imagesLayer.value.getNode().batchDraw();
    }
    if (transformerLayer.value) {
      transformerLayer.value.getNode().batchDraw();
    }
  });
};
```

---

## Paso 16: AutoBuild - Líneas 678-727

### ❌ Reemplazar todo

### ✅ Nuevo código:
```javascript
const autoBuild = () => {
  if (uploadedImages.value.length === 0) {
    alert('Por favor sube imágenes primero');
    return;
  }

  images.value = [];
  const marginInches = 0.25;
  let currentXInches = marginInches;
  let currentYInches = marginInches;
  let rowHeightInches = 0;

  const sortedImages = [...uploadedImages.value].sort((a, b) => b.originalHeight - a.originalHeight);

  sortedImages.forEach((uploadedImage) => {
    const defaultWidthInches = 24; // 2 pies
    const aspectRatio = uploadedImage.originalHeight / uploadedImage.originalWidth;
    const defaultHeightInches = defaultWidthInches * aspectRatio;

    if (currentXInches + defaultWidthInches + marginInches > canvasWidthInches.value) {
      currentXInches = marginInches;
      currentYInches += rowHeightInches + marginInches;
      rowHeightInches = 0;
    }

    if (currentYInches + defaultHeightInches + marginInches <= canvasHeightInches.value) {
      images.value.push({
        id: 'canvas-' + Date.now() + '-' + Math.random(),
        xInches: currentXInches,
        yInches: currentYInches,
        widthInches: defaultWidthInches,
        heightInches: defaultHeightInches,
        imageObj: uploadedImage.imageObj,
        originalWidth: uploadedImage.originalWidth,
        originalHeight: uploadedImage.originalHeight,
        name: uploadedImage.name,
        file: uploadedImage.file,
      });

      currentXInches += defaultWidthInches + marginInches;
      rowHeightInches = Math.max(rowHeightInches, defaultHeightInches);
    }
  });
};
```

---

## Paso 17: DownloadGangSheet - Todo el bloque

### ❌ Eliminar función `createHighResolutionExport()` completa (líneas 729-780)

### ❌ Reemplazar `downloadGangSheet()` con:

```javascript
const generatePrintFile = async () => {
  const exportWidth = converter.inchesToExportPixels(canvasWidthInches.value);
  const exportHeight = converter.inchesToExportPixels(canvasHeightInches.value);
  
  const canvas = document.createElement('canvas');
  canvas.width = exportWidth;
  canvas.height = exportHeight;
  
  const ctx = canvas.getContext('2d', { willReadFrequently: false });
  
  ctx.fillStyle = 'white';
  ctx.fillRect(0, 0, exportWidth, exportHeight);
  
  for (const img of images.value) {
    try {
      if (!img.imageObj || !img.imageObj.complete) {
        console.warn('Imagen no cargada:', img.name);
        continue;
      }
      
      const exportX = converter.inchesToExportPixels(img.xInches);
      const exportY = converter.inchesToExportPixels(img.yInches);
      const exportImgWidth = converter.inchesToExportPixels(img.widthInches);
      const exportImgHeight = converter.inchesToExportPixels(img.heightInches);
      
      ctx.drawImage(
        img.imageObj,
        exportX,
        exportY,
        exportImgWidth,
        exportImgHeight
      );
    } catch (e) {
      console.error('Error dibujando imagen:', img.name, e);
    }
  }
  
  return canvas.toDataURL('image/png');
};

const downloadGangSheet = async () => {
  if (images.value.length === 0) {
    alert('Por favor agrega imágenes primero');
    return;
  }

  try {
    const btn = event.target.closest('button');
    const originalText = btn.textContent;
    btn.textContent = 'Generando...';
    btn.disabled = true;

    const dataURL = await generatePrintFile();
    
    const link = document.createElement('a');
    link.href = dataURL;
    
    const filename = `gang-sheet-${sheetWidthFeet.value}x${sheetHeightFeet.value}ft-300dpi.png`;
    link.download = filename;
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    btn.textContent = originalText;
    btn.disabled = false;

    alert(`✅ Descargado: ${filename}\n\nResolución: ${exportInfo.value.width} × ${exportInfo.value.height} px\nDPI: 300\nTamaño: ~${exportInfo.value.megabytes} MB\n\n¡Listo para imprimir DTF!`);
  } catch (error) {
    console.error('Error:', error);
    alert('Error al exportar: ' + error.message);
  }
};
```

---

## Paso 18: GetResolutionClass y GetResolutionMessage

### ✅ Actualizar con:

```javascript
const getResolutionClass = () => {
  if (!selectedImage.value) return 'text-gray-600';
  
  const dpi = converter.calculateImageDPI(
    selectedImage.value.originalWidth,
    selectedImage.value.originalHeight,
    selectedImage.value.widthInches,
    selectedImage.value.heightInches
  );
  
  const quality = converter.getImageQuality(dpi);
  return `text-${quality.color}-700 bg-${quality.color}-50 p-2 rounded`;
};

const getResolutionMessage = () => {
  if (!selectedImage.value) return 'Selecciona una imagen';
  
  const dpi = converter.calculateImageDPI(
    selectedImage.value.originalWidth,
    selectedImage.value.originalHeight,
    selectedImage.value.widthInches,
    selectedImage.value.heightInches
  );
  
  const quality = converter.getImageQuality(dpi);
  return `${Math.round(dpi)} DPI - ${quality.message}`;
};
```

---

## Paso 19: EstimateFileSize - Líneas 1093-1104

### ❌ Eliminar función completa

### ✅ Ya no es necesaria (usar `exportInfo.value.megabytes`)

---

## Paso 20: Watch para showGrid

### ✅ Ya existe (líneas 1128-1132), mantener

---

## Paso 21: Agregar handleWheel

### ✅ Agregar nueva función:

```javascript
const handleWheel = (e) => {
  const stageNode = stage.value?.getStage();
  if (!stageNode) return;
  
  zoom.handleWheelZoom(e, stageNode);
  
  nextTick(() => {
    updateKonvaLayers();
  });
};
```

---

## Paso 22: Return statement

### ✅ Agregar a return:

```javascript
return {
  // Composables
  converter,
  zoom,
  snap,
  
  // Constantes
  RULERSIZE,
  
  // Computed
  canvasWidthInches,
  canvasHeightInches,
  canvasWidthPixels,
  canvasHeightPixels,
  exportInfo,
  
  // ... resto del código existente
  
  // Nuevas funciones
  handleWheel,
  updateKonvaLayers,
  generatePrintFile,
};
```

---

## Paso 23: Template - Líneas 118-155

### ❌ Eliminar layers actuales

### ✅ Reemplazar con:

```vue
<v-stage
  ref="stage"
  :config="stageConfig"
  @mousedown="handleStageClick"
  @wheel="handleWheel"
>
  <!-- Background Layer con Grid -->
  <v-layer ref="gridLayer">
    <v-rect :config="{ 
      x: RULERSIZE, 
      y: RULERSIZE, 
      width: canvasWidthPixels, 
      height: canvasHeightPixels, 
      fill: 'white', 
      stroke: '#9ca3af', 
      strokeWidth: 1 
    }" />
  </v-layer>

  <!-- Rulers Layer -->
  <v-layer ref="rulersLayer"></v-layer>

  <!-- Images Layer -->
  <v-layer ref="imagesLayer">
    <v-group
      v-for="img in images"
      :key="img.id"
      :config="{
        x: (converter.inchesToEditorPixels(img.xInches) * zoom.zoomLevel.value) + RULERSIZE,
        y: (converter.inchesToEditorPixels(img.yInches) * zoom.zoomLevel.value) + RULERSIZE,
        draggable: true,
        id: img.id,
        name: 'image-group',
      }"
      @dragend="handleDragEnd"
      @transformend="handleTransformEnd"
    >
      <v-image :config="{ 
        image: img.imageObj, 
        width: converter.inchesToEditorPixels(img.widthInches),
        height: converter.inchesToEditorPixels(img.heightInches),
      }" />
    </v-group>
  </v-layer>

  <!-- Guides Layer -->
  <v-layer ref="guidesLayer"></v-layer>

  <!-- Transformer Layer -->
  <v-layer ref="transformerLayer">
    <v-transformer ref="transformer" />
  </v-layer>
</v-stage>
```

---

## Paso 24: Agregar Controles de Zoom en Template

### ✅ Agregar antes del canvas (después de línea 48):

```vue
<!-- Zoom Controls -->
<div class="flex items-center justify-between mb-4">
  <div class="zoom-controls flex items-center gap-2 bg-white p-2 rounded-lg shadow">
    <button @click="zoom.zoomOut()" class="btn-secondary text-xs px-2 py-1">
      -
    </button>
    <span class="text-sm font-mono min-w-[4rem] text-center">
      {{ zoom.zoomPercentage.value }}
    </span>
    <button @click="zoom.zoomIn()" class="btn-secondary text-xs px-2 py-1">
      +
    </button>
    <button @click="zoom.zoomTo100()" class="btn-secondary text-xs px-2 py-1">
      100%
    </button>
    <button @click="updateStageSize()" class="btn-secondary text-xs px-2 py-1">
      Fit
    </button>
  </div>
  
  <div class="text-sm text-gray-600">
    Canvas: {{ Math.round(canvasWidthPixels) }} × {{ Math.round(canvasHeightPixels) }} px (editor)
  </div>
</div>
```

---

## Paso 25: Actualizar Stats Footer - Líneas 190-210

### ✅ Reemplazar exportInfo con:

```vue
<div class="text-right text-sm">
  <div class="text-gray-500 mb-1">
    Export Resolution: {{ exportInfo.width }} × {{ exportInfo.height }} px @ 300 DPI
  </div>
  <div class="text-gray-600 text-xs">
    File size: ~{{ exportInfo.megabytes }} MB (professional DTF quality)
  </div>
</div>
```

---

## ✅ Checklist Final

- [ ] Backup creado
- [ ] Imports actualizados
- [ ] Composables importados
- [ ] Constantes eliminadas
- [ ] StageConfig refactorizado
- [ ] Refs actualizadas
- [ ] Computed properties agregados
- [ ] UpdateStageSize refactorizado
- [ ] UpdateKonvaLayers creado
- [ ] AddImageToCanvas en pulgadas
- [ ] HandleCanvasDrop en pulgadas
- [ ] HandleDragEnd con snap
- [ ] HandleTransformEnd actualizado
- [ ] UpdateImageSize simplificado
- [ ] AutoBuild en pulgadas
- [ ] GeneratePrintFile creado
- [ ] DownloadGangSheet actualizado
- [ ] GetResolution actualizado
- [ ] HandleWheel agregado
- [ ] Return actualizado
- [ ] Template actualizado
- [ ] Controles de zoom agregados
- [ ] Stats footer actualizado

---

## 🧪 Testing

1. **Compilar:**
```bash
npm run dev
```

2. **Probar:**
- ✅ Canvas carga fluido (no lag)
- ✅ Subir imágenes funciona
- ✅ Drag & drop al canvas
- ✅ Transformar imágenes (resize/rotate)
- ✅ Zoom con rueda del mouse
- ✅ Botones de zoom
- ✅ Snap al grid activo
- ✅ Auto Build posiciona imágenes
- ✅ Download genera PNG a 300 DPI
- ✅ Tamaño del archivo correcto (~X MB)

---

## 🆘 Si algo sale mal

```bash
# Restaurar backup
cp resources/js/components/GangSheetEditorFeet.vue.backup resources/js/components/GangSheetEditorFeet.vue

# Ver errores
npm run dev
# Revisar consola del navegador
```

---

## 📞 Soporte

Si encuentras errores específicos, revisa:
1. Consola del navegador (F12)
2. Terminal donde corre `npm run dev`
3. Verificar que todos los imports estén correctos
4. Verificar que todas las propiedades de `images` usen `Inches` en lugar de `px`

---

¡Éxito! 🚀
