/**
 * Composable para conversión de unidades
 * Separa la visualización del editor de la resolución de impresión
 */
import { computed } from 'vue';

export function useUnitConverter() {
  // CONSTANTES FUNDAMENTALES
  const INCHES_PER_FOOT = 12;
  const EDITOR_PPI = 10;  // Pixels por pulgada en el EDITOR (visual)
  const EXPORT_DPI = 150; // DPI para exportación DTF (balance calidad/memoria)

  /**
   * Convertir pies a pulgadas
   */
  const feetToInches = (feet) => feet * INCHES_PER_FOOT;

  /**
   * Convertir pulgadas a pies
   */
  const inchesToFeet = (inches) => inches / INCHES_PER_FOOT;

  /**
   * Convertir pulgadas a píxeles del EDITOR
   */
  const inchesToEditorPixels = (inches) => inches * EDITOR_PPI;

  /**
   * Convertir píxeles del EDITOR a pulgadas
   */
  const editorPixelsToInches = (pixels) => pixels / EDITOR_PPI;

  /**
   * Convertir pies a píxeles del EDITOR
   */
  const feetToEditorPixels = (feet) => {
    return feetToInches(feet) * EDITOR_PPI;
  };

  /**
   * Convertir píxeles del EDITOR a pies
   */
  const editorPixelsToFeet = (pixels) => {
    return inchesToFeet(editorPixelsToInches(pixels));
  };

  /**
   * Convertir pulgadas a píxeles de EXPORTACIÓN
   */
  const inchesToExportPixels = (inches) => inches * EXPORT_DPI;

  /**
   * Convertir píxeles de EXPORTACIÓN a pulgadas
   */
  const exportPixelsToInches = (pixels) => pixels / EXPORT_DPI;

  /**
   * Convertir objeto con dimensiones de pulgadas a píxeles del editor
   */
  const dimensionsToEditorPixels = (dimensions) => {
    return {
      x: inchesToEditorPixels(dimensions.xInches),
      y: inchesToEditorPixels(dimensions.yInches),
      width: inchesToEditorPixels(dimensions.widthInches),
      height: inchesToEditorPixels(dimensions.heightInches),
    };
  };

  /**
   * Convertir objeto con dimensiones de píxeles del editor a pulgadas
   */
  const editorPixelsToDimensions = (pixels) => {
    return {
      xInches: editorPixelsToInches(pixels.x),
      yInches: editorPixelsToInches(pixels.y),
      widthInches: editorPixelsToInches(pixels.width),
      heightInches: editorPixelsToInches(pixels.height),
    };
  };

  /**
   * Convertir objeto con dimensiones de pulgadas a píxeles de exportación
   */
  const dimensionsToExportPixels = (dimensions) => {
    return {
      x: inchesToExportPixels(dimensions.xInches),
      y: inchesToExportPixels(dimensions.yInches),
      width: inchesToExportPixels(dimensions.widthInches),
      height: inchesToExportPixels(dimensions.heightInches),
    };
  };

  /**
   * Calcular DPI real de una imagen basado en sus píxeles originales
   * y el tamaño final en pulgadas
   */
  const calculateImageDPI = (originalPixelWidth, originalPixelHeight, widthInches, heightInches) => {
    const dpiWidth = widthInches > 0 ? originalPixelWidth / widthInches : 0;
    const dpiHeight = heightInches > 0 ? originalPixelHeight / heightInches : 0;
    return Math.min(dpiWidth, dpiHeight);
  };

  /**
   * Determinar calidad de impresión basada en DPI
   */
  const getImageQuality = (dpi) => {
    if (dpi >= 200) return { level: 'excellent', message: 'Excelente calidad DTF', color: 'green' };
    if (dpi >= 150) return { level: 'good', message: 'Buena calidad DTF', color: 'blue' };
    if (dpi >= 100) return { level: 'acceptable', message: 'Calidad aceptable', color: 'yellow' };
    return { level: 'poor', message: 'Calidad baja - no recomendado', color: 'red' };
  };

  /**
   * Calcular tamaño estimado de archivo exportado
   */
  const estimateExportFileSize = (widthInches, heightInches) => {
    const exportWidth = inchesToExportPixels(widthInches);
    const exportHeight = inchesToExportPixels(heightInches);
    const totalPixels = exportWidth * exportHeight;
    
    // PNG comprimido típico: ~1.5 bytes por píxel
    const estimatedBytes = totalPixels * 1.5;
    return {
      bytes: estimatedBytes,
      megabytes: (estimatedBytes / 1_000_000).toFixed(1),
      width: Math.round(exportWidth),
      height: Math.round(exportHeight),
    };
  };

  return {
    // Constantes
    INCHES_PER_FOOT,
    EDITOR_PPI,
    EXPORT_DPI,

    // Conversiones básicas
    feetToInches,
    inchesToFeet,
    inchesToEditorPixels,
    editorPixelsToInches,
    feetToEditorPixels,
    editorPixelsToFeet,
    inchesToExportPixels,
    exportPixelsToInches,

    // Conversiones de objetos
    dimensionsToEditorPixels,
    editorPixelsToDimensions,
    dimensionsToExportPixels,

    // Utilidades
    calculateImageDPI,
    getImageQuality,
    estimateExportFileSize,
  };
}
