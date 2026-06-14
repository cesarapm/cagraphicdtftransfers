import Konva from 'konva';

/**
 * Optimized Grid Component para Konva - REFACTORIZADO
 * Usa EDITOR_PPI y unidades reales en pulgadas
 * Dibuja cuadrícula usando Canvas API directamente
 * Adaptativo según nivel de zoom
 */
export function createGrid(config) {
  const {
    canvasWidthInches,
    canvasHeightInches,
    editorPPI = 10,      // Píxeles por pulgada en el EDITOR
    zoom = 1,            // Nivel de zoom actual
    gridDetail = null,   // { showFeet, showInches, showHalfInches }
    visible = true,
  } = config;

  const inchesToPixels = (inches) => inches * editorPPI * zoom;
  const width = inchesToPixels(canvasWidthInches);
  const height = inchesToPixels(canvasHeightInches);

  return new Konva.Shape({
    sceneFunc: (context, shape) => {
      if (!visible) return;
      
      // Determinar nivel de detalle (si no se proporciona)
      const detail = gridDetail || {
        showFeet: true,
        showInches: zoom >= 0.75,
        showHalfInches: zoom >= 3,
      };
      
      const pixelsPerInch = editorPPI * zoom;
      const pixelsPerFoot = pixelsPerInch * 12;

      // Grid de pies (líneas principales)
      if (detail.showFeet) {
        context.strokeStyle = '#d1d5db';
        context.lineWidth = 1.5 / zoom;
        
        const totalFeet = Math.ceil(canvasWidthInches / 12);
        const totalFeetHeight = Math.ceil(canvasHeightInches / 12);
        
        // Líneas verticales (cada pie)
        for (let feet = 0; feet <= totalFeet; feet++) {
          const x = inchesToPixels(feet * 12);
          if (x > width) break;
          context.beginPath();
          context.moveTo(x, 0);
          context.lineTo(x, height);
          context.stroke();
        }
        
        // Líneas horizontales (cada pie)
        for (let feet = 0; feet <= totalFeetHeight; feet++) {
          const y = inchesToPixels(feet * 12);
          if (y > height) break;
          context.beginPath();
          context.moveTo(0, y);
          context.lineTo(width, y);
          context.stroke();
        }
      }
      
      // Grid de pulgadas (líneas secundarias)
      if (detail.showInches) {
        context.strokeStyle = '#e5e7eb';
        context.lineWidth = 0.5 / zoom;
        
        const totalInches = Math.ceil(canvasWidthInches);
        const totalInchesHeight = Math.ceil(canvasHeightInches);
        
        // Líneas verticales (cada pulgada)
        for (let inch = 0; inch <= totalInches; inch++) {
          // Saltar líneas que coinciden con pies
          if (inch % 12 === 0) continue;
          
          const x = inchesToPixels(inch);
          if (x > width) break;
          context.beginPath();
          context.moveTo(x, 0);
          context.lineTo(x, height);
          context.stroke();
        }
        
        // Líneas horizontales (cada pulgada)
        for (let inch = 0; inch <= totalInchesHeight; inch++) {
          // Saltar líneas que coinciden con pies
          if (inch % 12 === 0) continue;
          
          const y = inchesToPixels(inch);
          if (y > height) break;
          context.beginPath();
          context.moveTo(0, y);
          context.lineTo(width, y);
          context.stroke();
        }
      }
      
      // Grid de medias pulgadas (líneas más finas)
      if (detail.showHalfInches) {
        context.strokeStyle = '#f3f4f6';
        context.lineWidth = 0.25 / zoom;
        
        const totalHalfInches = Math.ceil(canvasWidthInches * 2);
        const totalHalfInchesHeight = Math.ceil(canvasHeightInches * 2);
        
        // Líneas verticales (cada media pulgada)
        for (let halfInch = 0; halfInch <= totalHalfInches; halfInch++) {
          // Saltar líneas que coinciden con pulgadas completas
          if (halfInch % 2 === 0) continue;
          
          const x = inchesToPixels(halfInch * 0.5);
          if (x > width) break;
          context.beginPath();
          context.moveTo(x, 0);
          context.lineTo(x, height);
          context.stroke();
        }
        
        // Líneas horizontales (cada media pulgada)
        for (let halfInch = 0; halfInch <= totalHalfInchesHeight; halfInch++) {
          // Saltar líneas que coinciden con pulgadas completas
          if (halfInch % 2 === 0) continue;
          
          const y = inchesToPixels(halfInch * 0.5);
          if (y > height) break;
          context.beginPath();
          context.moveTo(0, y);
          context.lineTo(width, y);
          context.stroke();
        }
      }
      
      context.fillStrokeShape(shape);
    },
    hitFunc: (context, shape) => {
      // Grid no es clickeable
      context.beginPath();
      context.rect(0, 0, width, height);
      context.closePath();
      context.fillStrokeShape(shape);
    },
  });
}

/**
 * Función de utilidad para snap al grid
 */
export function snapToGrid(value, gridSize, enabled = true) {
  if (!enabled) return value;
  return Math.round(value / gridSize) * gridSize;
}

/**
 * Función para snap de posición de objeto al grid
 */
export function snapPositionToGrid(pos, gridSizePixels, enabled = true) {
  if (!enabled) return pos;
  
  return {
    x: snapToGrid(pos.x, gridSizePixels, enabled),
    y: snapToGrid(pos.y, gridSizePixels, enabled),
  };
}
