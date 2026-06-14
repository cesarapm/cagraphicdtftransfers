<template>
  <div class="gang-sheet-editor">
    <!-- Header Controls -->
    <div class="editor-header bg-white shadow-sm border-b p-4 mb-4 rounded-lg">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Gang Sheet Builder</h2>
        <div class="flex gap-2">
          <button @click="autoBuild" class="btn-primary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Auto Build
          </button>
          <button @click="clearCanvas" class="btn-secondary">Clear All</button>
          <button @click="saveGangSheet" class="btn-success">Save & Export</button>
        </div>
      </div>

      <!-- Sheet Size Selector -->
      <div class="flex items-center gap-4 mb-4">
        <label class="font-semibold text-gray-700">Sheet Size:</label>
        <select v-model="selectedSize" @change="changeSheetSize" class="px-4 py-2 border rounded-lg">
          <option value="22x120">22" × 120" (10 ft)</option>
          <option value="22x60">22" × 60" (5 ft)</option>
          <option value="13x19">13" × 19"</option>
          <option value="custom">Custom</option>
        </select>
        
        <div v-if="selectedSize === 'custom'" class="flex gap-2">
          <input 
            v-model.number="customWidth" 
            type="number" 
            placeholder="Width (in)" 
            class="w-24 px-3 py-2 border rounded-lg"
            min="1"
            max="1200"
          />
          <span class="self-center">×</span>
          <input 
            v-model.number="customHeight" 
            type="number" 
            placeholder="Height (in)" 
            class="w-24 px-3 py-2 border rounded-lg"
            min="1"
          />
          <button @click="applyCustomSize" class="btn-primary">Apply</button>
        </div>
        
        <div class="ml-auto">
          <label class="flex items-center">
            <input type="checkbox" v-model="showGrid" class="mr-2" />
            <span class="text-sm text-gray-700">Show Grid</span>
          </label>
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
            <input v-model.number="imageQuantity" type="number" min="1" class="w-full px-3 py-2 border rounded-lg" />
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
        <!-- Background Layer -->
        <v-layer>
          <!-- White background (offset por las reglas) -->
          <v-rect :config="{ x: 30, y: 30, width: sheetWidth * dpi, height: sheetHeight * dpi, fill: 'white', stroke: '#9ca3af', strokeWidth: 1 }" />
          
          <!-- Grid (offset por las reglas) -->
          <v-line v-if="showGrid" v-for="i in Math.ceil(sheetWidth)" :key="'v-' + i"
            :config="{ points: [(i * dpi) + 30, 30, (i * dpi) + 30, (sheetHeight * dpi) + 30], stroke: '#ddd', strokeWidth: 1 }" />
          <v-line v-if="showGrid" v-for="i in Math.ceil(sheetHeight)" :key="'h-' + i"
            :config="{ points: [30, (i * dpi) + 30, (sheetWidth * dpi) + 30, (i * dpi) + 30], stroke: '#ddd', strokeWidth: 1 }" />
        </v-layer>

        <!-- Rulers Layer -->
        <v-layer ref="rulersLayer">
          <!-- Regla horizontal superior (eje X) -->
          <v-rect :config="{ x: 30, y: 0, width: sheetWidth * dpi, height: 30, fill: '#d1d5db', stroke: '#9ca3af', strokeWidth: 1 }" />
          
          <!-- Marcas y números en regla horizontal -->
          <template v-for="i in Math.ceil(sheetWidth)" :key="'ruler-h-' + i">
            <!-- Línea de marca principal cada pulgada -->
            <v-line :config="{ 
              points: [(i * dpi) + 30, 0, (i * dpi) + 30, 20], 
              stroke: '#374151', 
              strokeWidth: 2 
            }" />
            <!-- Fondo blanco para número -->
            <v-rect :config="{ 
              x: (i * dpi) + 18, 
              y: 6, 
              width: 24, 
              height: 18, 
              fill: 'white', 
              opacity: 0.9 
            }" />
            <!-- Número de medida -->
            <v-text :config="{ 
              x: (i * dpi) + 20, 
              y: 8, 
              text: String(i) + '\u0022', 
              fontSize: 14, 
              fill: '#000000',
              fontStyle: 'bold',
              fontFamily: 'Arial, sans-serif'
            }" />
            <!-- Marcas intermedias (0.5 pulgadas) -->
            <v-line v-if="i < Math.ceil(sheetWidth)" :config="{ 
              points: [((i + 0.5) * dpi) + 30, 0, ((i + 0.5) * dpi) + 30, 10], 
              stroke: '#6b7280', 
              strokeWidth: 1 
            }" />
          </template>
          
          <!-- Regla vertical izquierda (eje Y) -->
          <v-rect :config="{ x: 0, y: 30, width: 30, height: sheetHeight * dpi, fill: '#d1d5db', stroke: '#9ca3af', strokeWidth: 1 }" />
          
          <!-- Marcas y números en regla vertical -->
          <template v-for="i in Math.ceil(sheetHeight)" :key="'ruler-v-' + i">
            <!-- Línea de marca principal cada pulgada -->
            <v-line :config="{ 
              points: [0, (i * dpi) + 30, 20, (i * dpi) + 30], 
              stroke: '#374151', 
              strokeWidth: 2 
            }" />
            <!-- Fondo blanco para número -->
            <v-rect :config="{ 
              x: 4, 
              y: (i * dpi) + 22, 
              width: 24, 
              height: 18, 
              fill: 'white', 
              opacity: 0.9 
            }" />
            <!-- Número de medida -->
            <v-text :config="{ 
              x: 6, 
              y: (i * dpi) + 24, 
              text: String(i) + '\u0022', 
              fontSize: 14, 
              fill: '#000000',
              fontStyle: 'bold',
              fontFamily: 'Arial, sans-serif'
            }" />
            <!-- Marcas intermedias (0.5 pulgadas) -->
            <v-line v-if="i < Math.ceil(sheetHeight)" :config="{ 
              points: [0, ((i + 0.5) * dpi) + 30, 10, ((i + 0.5) * dpi) + 30], 
              stroke: '#6b7280', 
              strokeWidth: 1 
            }" />
          </template>
          
          <!-- Esquina superior izquierda (intersección de reglas) -->
          <v-rect :config="{ x: 0, y: 0, width: 30, height: 30, fill: '#d1d5db', stroke: '#9ca3af', strokeWidth: 1 }" />
        </v-layer>

        <!-- Images Layer -->
        <v-layer ref="imagesLayer">
          <v-group
            v-for="(img, index) in images"
            :key="img.id"
            :config="{
              x: img.x + 30,
              y: img.y + 30,
              draggable: true,
              id: img.id,
              name: 'image-group'
            }"
            @dragend="handleDragEnd"
          >
            <v-image :config="{ image: img.imageObj, width: img.width, height: img.height }" />
          </v-group>
        </v-layer>

        <!-- Transformer Layer (separado para que siempre esté encima) -->
        <v-layer ref="transformerLayer">
          <v-transformer 
            ref="transformer"
            :config="{
              rotateEnabled: true,
              keepRatio: false,
              enabledAnchors: ['top-left', 'top-right', 'bottom-left', 'bottom-right', 'top-center', 'middle-left', 'middle-right', 'bottom-center'],
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
          <span class="font-semibold ml-2">{{ sheetWidth }}" × {{ sheetHeight }}"</span>
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
      <div class="text-sm text-gray-500">
        Resolution: {{ dpi }} DPI
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, nextTick } from 'vue';

