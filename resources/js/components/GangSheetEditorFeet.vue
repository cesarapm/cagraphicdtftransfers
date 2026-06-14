<template>
  <div class="gang-sheet-editor">
    <!-- Header Controls -->
    <div class="editor-header bg-white shadow-sm border-b p-4 mb-4 rounded-lg">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Gang Sheet Builder - Feet</h2>
        <div class="flex gap-2">
          <button @click="autoBuild" class="btn-primary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Auto Build
          </button>
          <button @click="clearCanvas" class="btn-secondary">Clear All</button>
          <button @click="downloadGangSheet($event)" class="btn-success">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download PNG
          </button>
          <button @click="saveGangSheet($event)" class="btn-success">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            Save to Server
          </button>
        </div>
      </div>

      <!-- Sheet Size Selector -->
      <div class="flex items-center gap-4 mb-4">
        <label class="font-semibold text-gray-700">Sheet Size:</label>
        <select v-model="selectedSizeId" @change="changeSheetSize" class="px-4 py-2 border rounded-lg">
          <option value="">-- Select a size --</option>
          <option v-for="size in availableSizes" :key="size.id" :value="size.id">
            {{ size.name }} - ${{ parseFloat(size.price).toFixed(2) }}
          </option>
        </select>
        
        <label class="font-semibold text-gray-700 ml-4">Export Quality:</label>
        <select v-model="exportDPI" class="px-4 py-2 border rounded-lg">
          <option :value="150">150 DPI (Buena - Rápido)</option>
          <option :value="200">200 DPI (Excelente - Recomendado)</option>
          <option :value="300">300 DPI (Máxima - Lento)</option>
        </select>
        
        <div class="ml-auto flex gap-4">
          <label class="flex items-center">
            <input type="checkbox" v-model="showGrid" class="mr-2" />
            <span class="text-sm text-gray-700">Show Grid</span>
          </label>
          <label class="flex items-center">
            <input type="checkbox" v-model="snap.snapEnabled.value" class="mr-2" />
            <span class="text-sm text-gray-700">Snap to Grid</span>
          </label>
        </div>
      </div>
      
      <!-- Zoom Controls -->
      <div class="flex items-center justify-between mb-4">
        <div class="zoom-controls flex items-center gap-2 bg-white p-2 rounded-lg shadow">
          <button @click="handleZoomOut" class="btn-secondary text-xs px-2 py-1">
            -
          </button>
          <span class="text-sm font-mono min-w-[4rem] text-center">
            {{ zoom.zoomPercentage.value }}
          </span>
          <button @click="handleZoomIn" class="btn-secondary text-xs px-2 py-1">
            +
          </button>
          <button @click="handleZoom100" class="btn-secondary text-xs px-2 py-1" title="Reset zoom to 100%">
            100%
          </button>
          <button @click="fitToScreen" class="btn-primary text-xs px-2 py-1 font-semibold" title="Ajustar al tamaño de pantalla">
            🎯 Fit
          </button>
        </div>
        
        <div class="text-sm text-gray-600">
          Canvas: {{ Math.round(canvasWidthPixels) }} × {{ Math.round(canvasHeightPixels) }} px (editor)
        </div>
      </div>

      <!-- Upload Area -->
      <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors cursor-pointer"
           @dragover.prevent
           @drop.prevent="handleDrop"
           @click="$refs.fileInput.click()">
        <input ref="fileInput" type="file" multiple accept="image/*" @change="handleFileUpload" class="hidden" />
        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p class="text-gray-600 mb-1">Drag & drop images or click to upload</p>
        <p class="text-sm text-gray-400">PNG, JPG, JPEG, SVG (min 300×300px recommended)</p>
      </div>

      <!-- Image Settings -->
      <div v-if="selectedImage" class="mt-4 p-4 bg-gray-50 rounded-lg">
        <h3 class="font-semibold mb-3">Selected Image Settings</h3>
        
        <!-- Resolution Info -->
        <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded">
          <p class="text-xs text-gray-600 mb-1">📊 <strong>Resolución de la imagen:</strong></p>
          <p class="text-xs text-gray-700">Tamaño actual: {{ selectedImage.width }} × {{ selectedImage.height }} px (72 DPI pantalla)</p>
          <p class="text-xs text-gray-700">Tamaño en pulgadas: {{ (imageWidth * 1).toFixed(1) }} × {{ (imageHeight * 1).toFixed(1) }} in</p>
          <p class="text-xs font-semibold" :class="getResolutionClass()">
            {{ getResolutionMessage() }}
          </p>
        </div>
        
        <div class="grid grid-cols-4 gap-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Width (in)</label>
            <input v-model.number="imageWidth" @input="updateImageSize" type="number" step="0.1" class="w-full px-3 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Height (in)</label>
            <input v-model.number="imageHeight" @input="updateImageSize" type="number" step="0.1" class="w-full px-3 py-2 border rounded-lg" />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Quantity</label>
            <input v-model.number="imageQuantity" @change="duplicateImage" type="number" min="1" class="w-full px-3 py-2 border rounded-lg" />
          </div>
          <div class="flex items-end">
            <button @click="deleteSelected" class="btn-danger w-full">Delete</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Editor Canvas -->
    <div class="editor-container bg-gray-100 p-4 rounded-lg shadow-inner" 
         ref="editorContainer"
         @dragover.prevent="handleCanvasDragOver"
         @drop.prevent="handleCanvasDrop">
      <v-stage
        ref="stage"
        :config="stageConfig"
        @mousedown="handleStageClick"
        @touchstart="handleStageClick"
      >
        <!-- Background Layer con Grid optimizado -->
        <v-layer ref="gridLayer">
          <!-- El rectángulo blanco y el grid se agregan programáticamente en updateKonvaLayers -->
        </v-layer>

        <!-- Rulers Layer con Rulers optimizados -->
        <v-layer ref="rulersLayer">
          <!-- Ruler shapes se agregan programáticamente en updateKonvaLayers -->
        </v-layer>

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
          >
            <v-image :config="{ 
              image: img.imageObj, 
              width: converter.inchesToEditorPixels(img.widthInches) * zoom.zoomLevel.value,
              height: converter.inchesToEditorPixels(img.heightInches) * zoom.zoomLevel.value,
            }" />
          </v-group>
        </v-layer>
        
        <!-- Guides Layer -->
        <v-layer ref="guidesLayer">
          <!-- Guide shapes se agregan programáticamente en updateKonvaLayers -->
        </v-layer>

        <!-- Transformer Layer -->
        <v-layer ref="transformerLayer">
          <v-transformer 
            ref="transformer"
            :config="{
              rotateEnabled: true,
              keepRatio: true,
              enabledAnchors: ['top-left', 'top-right', 'bottom-left', 'bottom-right'],
              borderStroke: '#0066ff',
              borderStrokeWidth: 2,
              anchorStroke: '#0066ff',
              anchorFill: 'white',
              anchorSize: 12,
              anchorCornerRadius: 6,
              borderDash: [3, 3]
            }"
            @transformend="handleTransformEnd"
          />
        </v-layer>
      </v-stage>
    </div>

    <!-- Uploaded Images List -->
    <div v-if="uploadedImages.length > 0" class="mt-4 bg-white rounded-lg shadow-sm p-4">
      <h3 class="font-semibold mb-3 text-gray-800">Uploaded Images ({{ uploadedImages.length }})</h3>
      <div class="grid grid-cols-6 gap-3">
        <div v-for="img in uploadedImages" :key="img.id" 
             class="border rounded-lg p-2 hover:shadow-md transition-shadow cursor-move"
             draggable="true"
             @dragstart="handleImageDragStart($event, img)"
             @click="addImageToCanvas(img)">
          <img :src="img.src" :alt="img.name" class="w-full h-20 object-contain mb-1" />
          <p class="text-xs text-gray-600 truncate">{{ img.name }}</p>
          <p class="text-xs text-gray-500">{{ (img.size / 1024).toFixed(1) }} KB</p>
        </div>
      </div>
    </div>

    <!-- Stats Footer -->
    <div class="stats-footer mt-4 bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
      <div class="flex gap-6">
        <div>
          <span class="text-sm text-gray-600">Sheet Size:</span>
          <span class="font-semibold ml-2">{{ sheetWidth }}' × {{ sheetHeight }}'</span>
        </div>
        <div>
          <span class="text-sm text-gray-600">Images on Sheet:</span>
          <span class="font-semibold ml-2">{{ images.length }}</span>
        </div>
        <div>
          <span class="text-sm text-gray-600">Coverage:</span>
          <span class="font-semibold ml-2">{{ coveragePercentage }}%</span>
        </div>
      </div>
      <div class="text-right text-sm">
        <div class="text-gray-500 mb-1">
          Export Resolution: {{ Math.round(canvasWidthInches * exportDPI) }} × {{ Math.round(canvasHeightInches * exportDPI) }} px @ {{ exportDPI }} DPI
        </div>
        <div class="text-gray-600 text-xs">
          File size: ~{{ ((canvasWidthInches * exportDPI * canvasHeightInches * exportDPI * 4) / 1024 / 1024).toFixed(1) }} MB ({{ exportDPI === 300 ? 'máxima' : exportDPI === 200 ? 'excelente' : 'buena' }} calidad)
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, nextTick, watch } from 'vue';
import { useUnitConverter } from '@/composables/useUnitConverter';
import { useZoomManager } from '@/composables/useZoomManager';
import { useSnapManager } from '@/composables/useSnapManager';
import { createGrid } from './konva/OptimizedGrid';
import { createRuler } from './konva/OptimizedRuler';
import { createGuideLayer } from './konva/GuideLayer';

