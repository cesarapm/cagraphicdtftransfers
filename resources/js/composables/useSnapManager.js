/**
 * Composable para sistema de snap profesional
 * Snap a grid, bordes, centros y guías visuales
 */
import { ref } from 'vue';

export function useSnapManager() {
  const snapEnabled = ref(true);
  const snapToGrid = ref(true);
  const snapToEdges = ref(true);
  const snapToCenters = ref(true);
  const snapThreshold = ref(5); // píxeles de tolerancia en pantalla
  
  // Guías visibles temporalmente
  const activeGuides = ref([]);

  /**
   * Snap a grid (basado en pulgadas)
   * @param {number} valueInches - Valor en pulgadas
   * @param {number} gridSizeInches - Tamaño del grid en pulgadas (ej: 1 para snap cada pulgada)
   * @returns {number} - Valor ajustado en pulgadas
   */
  const snapValueToGrid = (valueInches, gridSizeInches = 1) => {
    if (!snapEnabled.value || !snapToGrid.value) return valueInches;
    return Math.round(valueInches / gridSizeInches) * gridSizeInches;
  };

  /**
   * Snap posición completa al grid
   */
  const snapPositionToGridInches = (position, gridSizeInches = 1) => {
    if (!snapEnabled.value || !snapToGrid.value) return position;
    
    return {
      xInches: snapValueToGrid(position.xInches, gridSizeInches),
      yInches: snapValueToGrid(position.yInches, gridSizeInches),
    };
  };

  /**
   * Snap a bordes del canvas
   * @param {object} image - Imagen con xInches, yInches, widthInches, heightInches
   * @param {number} canvasWidthInches - Ancho del canvas en pulgadas
   * @param {number} canvasHeightInches - Alto del canvas en pulgadas
   * @param {number} thresholdInches - Tolerancia en pulgadas
   * @returns {object} - Nueva posición con snap aplicado
   */
  const snapToCanvasEdges = (image, canvasWidthInches, canvasHeightInches, thresholdInches = 0.5) => {
    if (!snapEnabled.value || !snapToEdges.value) {
      return { xInches: image.xInches, yInches: image.yInches };
    }

    let x = image.xInches;
    let y = image.yInches;
    const guides = [];

    // Snap al borde izquierdo
    if (Math.abs(x) < thresholdInches) {
      x = 0;
      guides.push({ type: 'vertical', position: 0, label: 'Left Edge' });
    }

    // Snap al borde derecho
    const rightEdge = x + image.widthInches;
    if (Math.abs(rightEdge - canvasWidthInches) < thresholdInches) {
      x = canvasWidthInches - image.widthInches;
      guides.push({ type: 'vertical', position: canvasWidthInches, label: 'Right Edge' });
    }

    // Snap al borde superior
    if (Math.abs(y) < thresholdInches) {
      y = 0;
      guides.push({ type: 'horizontal', position: 0, label: 'Top Edge' });
    }

    // Snap al borde inferior
    const bottomEdge = y + image.heightInches;
    if (Math.abs(bottomEdge - canvasHeightInches) < thresholdInches) {
      y = canvasHeightInches - image.heightInches;
      guides.push({ type: 'horizontal', position: canvasHeightInches, label: 'Bottom Edge' });
    }

    // Actualizar guías activas
    activeGuides.value = guides;

    return { xInches: x, yInches: y };
  };

  /**
   * Snap al centro del canvas
   */
  const snapToCanvasCenter = (image, canvasWidthInches, canvasHeightInches, thresholdInches = 0.5) => {
    if (!snapEnabled.value || !snapToCenters.value) {
      return { xInches: image.xInches, yInches: image.yInches };
    }

    let x = image.xInches;
    let y = image.yInches;
    const guides = [];

    const canvasCenterX = canvasWidthInches / 2;
    const canvasCenterY = canvasHeightInches / 2;
    
    const imageCenterX = x + image.widthInches / 2;
    const imageCenterY = y + image.heightInches / 2;

    // Snap horizontal center
    if (Math.abs(imageCenterX - canvasCenterX) < thresholdInches) {
      x = canvasCenterX - image.widthInches / 2;
      guides.push({ type: 'vertical', position: canvasCenterX, label: 'Center' });
    }

    // Snap vertical center
    if (Math.abs(imageCenterY - canvasCenterY) < thresholdInches) {
      y = canvasCenterY - image.heightInches / 2;
      guides.push({ type: 'horizontal', position: canvasCenterY, label: 'Center' });
    }

    // Actualizar guías activas
    activeGuides.value = guides;

    return { xInches: x, yInches: y };
  };

  /**
   * Snap entre imágenes (alineación entre elementos)
   */
  const snapToOtherImages = (currentImage, otherImages, thresholdInches = 0.5) => {
    if (!snapEnabled.value || !snapToEdges.value) {
      return { xInches: currentImage.xInches, yInches: currentImage.yInches };
    }

    let x = currentImage.xInches;
    let y = currentImage.yInches;
    const guides = [];

    const currentCenterX = x + currentImage.widthInches / 2;
    const currentCenterY = y + currentImage.heightInches / 2;
    const currentRight = x + currentImage.widthInches;
    const currentBottom = y + currentImage.heightInches;

    for (const other of otherImages) {
      if (other.id === currentImage.id) continue;

      const otherCenterX = other.xInches + other.widthInches / 2;
      const otherCenterY = other.yInches + other.heightInches / 2;
      const otherRight = other.xInches + other.widthInches;
      const otherBottom = other.yInches + other.heightInches;

      // Snap bordes verticales
      if (Math.abs(x - other.xInches) < thresholdInches) {
        x = other.xInches;
        guides.push({ type: 'vertical', position: other.xInches, label: 'Align Left' });
      }
      if (Math.abs(currentRight - otherRight) < thresholdInches) {
        x = otherRight - currentImage.widthInches;
        guides.push({ type: 'vertical', position: otherRight, label: 'Align Right' });
      }
      if (Math.abs(currentCenterX - otherCenterX) < thresholdInches) {
        x = otherCenterX - currentImage.widthInches / 2;
        guides.push({ type: 'vertical', position: otherCenterX, label: 'Align Center' });
      }

      // Snap bordes horizontales
      if (Math.abs(y - other.yInches) < thresholdInches) {
        y = other.yInches;
        guides.push({ type: 'horizontal', position: other.yInches, label: 'Align Top' });
      }
      if (Math.abs(currentBottom - otherBottom) < thresholdInches) {
        y = otherBottom - currentImage.heightInches;
        guides.push({ type: 'horizontal', position: otherBottom, label: 'Align Bottom' });
      }
      if (Math.abs(currentCenterY - otherCenterY) < thresholdInches) {
        y = otherCenterY - currentImage.heightInches / 2;
        guides.push({ type: 'horizontal', position: otherCenterY, label: 'Align Middle' });
      }
    }

    // Actualizar guías activas
    activeGuides.value = guides;

    return { xInches: x, yInches: y };
  };

  /**
   * Aplicar todos los snaps en secuencia
   */
  const applyAllSnaps = (image, allImages, canvasWidthInches, canvasHeightInches) => {
    if (!snapEnabled.value) {
      return { xInches: image.xInches, yInches: image.yInches };
    }

    let position = { xInches: image.xInches, yInches: image.yInches };

    // 1. Snap al grid (más prioritario)
    if (snapToGrid.value) {
      position = snapPositionToGridInches(position, 1); // Cada pulgada
    }

    // 2. Snap a centros del canvas
    if (snapToCenters.value) {
      const centerSnap = snapToCanvasCenter(
        { ...image, ...position },
        canvasWidthInches,
        canvasHeightInches
      );
      if (centerSnap.xInches !== position.xInches || centerSnap.yInches !== position.yInches) {
        position = centerSnap;
      }
    }

    // 3. Snap a bordes del canvas
    if (snapToEdges.value) {
      const edgeSnap = snapToCanvasEdges(
        { ...image, ...position },
        canvasWidthInches,
        canvasHeightInches
      );
      if (edgeSnap.xInches !== position.xInches || edgeSnap.yInches !== position.yInches) {
        position = edgeSnap;
      }
    }

    // 4. Snap a otras imágenes
    if (snapToEdges.value && allImages && allImages.length > 1) {
      const imageSnap = snapToOtherImages(
        { ...image, ...position },
        allImages
      );
      if (imageSnap.xInches !== position.xInches || imageSnap.yInches !== position.yInches) {
        position = imageSnap;
      }
    }

    return position;
  };

  /**
   * Limpiar guías visuales
   */
  const clearGuides = () => {
    activeGuides.value = [];
  };

  /**
   * Toggle snap general
   */
  const toggleSnap = () => {
    snapEnabled.value = !snapEnabled.value;
    if (!snapEnabled.value) {
      clearGuides();
    }
  };

  return {
    // Estado
    snapEnabled,
    snapToGrid,
    snapToEdges,
    snapToCenters,
    snapThreshold,
    activeGuides,

    // Métodos
    snapValueToGrid,
    snapPositionToGridInches,
    snapToCanvasEdges,
    snapToCanvasCenter,
    snapToOtherImages,
    applyAllSnaps,
    clearGuides,
    toggleSnap,
  };
}
