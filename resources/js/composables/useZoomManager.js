/**
 * Composable para gestión de zoom profesional tipo Photoshop
 * Zoom centrado en el cursor, niveles predefinidos, actualización de rulers
 */
import { ref, computed } from 'vue';

export function useZoomManager() {
  const zoomLevel = ref(1); // 1 = 100%
  const MIN_ZOOM = 0.25;   // 25%
  const MAX_ZOOM = 8;       // 800%

  // Niveles de zoom predefinidos para botones
  const ZOOM_PRESETS = [0.25, 0.5, 0.75, 1, 1.5, 2, 3, 4, 6, 8];

  /**
   * Ajustar zoom manualmente
   */
  const setZoom = (newZoom) => {
    zoomLevel.value = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, newZoom));
  };

  /**
   * Zoom in (incrementar)
   */
  const zoomIn = () => {
    const currentIndex = ZOOM_PRESETS.findIndex(z => z >= zoomLevel.value);
    const nextIndex = Math.min(currentIndex + 1, ZOOM_PRESETS.length - 1);
    setZoom(ZOOM_PRESETS[nextIndex]);
  };

  /**
   * Zoom out (decrementar)
   */
  const zoomOut = () => {
    const currentIndex = ZOOM_PRESETS.findIndex(z => z >= zoomLevel.value);
    const prevIndex = Math.max(currentIndex - 1, 0);
    setZoom(ZOOM_PRESETS[prevIndex]);
  };

  /**
   * Zoom to fit (ajustar al contenedor)
   */
  const zoomToFit = (canvasWidth, canvasHeight, containerWidth, containerHeight, rulerSize = 30) => {
    const availableWidth = containerWidth - rulerSize - 20; // 20px de margen extra
    const availableHeight = containerHeight - rulerSize - 20; // 20px de margen extra
    
    const scaleX = availableWidth / canvasWidth;
    const scaleY = availableHeight / canvasHeight;
    
    // Usar el menor de los dos y aplicar factor de seguridad del 95%
    const fitZoom = Math.min(scaleX, scaleY, MAX_ZOOM) * 0.95;
    setZoom(fitZoom);
  };

  /**
   * Zoom al 100% (1:1)
   */
  const zoomTo100 = () => {
    setZoom(1);
  };

  /**
   * Zoom con rueda del mouse centrado en el cursor
   * @param {WheelEvent} e - Evento de la rueda
   * @param {Konva.Stage} stage - Stage de Konva
   */
  const handleWheelZoom = (e, stage) => {
    e.evt.preventDefault();

    const oldScale = stage.scaleX();
    const pointer = stage.getPointerPosition();

    const mousePointTo = {
      x: (pointer.x - stage.x()) / oldScale,
      y: (pointer.y - stage.y()) / oldScale,
    };

    // Determinar dirección del zoom
    const direction = e.evt.deltaY > 0 ? -1 : 1;
    const zoomFactor = 1.1; // 10% por paso

    let newScale = direction > 0 ? oldScale * zoomFactor : oldScale / zoomFactor;
    newScale = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, newScale));

    zoomLevel.value = newScale;

    // Ajustar posición para mantener el punto del mouse fijo
    const newPos = {
      x: pointer.x - mousePointTo.x * newScale,
      y: pointer.y - mousePointTo.y * newScale,
    };

    stage.scale({ x: newScale, y: newScale });
    stage.position(newPos);
  };

  /**
   * Formatear zoom para display (ej: "100%")
   */
  const zoomPercentage = computed(() => {
    return Math.round(zoomLevel.value * 100) + '%';
  });

  /**
   * Determinar nivel de detalle para rulers basado en zoom
   */
  const getRulerDetail = computed(() => {
    const zoom = zoomLevel.value;
    
    if (zoom >= 4) {
      return { showFeet: true, showInches: true, showHalfInches: true, showQuarterInches: true };
    } else if (zoom >= 2) {
      return { showFeet: true, showInches: true, showHalfInches: true, showQuarterInches: false };
    } else if (zoom >= 1) {
      return { showFeet: true, showInches: true, showHalfInches: false, showQuarterInches: false };
    } else if (zoom >= 0.5) {
      return { showFeet: true, showInches: true, showHalfInches: false, showQuarterInches: false };
    } else {
      return { showFeet: true, showInches: false, showHalfInches: false, showQuarterInches: false };
    }
  });

  /**
   * Determinar nivel de detalle para grid basado en zoom
   */
  const getGridDetail = computed(() => {
    const zoom = zoomLevel.value;
    
    if (zoom >= 3) {
      return { showFeet: true, showInches: true, showHalfInches: true };
    } else if (zoom >= 1.5) {
      return { showFeet: true, showInches: true, showHalfInches: false };
    } else if (zoom >= 0.75) {
      return { showFeet: true, showInches: true, showHalfInches: false };
    } else {
      return { showFeet: true, showInches: false, showHalfInches: false };
    }
  });

  return {
    // Estado
    zoomLevel,
    MIN_ZOOM,
    MAX_ZOOM,
    ZOOM_PRESETS,

    // Computed
    zoomPercentage,
    getRulerDetail,
    getGridDetail,

    // Métodos
    setZoom,
    zoomIn,
    zoomOut,
    zoomToFit,
    zoomTo100,
    handleWheelZoom,
  };
}