export default {
  name: 'GangSheetEditorFeet',
  setup() {
    // Composables
    const converter = useUnitConverter();
    const zoom = useZoomManager();
    const snap = useSnapManager();
    
    // Constantes del editor
    const RULERSIZE = 30;

    // Estado del canvas
    const availableSizes = ref([]);
    const selectedSizeId = ref('');
    const sheetWidth = ref(22);
    const sheetHeight = ref(10);
    const showGrid = ref(true);
    const exportDPI = ref(200); // Default: 200 DPI (excelente calidad)
    
    // Computed: tamaño en pulgadas
    const canvasWidthInches = computed(() => 
      converter.feetToInches(sheetWidth.value)
    );
    const canvasHeightInches = computed(() => 
      converter.feetToInches(sheetHeight.value)
    );
    
    // Computed: tamaño en píxeles del EDITOR
    const canvasWidthPixels = computed(() => 
      converter.inchesToEditorPixels(canvasWidthInches.value)
    );
    const canvasHeightPixels = computed(() => 
      converter.inchesToEditorPixels(canvasHeightInches.value)
    );
    
    // Computed: config del rectángulo blanco con zoom aplicado
    const canvasRectConfig = computed(() => ({
      x: RULERSIZE,
      y: RULERSIZE,
      width: canvasWidthPixels.value * zoom.zoomLevel.value,
      height: canvasHeightPixels.value * zoom.zoomLevel.value,
      fill: 'white',
      stroke: '#9ca3af',
      strokeWidth: 1,
    }));
    
    // Stage config dinámico
    const stageConfig = reactive({
      width: 0,
      height: 0,
      scaleX: 1,
      scaleY: 1,
      draggable: false,
    });

    const uploadedImages = ref([]);
    const images = ref([]);
    const selectedImage = ref(null);
    const imageWidth = ref(0);
    const imageHeight = ref(0);
    const imageQuantity = ref(1);
    const draggedImage = ref(null);
    const stage = ref(null);
    const transformer = ref(null);
    const editorContainer = ref(null);
    const imagesLayer = ref(null);
    const transformerLayer = ref(null);
    const rulersLayer = ref(null);
    const gridLayer = ref(null);
    const guidesLayer = ref(null);
    
    // Refs para shapes optimizados
    const canvasRectShape = ref(null);
    const gridShape = ref(null);
    const horizontalRulerShape = ref(null);
    const verticalRulerShape = ref(null);
    const guidesShape = ref(null);

      // Convert uploaded image ID to string for consistency
      const ensureStringId = (id) => typeof id === 'string' ? id : 'img-' + String(id);

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

    const loadAvailableSizes = async () => {
      try {
        const response = await fetch('/api/sheet-sizes/feet');
        const data = await response.json();
        availableSizes.value = data;
      } catch (error) {
        console.error('Error loading sheet sizes:', error);
      }
    };

    const changeSheetSize = () => {
      const selected = availableSizes.value.find(s => s.id == selectedSizeId.value);
      if (selected) {
        sheetWidth.value = parseFloat(selected.width);
        sheetHeight.value = parseFloat(selected.height);
        
        console.log('📐 Cambiando tamaño:', sheetWidth.value, '×', sheetHeight.value, 'feet');
        
        // Limpiar imágenes del canvas al cambiar tamaño
        images.value = [];
        selectedImage.value = null;
        if (transformer.value) {
          const transformerNode = transformer.value.getNode();
          transformerNode.nodes([]);
        }
        
        // Doble nextTick para asegurar que los computed se actualicen
        nextTick(() => {
          nextTick(() => {
            console.log('📊 Canvas pixels:', canvasWidthPixels.value, '×', canvasHeightPixels.value);
            updateStageSize();
          });
        });
      }
    };

    const updateStageSize = () => {
      if (!editorContainer.value) {
        console.warn('⚠️ editorContainer no disponible');
        return;
      }
      
      const containerWidth = editorContainer.value.clientWidth - 32;
      const containerHeight = editorContainer.value.clientHeight || 600;
      
      console.log('📦 Container:', containerWidth, '×', containerHeight);
      console.log('🎨 Canvas (pixels):', canvasWidthPixels.value, '×', canvasHeightPixels.value);
      
      // Calcular zoom to fit
      zoom.zoomToFit(
        canvasWidthPixels.value,
        canvasHeightPixels.value,
        containerWidth,
        containerHeight,
        RULERSIZE
      );
      
      const z = zoom.zoomLevel.value;
      console.log('🔍 Zoom calculado:', z, '=', zoom.zoomPercentage.value);
      
      // Actualizar stage config SIN aplicar scale (el zoom se aplica en los cálculos)
      stageConfig.width = (canvasWidthPixels.value * z) + RULERSIZE;
      stageConfig.height = (canvasHeightPixels.value * z) + RULERSIZE;
      stageConfig.scaleX = 1; // NO aplicar zoom aquí
      stageConfig.scaleY = 1; // NO aplicar zoom aquí
      
      console.log('📏 Stage final:', stageConfig.width, '×', stageConfig.height);
      
      nextTick(() => {
        updateKonvaLayers();
      });
    };
    
    /**
     * Actualizar todos los layers optimizados (grid, rulers, guides)
     */
    const updateKonvaLayers = () => {
      const z = zoom.zoomLevel.value;
      const gridDetail = zoom.getGridDetail.value;
      const rulerDetail = zoom.getRulerDetail.value;
      
      console.log('🎨 Actualizando layers con zoom:', z);
      
      // Grid Layer - Rectángulo blanco del canvas
      if (gridLayer.value) {
        const gridLayerNode = gridLayer.value.getNode();
        
        // Destruir rectángulo anterior
        if (canvasRectShape.value) {
          canvasRectShape.value.destroy();
        }
        
        // Crear nuevo rectángulo blanco con tamaño correcto
        const Konva = window.Konva;
        canvasRectShape.value = new Konva.Rect({
          x: RULERSIZE,
          y: RULERSIZE,
          width: canvasWidthPixels.value * z,
          height: canvasHeightPixels.value * z,
          fill: 'white',
          stroke: '#9ca3af',
          strokeWidth: 1,
        });
        
        gridLayerNode.add(canvasRectShape.value);
        console.log('✅ Canvas rect:', canvasWidthPixels.value * z, '×', canvasHeightPixels.value * z);
        
        // Grid (opcional)
        if (showGrid.value) {
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
        }
        
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
    
    /**
     * Ajustar canvas al tamaño de la pantalla (Fit button)
     */
    const fitToScreen = () => {
      updateStageSize();
    };
    
    /**
     * Zoom In - Acercar
     */
    const handleZoomIn = () => {
      zoom.zoomIn();
      nextTick(() => {
        const z = zoom.zoomLevel.value;
        stageConfig.width = (canvasWidthPixels.value * z) + RULERSIZE;
        stageConfig.height = (canvasHeightPixels.value * z) + RULERSIZE;
        updateKonvaLayers();
      });
    };
    
    /**
     * Zoom Out - Alejar
     */
    const handleZoomOut = () => {
      zoom.zoomOut();
      nextTick(() => {
        const z = zoom.zoomLevel.value;
        stageConfig.width = (canvasWidthPixels.value * z) + RULERSIZE;
        stageConfig.height = (canvasHeightPixels.value * z) + RULERSIZE;
        updateKonvaLayers();
      });
    };
    
    /**
     * Zoom 100% - Reset
     */
    const handleZoom100 = () => {
      zoom.zoomTo100();
      nextTick(() => {
        const z = zoom.zoomLevel.value;
        stageConfig.width = (canvasWidthPixels.value * z) + RULERSIZE;
        stageConfig.height = (canvasHeightPixels.value * z) + RULERSIZE;
        updateKonvaLayers();
      });
    };

    const handleFileUpload = (event) => {
      const files = event.target.files;
      processFiles(files);
    };

    const handleDrop = (event) => {
      const files = event.dataTransfer.files;
      processFiles(files);
    };

    const processFiles = (files) => {
      Array.from(files).forEach((file) => {
        if (!file.type.match('image.*')) return;

        // Usar Object URL en lugar de DataURL para mejor rendimiento
        const objectURL = URL.createObjectURL(file);
        const img = new Image();
        // NO usar crossOrigin con ObjectURL (blob:) - solo para URLs externas
        img.onload = () => {
          const imageData = {
            id: 'img-' + Date.now() + '-' + Math.random(),
            name: file.name,
            src: objectURL,
            size: file.size,
            originalWidth: img.width,
            originalHeight: img.height,
            imageObj: img,
            file: file, // Guardar el archivo para después
          };
          uploadedImages.value.push(imageData);
        };
        img.onerror = () => {
          console.error('Error cargando imagen:', file.name);
          URL.revokeObjectURL(objectURL); // Limpiar si hay error
        };
        img.src = objectURL;
      });
    };

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

    const handleImageDragStart = (event, img) => {
      draggedImage.value = img;
      if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = 'copy';
        event.dataTransfer.setData('text/plain', img.id);
      }
    };

    const handleCanvasDragOver = (event) => {
      event.preventDefault();
      if (event.dataTransfer) {
        event.dataTransfer.dropEffect = 'copy';
      }
    };

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

    const handleStageClick = (e) => {
      const clickedNode = e.target;
      
      if (clickedNode.getClassName() === 'Transformer' || 
          clickedNode.hasName('_anchor') ||
          clickedNode.hasName('back')) {
        return;
      }
      
      let targetGroup = clickedNode;
      while (targetGroup && targetGroup.getClassName() !== 'Group' && targetGroup !== targetGroup.getStage()) {
        targetGroup = targetGroup.getParent();
      }
      
      if (targetGroup && targetGroup.getClassName() === 'Group' && targetGroup.name() === 'image-group') {
        const id = targetGroup.id();
        const img = images.value.find(i => i.id === id);
        
        if (img) {
          selectedImage.value = img;
          imageWidth.value = img.widthInches.toFixed(2);
          imageHeight.value = img.heightInches.toFixed(2);
          
          nextTick(() => {
            if (transformer.value) {
              const transformerNode = transformer.value.getNode();
              transformerNode.nodes([targetGroup]);
              if (imagesLayer.value) {
                imagesLayer.value.getNode().batchDraw();
              }
              if (transformerLayer.value) {
                transformerLayer.value.getNode().batchDraw();
              }
            }
          });
        }
      } else {
        selectedImage.value = null;
        if (transformer.value) {
          const transformerNode = transformer.value.getNode();
          transformerNode.nodes([]);
          if (transformerLayer.value) {
            transformerLayer.value.getNode().batchDraw();
          }
        }
      }
    };

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
      
      // Obtener el nodo de imagen
      const imageNode = groupNode.findOne('Image');
      if (!imageNode) return;
      
      // Calcular nuevo tamaño (las dimensiones actuales YA tienen zoom aplicado)
      const currentWidthWithZoom = imageNode.width();
      const currentHeightWithZoom = imageNode.height();
      
      // Remover el zoom para obtener dimensiones del editor
      const currentWidthEditorPixels = currentWidthWithZoom / z;
      const currentHeightEditorPixels = currentHeightWithZoom / z;
      
      // Aplicar escala del transformer
      const newWidthEditorPixels = currentWidthEditorPixels * scaleX;
      const newHeightEditorPixels = currentHeightEditorPixels * scaleY;
      
      // Convertir a pulgadas
      img.widthInches = converter.editorPixelsToInches(newWidthEditorPixels);
      img.heightInches = converter.editorPixelsToInches(newHeightEditorPixels);
      
      // Actualizar display (mantener como número, no string)
      if (selectedImage.value && selectedImage.value.id === id) {
        imageWidth.value = img.widthInches;
        imageHeight.value = img.heightInches;
      }
      
      // Reset escala del nodo
      groupNode.scaleX(1);
      groupNode.scaleY(1);
      
      // Actualizar tamaño de la imagen en el nodo (con zoom)
      imageNode.width(converter.inchesToEditorPixels(img.widthInches) * z);
      imageNode.height(converter.inchesToEditorPixels(img.heightInches) * z);
      
      if (imagesLayer.value) {
        imagesLayer.value.getNode().batchDraw();
      }
    };

    const updateImageSize = () => {
      if (!selectedImage.value) return;
      
      // Actualizar tamaño en pulgadas directamente
      selectedImage.value.widthInches = parseFloat(imageWidth.value);
      selectedImage.value.heightInches = parseFloat(imageHeight.value);
      
      const z = zoom.zoomLevel.value;
      
      nextTick(() => {
        const groupNode = stage.value?.getStage().findOne('#' + selectedImage.value.id);
        if (groupNode) {
          const imageNode = groupNode.findOne('Image');
          if (imageNode) {
            imageNode.width(converter.inchesToEditorPixels(imageWidth.value) * z);
            imageNode.height(converter.inchesToEditorPixels(imageHeight.value) * z);
          }
        }
        
        if (imagesLayer.value) {
          imagesLayer.value.getNode().batchDraw();
        }
        if (transformerLayer.value) {
          transformerLayer.value.getNode().batchDraw();
        }
      });
    };

    const deleteSelected = () => {
      if (!selectedImage.value) return;
      const index = images.value.findIndex(i => i.id === selectedImage.value.id);
      if (index > -1) {
        images.value.splice(index, 1);
      }
      selectedImage.value = null;
    };

    const duplicateImage = () => {
      if (!selectedImage.value || imageQuantity.value < 1) return;

      const currentIndex = images.value.findIndex(i => i.id === selectedImage.value.id);
      if (currentIndex === -1) return;

      const original = selectedImage.value;
      const currentCount = images.value.filter(i => i.widthInches === original.widthInches && i.heightInches === original.heightInches && i.name === original.name).length;
      const targetCount = imageQuantity.value;
      const copiesToAdd = Math.max(0, targetCount - currentCount);

      // Generate copies with offset position (en pulgadas)
      for (let i = 0; i < copiesToAdd; i++) {
        const newImage = {
          id: 'canvas-' + Date.now() + '-' + Math.random(),
          imageObj: original.imageObj,
          xInches: original.xInches + (i + 1) * 2, // Offset en pulgadas
          yInches: original.yInches + (i + 1) * 1.5,
          widthInches: original.widthInches,
          heightInches: original.heightInches,
          originalWidth: original.originalWidth,
          originalHeight: original.originalHeight,
          name: original.name,
          file: original.file,
        };
        images.value.push(newImage);
      }
    };

    const clearCanvas = () => {
      if (confirm('Are you sure you want to clear all images from the canvas?')) {
        images.value = [];
        selectedImage.value = null;
      }
    };

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

    const saveGangSheet = async (event) => {
      if (images.value.length === 0) {
        alert('Please add images to the canvas first');
        return;
      }

      let btn = null;
      let originalText = '';

      try {
        // Mostrar estado de carga
        if (event?.target) {
          btn = event.target.closest('button');
          if (btn) {
            originalText = btn.textContent;
            btn.textContent = 'Saving to Server...';
            btn.disabled = true;
          }
        }

        console.log('💾 Preparando datos para guardar...');
        
        const formData = new FormData();
        
        // Datos del sheet (nombres correctos que espera el backend)
        formData.append('width', sheetWidth.value);
        formData.append('height', sheetHeight.value);
        formData.append('unit', 'feet');
        formData.append('name', `Gang Sheet ${sheetWidth.value}x${sheetHeight.value}ft`);
        
        // Preparar metadata de imágenes (sin archivos)
        const imagesMetadata = images.value.map((img, index) => ({
          index: index,
          x: img.xInches,
          y: img.yInches,
          width: img.widthInches,
          height: img.heightInches,
          name: img.name,
          originalWidth: img.originalWidth,
          originalHeight: img.originalHeight,
        }));
        
        // Enviar metadata como JSON string
        formData.append('images', JSON.stringify(imagesMetadata));
        
        console.log('📊 Metadata:', imagesMetadata);
        
        // Agregar archivos de imagen (formato correcto: image_files[])
        let filesAdded = 0;
        images.value.forEach((img, index) => {
          if (img.file) {
            formData.append(`image_files[${index}]`, img.file);
            filesAdded++;
            console.log(`✓ Archivo agregado [${index}]:`, img.name);
          }
        });
        
        console.log(`📁 Total archivos agregados: ${filesAdded}`);
        
        if (filesAdded === 0) {
          throw new Error('No se encontraron archivos de imagen para guardar');
        }

        console.log('🚀 Enviando a /api/gang-sheets/save...');

        // Guardar diseño en backend
        const saveResponse = await fetch('/api/gang-sheets/save', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
          },
          body: formData,
        });

        console.log('📡 Response status:', saveResponse.status);
        
        if (!saveResponse.ok) {
          const text = await saveResponse.text();
          console.error('❌ Response error:', text);
          throw new Error(`Error del servidor (${saveResponse.status}): ${text.substring(0, 100)}`);
        }

        const result = await saveResponse.json();
        console.log('✅ Response data:', result);
        
        const gangSheetId = result.data.id;

        // Ahora mostrar modal de pago
        if (btn) {
          btn.textContent = originalText;
          btn.disabled = false;
        }

        // Aquí irá la integración con tu sistema de pago (Stripe, etc)
        alert(`✅ Design saved!\n\nGang Sheet ID: ${gangSheetId}\n\nNow you need to process payment to generate final image.\n\nNext step: Payment Processing`);
        
        console.log('Design saved with ID:', gangSheetId);
        
        // TODO: Redirigir a page de pago pasando gang_sheet_id
        // window.location.href = `/checkout?gang_sheet_id=${gangSheetId}`;
        
      } catch (error) {
        console.error('❌ Error:', error);
        alert('Error saving design: ' + error.message);
        if (btn) {
          btn.textContent = originalText || 'Save to Server';
          btn.disabled = false;
        }
      }
    };

    const generatePrintFile = async () => {
      console.log('🖼️ === INICIO EXPORTACIÓN ===');
      console.log('📊 Total imágenes en canvas:', images.value.length);
      
      if (images.value.length === 0) {
        throw new Error('No hay imágenes en el canvas. Agrega imágenes primero.');
      }
      
      // Usar DPI seleccionado por el usuario (150, 200 o 300)
      let EXPORT_DPI = exportDPI.value;
      let exportWidth = Math.round(canvasWidthInches.value * EXPORT_DPI);
      let exportHeight = Math.round(canvasHeightInches.value * EXPORT_DPI);
      
      // LÍMITE CRÍTICO: Chrome soporta máximo ~268 megapixels
      const MAX_MEGAPIXELS = 250; // Límite seguro con margen
      let currentMegapixels = (exportWidth * exportHeight) / 1000000;
      
      console.log('📏 Tamaño inicial:', exportWidth, 'x', exportHeight, 'px');
      console.log('📐 DPI solicitado:', EXPORT_DPI);
      console.log('💾 Megapixels:', currentMegapixels.toFixed(1), 'MP');
      
      // Si excede el límite, reducir DPI automáticamente
      if (currentMegapixels > MAX_MEGAPIXELS) {
        const scaleFactor = Math.sqrt(MAX_MEGAPIXELS / currentMegapixels);
        EXPORT_DPI = Math.floor(EXPORT_DPI * scaleFactor);
        exportWidth = Math.round(canvasWidthInches.value * EXPORT_DPI);
        exportHeight = Math.round(canvasHeightInches.value * EXPORT_DPI);
        currentMegapixels = (exportWidth * exportHeight) / 1000000;
        
        console.warn('⚠️ Canvas demasiado grande, reduciendo DPI automáticamente');
        console.log('📐 DPI ajustado:', EXPORT_DPI);
        console.log('📏 Tamaño ajustado:', exportWidth, 'x', exportHeight, 'px');
        console.log('💾 Megapixels ajustados:', currentMegapixels.toFixed(1), 'MP');
      }
      
      console.log('💾 Memoria estimada:', Math.round((exportWidth * exportHeight * 4) / 1024 / 1024), 'MB');
      
      // Validar que el tamaño no sea excesivo
      const maxPixels = 50000 * 50000; // 2.5 gigapixels max
      if (exportWidth * exportHeight > maxPixels) {
        throw new Error(`Canvas demasiado grande: ${exportWidth}x${exportHeight}px. Reduce el tamaño del sheet.`);
      }
      
      const canvas = document.createElement('canvas');
      
      try {
        canvas.width = exportWidth;
        canvas.height = exportHeight;
      } catch (e) {
        throw new Error(`No se pudo crear canvas de ${exportWidth}x${exportHeight}px. El navegador no tiene suficiente memoria.`);
      }
      
      const ctx = canvas.getContext('2d', { willReadFrequently: false });
      
      if (!ctx) {
        throw new Error('No se pudo obtener contexto 2D del canvas');
      }
      
      // Fondo blanco
      ctx.fillStyle = 'white';
      ctx.fillRect(0, 0, exportWidth, exportHeight);
      
      let imagesDrawn = 0;
      let imagesSkipped = 0;
      
      for (let idx = 0; idx < images.value.length; idx++) {
        const img = images.value[idx];
        console.log(`\n📷 Imagen ${idx + 1}/${images.value.length}:`, img.name);
        
        try {
          if (!img.imageObj) {
            console.warn('  ⚠️ imageObj es null/undefined');
            imagesSkipped++;
            continue;
          }
          
          console.log('  ✓ imageObj existe');
          console.log('  - complete:', img.imageObj.complete);
          console.log('  - width:', img.imageObj.width);
          console.log('  - height:', img.imageObj.height);
          console.log('  - src:', img.imageObj.src?.substring(0, 50) + '...');
          
          if (!img.imageObj.complete) {
            console.warn('  ⚠️ Imagen no completamente cargada');
            imagesSkipped++;
            continue;
          }
          
          const exportX = Math.round(img.xInches * EXPORT_DPI);
          const exportY = Math.round(img.yInches * EXPORT_DPI);
          const exportImgWidth = Math.round(img.widthInches * EXPORT_DPI);
          const exportImgHeight = Math.round(img.heightInches * EXPORT_DPI);
          
          console.log(`  📍 Posición: (${exportX}, ${exportY})`);
          console.log(`  📏 Tamaño: ${exportImgWidth} × ${exportImgHeight}`);
          
          if (exportImgWidth > 0 && exportImgHeight > 0) {
            ctx.drawImage(
              img.imageObj,
              exportX,
              exportY,
              exportImgWidth,
              exportImgHeight
            );
            console.log('  ✅ Dibujada exitosamente');
            imagesDrawn++;
          } else {
            console.warn('  ⚠️ Dimensiones inválidas');
            imagesSkipped++;
          }
        } catch (e) {
          console.error('  ❌ Error dibujando:', e);
          imagesSkipped++;
        }
      }
      
      console.log('\n📊 RESUMEN:');
      console.log('  ✅ Imágenes dibujadas:', imagesDrawn);
      console.log('  ⚠️ Imágenes omitidas:', imagesSkipped);
      console.log('  📊 Total:', images.value.length);
      
      if (imagesDrawn === 0) {
        throw new Error('No se pudo dibujar ninguna imagen. Verifica la consola para más detalles.');
      }
      
      console.log('🎨 Convirtiendo canvas a dataURL...');
      console.log('📏 Canvas final:', canvas.width, 'x', canvas.height, 'px');
      console.log('🧪 Obteniendo pixel data para verificar...');
      
      // Verificar que el canvas tiene datos
      try {
        const imageData = ctx.getImageData(0, 0, Math.min(10, canvas.width), Math.min(10, canvas.height));
        console.log('✓ Canvas tiene datos, primeros pixeles:', imageData.data.slice(0, 12));
      } catch (e) {
        console.error('❌ No se puede leer datos del canvas:', e.message);
      }
      
      console.log('🔄 Llamando toDataURL...');
      let dataURL;
      try {
        // PNG sin compresión = máxima calidad (sin pérdida) - ideal para DTF
        dataURL = canvas.toDataURL('image/png');
        console.log('✅ toDataURL exitoso');
        console.log('📦 DataURL length:', dataURL.length);
        console.log('📦 DataURL primeros chars:', dataURL.substring(0, 50));
        console.log('✅ DataURL generado, tamaño:', Math.round(dataURL.length / 1024), 'KB');
      } catch (e) {
        console.error('❌ Error en toDataURL:', e.message);
        throw new Error('No se pudo convertir canvas a imagen: ' + e.message);
      }
      
      if (!dataURL || dataURL.length < 100) {
        console.error('❌ dataURL vacío o muy pequeño');
        throw new Error('Canvas generó dataURL vacío');
      }
      
      return dataURL;
    };

    const downloadGangSheet = async (event) => {
      if (images.value.length === 0) {
        alert('Por favor agrega imágenes primero');
        return;
      }

      let btn = null;
      let originalText = '';
      
      try {
        if (event?.target) {
          btn = event.target.closest('button');
          if (btn) {
            originalText = btn.textContent;
            btn.textContent = 'Generando...';
            btn.disabled = true;
          }
        }

        console.log('🚀 Iniciando descarga...');
        const dataURL = await generatePrintFile();
        
        if (!dataURL || dataURL === 'data:,') {
          throw new Error('Canvas vacío - no se generó ninguna imagen');
        }
        
        console.log('📦 DataURL generado, tamaño:', Math.round(dataURL.length / 1024), 'KB');
        
        // Convertir dataURL a Blob para mejor manejo
        const blob = await (await fetch(dataURL)).blob();
        const url = URL.createObjectURL(blob);
        
        console.log('💾 Blob creado, tamaño:', Math.round(blob.size / 1024), 'KB');
        
        const link = document.createElement('a');
        link.href = url;
        
        const filename = `gang-sheet-${sheetWidth.value}x${sheetHeight.value}ft-${exportDPI.value}dpi.png`;
        link.download = filename;
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Limpiar ObjectURL
        setTimeout(() => URL.revokeObjectURL(url), 100);
        
        if (btn) {
          btn.textContent = originalText;
          btn.disabled = false;
        }

        const fileSizeMB = (blob.size / 1024 / 1024).toFixed(2);
        const qualityMsg = exportDPI.value === 300 ? 'Máxima calidad' : exportDPI.value === 200 ? 'Excelente calidad' : 'Buena calidad';
        alert(`✅ Descargado: ${filename}\n\nTamaño: ${fileSizeMB} MB\nDPI: ${exportDPI.value} (${qualityMsg})\n\n¡Listo para imprimir DTF!`);
      } catch (error) {
        console.error('❌ Error completo:', error);
        alert('Error al exportar: ' + error.message);
        if (btn) {
          btn.textContent = originalText || 'Download PNG';
          btn.disabled = false;
        }
      }
    };

    const handleWheel = (e) => {
      e.evt.preventDefault();
      
      // Obtener dirección del zoom
      const direction = e.evt.deltaY > 0 ? -1 : 1;
      
      if (direction > 0) {
        zoom.zoomIn();
      } else {
        zoom.zoomOut();
      }
      
      // Recalcular stage completo
      nextTick(() => {
        const z = zoom.zoomLevel.value;
        stageConfig.width = (canvasWidthPixels.value * z) + RULERSIZE;
        stageConfig.height = (canvasHeightPixels.value * z) + RULERSIZE;
        
        updateKonvaLayers();
      });
    };

    onMounted(() => {
      loadAvailableSizes();
      updateStageSize();
      window.addEventListener('resize', updateStageSize);
    });
    
    // Watch para actualizar grid cuando cambie showGrid
    watch(showGrid, () => {
      nextTick(() => {
        updateKonvaLayers();
      });
    });

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
      stageConfig,
      availableSizes,
      selectedSizeId,
      sheetWidth,
      sheetHeight,
      showGrid,
      uploadedImages,
      images,
      selectedImage,
      imageWidth,
      imageHeight,
      imageQuantity,
      stage,
      transformer,
      editorContainer,
      imagesLayer,
      transformerLayer,
      rulersLayer,
      gridLayer,
      coveragePercentage,
      changeSheetSize,
      handleFileUpload,
      handleDrop,
      addImageToCanvas,
      handleImageDragStart,
      handleCanvasDragOver,
      handleCanvasDrop,
      handleStageClick,
      handleDragEnd,
      handleTransformEnd,
      updateImageSize,
      deleteSelected,
      duplicateImage,
      clearCanvas,
      autoBuild,
      saveGangSheet,
      downloadGangSheet,
      generatePrintFile,
      handleWheel,
      updateKonvaLayers,
      getResolutionClass,
      getResolutionMessage,
      // Zoom functions
      handleZoomIn,
      handleZoomOut,
      handleZoom100,
      fitToScreen,
    };
  },
};
</script>

<style scoped>
.gang-sheet-editor {
  max-width: 100%;
  margin: 0 auto;
}

.editor-container {
  overflow: auto;
  max-height: 80vh;
  max-width: 100%;
  position: relative;
  /* Scroll horizontal y vertical */
  overflow-x: auto;
  overflow-y: auto;
}

.editor-container canvas {
  display: block;
}

.btn-primary {
  padding: 0.5rem 1rem;
  background-color: #2563eb;
  color: white;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: #1d4ed8;
}

.btn-secondary {
  padding: 0.5rem 1rem;
  background-color: #4b5563;
  color: white;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-secondary:hover {
  background-color: #374151;
}

.btn-success {
  padding: 0.5rem 1rem;
  background-color: #16a34a;
  color: white;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-success:hover {
  background-color: #15803d;
}

.btn-danger {
  padding: 0.5rem 1rem;
  background-color: #dc2626;
  color: white;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn-danger:hover {
  background-color: #b91c1c;
}

.upload-area {
  transition: all 0.3s ease;
}

.upload-area:hover {
  background-color: #f8fafc;
}

.cursor-move {
  cursor: move;
}

.cursor-move:active {
  cursor: grabbing;
  opacity: 0.7;
}
</style>