export default {
  name: 'GangSheetEditor',
  setup() {
    // Stage and canvas configuration
    const dpi = 72; // Canvas DPI for display
    const stageConfig = reactive({
      width: 800,
      height: 600,
      scaleX: 1,
      scaleY: 1,
    });

    // Sheet dimensions (in inches)
    const sheetWidth = ref(22);
    const sheetHeight = ref(120);
    const selectedSize = ref('22x120');
    const customWidth = ref(22);
    const customHeight = ref(120);
    const showGrid = ref(true);

    // Images
    const uploadedImages = ref([]);
    const images = ref([]);
    const selectedImage = ref(null);
    const imageWidth = ref(0);
    const imageHeight = ref(0);
    const imageQuantity = ref(1);
    const draggedImage = ref(null); // Para el drag & drop

    // Refs
    const stage = ref(null);
    const transformer = ref(null);
    const editorContainer = ref(null);
    const imagesLayer = ref(null);
    const transformerLayer = ref(null);
    const rulersLayer = ref(null);

    // Computed
    const coveragePercentage = computed(() => {
      const totalArea = sheetWidth.value * sheetHeight.value;
      const usedArea = images.value.reduce((acc, img) => {
        const imgWidthInches = img.width / dpi;
        const imgHeightInches = img.height / dpi;
        return acc + (imgWidthInches * imgHeightInches);
      }, 0);
      return Math.round((usedArea / totalArea) * 100);
    });

    // Methods
    const changeSheetSize = () => {
      const sizes = {
        '22x120': { width: 22, height: 120 },
        '22x60': { width: 22, height: 60 },
        '13x19': { width: 13, height: 19 },
      };
      
      if (selectedSize.value !== 'custom') {
        sheetWidth.value = sizes[selectedSize.value].width;
        sheetHeight.value = sizes[selectedSize.value].height;
        updateStageSize();
      }
    };

    const applyCustomSize = () => {
      sheetWidth.value = customWidth.value;
      sheetHeight.value = customHeight.value;
      updateStageSize();
    };

    const updateStageSize = () => {
      if (!editorContainer.value) return;
      
      const rulerSize = 30; // Tamaño de las reglas en píxeles
      const containerWidth = editorContainer.value.clientWidth - 32; // 32px de padding
      const scale = (containerWidth - rulerSize) / (sheetWidth.value * dpi);
      
      // Incluir espacio para las reglas
      stageConfig.width = (sheetWidth.value * dpi * scale) + rulerSize;
      stageConfig.height = (sheetHeight.value * dpi * scale) + rulerSize;
      stageConfig.scaleX = scale;
      stageConfig.scaleY = scale;
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

        const reader = new FileReader();
        reader.onload = (e) => {
          const img = new Image();
          img.onload = () => {
            const imageData = {
              id: Date.now() + Math.random(),
              name: file.name,
              src: e.target.result,
              size: file.size,
              originalWidth: img.width,
              originalHeight: img.height,
              imageObj: img,
            };
            uploadedImages.value.push(imageData);
          };
          img.src = e.target.result;
        };
        reader.readAsDataURL(file);
      });
    };

    const addImageToCanvas = (uploadedImage) => {
      // Default size: 4 inches width, maintain aspect ratio
      const defaultWidthInches = 4;
      const aspectRatio = uploadedImage.originalHeight / uploadedImage.originalWidth;
      const defaultHeightInches = defaultWidthInches * aspectRatio;

      for (let i = 0; i < imageQuantity.value; i++) {
        const newImage = {
          id: Date.now() + Math.random(),
          x: 50 + (i * 20),
          y: 50 + (i * 20),
          width: defaultWidthInches * dpi,
          height: defaultHeightInches * dpi,
          imageObj: uploadedImage.imageObj,
          name: uploadedImage.name,
        };
        images.value.push(newImage);
      }
    };

    // Drag & Drop desde la lista de imágenes hacia el canvas
    const handleImageDragStart = (event, img) => {
      draggedImage.value = img;
      // Configurar el efecto visual del drag
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
      
      // Obtener las coordenadas del drop relativas al canvas
      const containerRect = editorContainer.value.getBoundingClientRect();
      const stageNode = stage.value?.getStage();
      if (!stageNode) return;
      
      // Calcular posición en el canvas (considerando el scale, padding y reglas)
      const rulerSize = 30;
      const scale = stageConfig.scaleX;
      const dropX = (event.clientX - containerRect.left - 16 - rulerSize) / scale; // 16px padding + 30px regla
      const dropY = (event.clientY - containerRect.top - 16 - rulerSize) / scale;
      
      // Default size: 4 inches width, maintain aspect ratio
      const defaultWidthInches = 4;
      const aspectRatio = draggedImage.value.originalHeight / draggedImage.value.originalWidth;
      const defaultHeightInches = defaultWidthInches * aspectRatio;
      
      // Crear la imagen en la posición del drop
      const newImage = {
        id: Date.now() + Math.random(),
        x: Math.max(0, Math.min(dropX, sheetWidth.value * dpi - defaultWidthInches * dpi)),
        y: Math.max(0, Math.min(dropY, sheetHeight.value * dpi - defaultHeightInches * dpi)),
        width: defaultWidthInches * dpi,
        height: defaultHeightInches * dpi,
        imageObj: draggedImage.value.imageObj,
        name: draggedImage.value.name,
      };
      
      images.value.push(newImage);
      draggedImage.value = null;
    };

    const handleImageClick = (e) => {
      // Don't interfere with transformer handles
      const clickedOnTransformer = e.target.getClassName() === 'Transformer';
      if (clickedOnTransformer) return;
      
      // Get the clicked group (traverse up if we clicked on the image inside)
      let target = e.target;
      while (target && target.getClassName() !== 'Group') {
        target = target.getParent();
      }
      
      if (!target) return;
      
      // Find the image data
      const id = target.id();
      const img = images.value.find(i => i.id === id);
      
      if (img) {
        selectedImage.value = img;
        imageWidth.value = (img.width / dpi).toFixed(2);
        imageHeight.value = (img.height / dpi).toFixed(2);
        
        // Attach transformer to the clicked node
        nextTick(() => {
          if (transformer.value) {
            const transformerNode = transformer.value.getNode();
            transformerNode.nodes([target]);
            transformerNode.getLayer().batchDraw();
          }
        });
      }
    };

    const handleStageClick = (e) => {
      const clickedNode = e.target;
      
      // Check if clicked on transformer or its anchors
      if (clickedNode.getClassName() === 'Transformer' || 
          clickedNode.hasName('_anchor') ||
          clickedNode.hasName('back')) {
        return; // Don't do anything, let transformer handle it
      }
      
      // Check if clicked on an image group
      let targetGroup = clickedNode;
      while (targetGroup && targetGroup.getClassName() !== 'Group' && targetGroup !== targetGroup.getStage()) {
        targetGroup = targetGroup.getParent();
      }
      
      if (targetGroup && targetGroup.getClassName() === 'Group' && targetGroup.name() === 'image-group') {
        // Clicked on an image - select it
        const id = targetGroup.id();
        const img = images.value.find(i => i.id === id);
        
        if (img) {
          selectedImage.value = img;
          imageWidth.value = (img.width / dpi).toFixed(2);
          imageHeight.value = (img.height / dpi).toFixed(2);
          
          nextTick(() => {
            if (transformer.value) {
              const transformerNode = transformer.value.getNode();
              transformerNode.nodes([targetGroup]);
              // Forzar redibujado de ambos layers
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
        // Clicked on empty area - deselect
        selectedImage.value = null;
        if (transformer.value) {
          const transformerNode = transformer.value.getNode();
          transformerNode.nodes([]);
          // Forzar redibujado del transformer layer
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
      if (img) {
        // Restar el offset de las reglas (30px)
        img.x = node.x() - 30;
        img.y = node.y() - 30;
      }
    };

    const handleTransformEnd = (e) => {
      // Get the node being transformed from the transformer
      const transformerNode = transformer.value?.getNode();
      if (!transformerNode) return;
      
      const nodes = transformerNode.nodes();
      if (!nodes || nodes.length === 0) return;
      
      const groupNode = nodes[0]; // Get the group
      const id = groupNode.id();
      const img = images.value.find(i => i.id === id);
      
      if (img) {
        const scaleX = groupNode.scaleX();
        const scaleY = groupNode.scaleY();
        
        // Update image dimensions based on scale and current size
        const newWidth = img.width * scaleX;
        const newHeight = img.height * scaleY;
        
        // Update the image data
        img.width = newWidth;
        img.height = newHeight;
        
        // Update the input fields
        imageWidth.value = (newWidth / dpi).toFixed(2);
        imageHeight.value = (newHeight / dpi).toFixed(2);
        
        // Reset scale to 1
        groupNode.scaleX(1);
        groupNode.scaleY(1);
        
        // Force update on both layers
        if (imagesLayer.value) {
          imagesLayer.value.getNode().batchDraw();
        }
        if (transformerLayer.value) {
          transformerLayer.value.getNode().batchDraw();
        }
      }
    };

    const updateImageSize = () => {
      if (!selectedImage.value) return;
      selectedImage.value.width = imageWidth.value * dpi;
      selectedImage.value.height = imageHeight.value * dpi;
      
      // Update the transformer to reflect new size
      nextTick(() => {
        if (stage.value && transformer.value) {
          const node = stage.value.getStage().findOne('#' + selectedImage.value.id);
          if (node) {
            // Update the node size
            const parent = node.getParent();
            if (parent) {
              // Force transformer to update
              const transformerNode = transformer.value.getNode();
              transformerNode.forceUpdate();
              transformerNode.getLayer().batchDraw();
            }
          }
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
      if (transformer.value) {
        const transformerNode = transformer.value.getNode();
        transformerNode.nodes([]);
        // Forzar redibujado de ambos layers
        if (imagesLayer.value) {
          imagesLayer.value.getNode().batchDraw();
        }
        if (transformerLayer.value) {
          transformerLayer.value.getNode().batchDraw();
        }
      }
    };

    const clearCanvas = () => {
      if (confirm('Are you sure you want to clear all images from the canvas?')) {
        images.value = [];
        selectedImage.value = null;
      }
    };

    const autoBuild = () => {
      if (uploadedImages.value.length === 0) {
        alert('Please upload images first');
        return;
      }

      // Simple bin packing algorithm (First Fit Decreasing)
      images.value = [];
      const margin = 0.25 * dpi; // 0.25 inch margin
      let currentX = margin;
      let currentY = margin;
      let rowHeight = 0;

      // Sort images by height (descending)
      const sortedImages = [...uploadedImages.value].sort((a, b) => b.originalHeight - a.originalHeight);

      sortedImages.forEach((uploadedImage) => {
        const defaultWidthInches = 4;
        const aspectRatio = uploadedImage.originalHeight / uploadedImage.originalWidth;
        const defaultHeightInches = defaultWidthInches * aspectRatio;
        const imgWidth = defaultWidthInches * dpi;
        const imgHeight = defaultHeightInches * dpi;

        // Check if image fits in current row
        if (currentX + imgWidth + margin > sheetWidth.value * dpi) {
          // Move to next row
          currentX = margin;
          currentY += rowHeight + margin;
          rowHeight = 0;
        }

        // Check if image fits vertically
        if (currentY + imgHeight + margin <= sheetHeight.value * dpi) {
          images.value.push({
            id: Date.now() + Math.random(),
            x: currentX,
            y: currentY,
            width: imgWidth,
            height: imgHeight,
            imageObj: uploadedImage.imageObj,
            name: uploadedImage.name,
          });

          currentX += imgWidth + margin;
          rowHeight = Math.max(rowHeight, imgHeight);
        }
      });
    };

    const saveGangSheet = async () => {
      if (images.value.length === 0) {
        alert('Please add images to the canvas first');
        return;
      }

      // Get the stage as data URL
      const stageNode = stage.value.getStage();
      const dataURL = stageNode.toDataURL({ pixelRatio: 300 / 72 }); // Export at 300 DPI

      // Send to Laravel backend
      try {
        const response = await fetch('/api/gang-sheets/save', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            width: sheetWidth.value,
            height: sheetHeight.value,
            images: images.value.map(img => ({
              x: img.x / dpi,
              y: img.y / dpi,
              width: img.width / dpi,
              height: img.height / dpi,
              name: img.name,
            })),
            preview: dataURL,
          }),
        });

        if (response.ok) {
          const result = await response.json();
          alert('Gang sheet saved successfully!');
          console.log('Saved:', result);
        } else {
          alert('Error saving gang sheet');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error saving gang sheet');
      }
    };

    // Lifecycle
    onMounted(() => {
      updateStageSize();
      window.addEventListener('resize', updateStageSize);
    });

    return {
      // Config
      dpi,
      stageConfig,
      sheetWidth,
      sheetHeight,
      selectedSize,
      customWidth,
      customHeight,
      showGrid,
      
      // Images
      uploadedImages,
      images,
      selectedImage,
      imageWidth,
      imageHeight,
      imageQuantity,
      
      // Refs
      stage,
      transformer,
      editorContainer,
      imagesLayer,
      transformerLayer,
      rulersLayer,
      
      // Computed
      coveragePercentage,
      
      // Methods
      changeSheetSize,
      applyCustomSize,
      handleFileUpload,
      handleDrop,
      addImageToCanvas,
      handleImageDragStart,
      handleCanvasDragOver,
      handleCanvasDrop,
      handleImageClick,
      handleStageClick,
      handleDragEnd,
      handleTransformEnd,
      updateImageSize,
      deleteSelected,
      clearCanvas,
      autoBuild,
      saveGangSheet,
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
  max-height: 70vh;
  position: relative;
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

/* Estilos para drag & drop */
.cursor-move {
  cursor: move;
}

.cursor-move:active {
  cursor: grabbing;
  opacity: 0.7;
}
</style>
