import Konva from 'konva';

/**
 * Optimized Ruler Component para Konva - REFACTORIZADO
 * Usa EDITOR_PPI y unidades reales en pulgadas
 * Ruler tipo Photoshop adaptativo según zoom
 * Dibuja usando Canvas API directamente
 */
export function createRuler(config) {
  const {
    orientation = 'horizontal', // 'horizontal' | 'vertical'
    sizeInches,                 // Tamaño del canvas en pulgadas
    rulerHeight = 30,           // Alto/ancho del ruler en píxeles (fijo)
    editorPPI = 10,             // Píxeles por pulgada en el EDITOR
    zoom = 1,                   // Nivel de zoom actual
    rulerDetail = null,         // { showFeet, showInches, showHalfInches, showQuarterInches }
  } = config;

  const isHorizontal = orientation === 'horizontal';
  const inchesToPixels = (inches) => inches * editorPPI * zoom;
  const pixelsToInches = (pixels) => pixels / (editorPPI * zoom);
  
  const rulerLength = inchesToPixels(sizeInches);
  const width = isHorizontal ? rulerLength : rulerHeight;
  const height = isHorizontal ? rulerHeight : rulerLength;

  return new Konva.Shape({
    sceneFunc: (context, shape) => {
      // Fondo del ruler
      context.fillStyle = '#f3f4f6';
      context.fillRect(0, 0, width, height);
      
      // Borde
      context.strokeStyle = '#d1d5db';
      context.lineWidth = 1;
      context.strokeRect(0, 0, width, height);

      // Determinar nivel de detalle (si no se proporciona)
      const detail = rulerDetail || {
        showFeet: true,
        showInches: zoom >= 0.5,
        showHalfInches: zoom >= 2,
        showQuarterInches: zoom >= 4,
      };

      const pixelsPerInch = editorPPI * zoom;
      const pixelsPerFoot = pixelsPerInch * 12;

      // Dibujar marcas de PIES
      if (detail.showFeet) {
        const totalFeet = Math.ceil(sizeInches / 12);
        
        for (let feet = 0; feet <= totalFeet; feet++) {
          const positionInches = feet * 12;
          const position = inchesToPixels(positionInches);
          
          if (position > rulerLength) break;

          // Línea de marca
          context.strokeStyle = '#374151';
          context.lineWidth = 2;
          context.beginPath();
          
          if (isHorizontal) {
            context.moveTo(position, 0);
            context.lineTo(position, 20);
          } else {
            context.moveTo(0, position);
            context.lineTo(20, position);
          }
          context.stroke();

          // Texto de pies (solo si hay espacio)
          if (pixelsPerFoot > 40) {
            context.fillStyle = '#1f2937';
            context.font = 'bold 12px Arial, sans-serif';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            
            const text = feet + "'";
            
            if (isHorizontal) {
              context.fillText(text, position, rulerHeight / 2);
            } else {
              context.save();
              context.translate(rulerHeight / 2, position);
              context.rotate(-Math.PI / 2);
              context.fillText(text, 0, 0);
              context.restore();
            }
          }
        }
      }

      // Dibujar marcas de PULGADAS
      if (detail.showInches) {
        const totalInches = Math.ceil(sizeInches);
        
        for (let inch = 0; inch <= totalInches; inch++) {
          // Saltar pulgadas que son pies exactos
          if (inch % 12 === 0) continue;
          
          const position = inchesToPixels(inch);
          if (position > rulerLength) break;

          // Línea de marca
          context.strokeStyle = '#6b7280';
          context.lineWidth = 1;
          context.beginPath();
          
          if (isHorizontal) {
            context.moveTo(position, 0);
            context.lineTo(position, 15);
          } else {
            context.moveTo(0, position);
            context.lineTo(15, position);
          }
          context.stroke();

          // Texto de pulgadas (mostrar cada 6 pulgadas o si hay espacio)
          const shouldShowText = (inch % 6 === 0 && pixelsPerInch > 10) || pixelsPerInch > 20;
          
          if (shouldShowText) {
            context.fillStyle = '#4b5563';
            context.font = '9px Arial, sans-serif';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            
            // Mostrar número absoluto de pulgada (ej: 18" en lugar de solo 6")
            const text = inch + '"';
            
            if (isHorizontal) {
              context.fillText(text, position, rulerHeight - 8);
            } else {
              context.save();
              context.translate(rulerHeight - 8, position);
              context.rotate(-Math.PI / 2);
              context.fillText(text, 0, 0);
              context.restore();
            }
          }
        }
      }

      // Dibujar marcas de MEDIAS PULGADAS
      if (detail.showHalfInches) {
        const totalHalfInches = Math.ceil(sizeInches * 2);
        
        for (let halfInch = 0; halfInch <= totalHalfInches; halfInch++) {
          // Saltar medias pulgadas que son pulgadas completas
          if (halfInch % 2 === 0) continue;
          
          const positionInches = halfInch * 0.5;
          const position = inchesToPixels(positionInches);
          if (position > rulerLength) break;

          // Línea de marca
          context.strokeStyle = '#9ca3af';
          context.lineWidth = 0.5;
          context.beginPath();
          
          if (isHorizontal) {
            context.moveTo(position, 0);
            context.lineTo(position, 10);
          } else {
            context.moveTo(0, position);
            context.lineTo(10, position);
          }
          context.stroke();
        }
      }

      // Dibujar marcas de CUARTOS DE PULGADA
      if (detail.showQuarterInches) {
        const totalQuarterInches = Math.ceil(sizeInches * 4);
        
        for (let quarterInch = 0; quarterInch <= totalQuarterInches; quarterInch++) {
          // Saltar cuartos que son medias o pulgadas completas
          if (quarterInch % 2 === 0) continue;
          
          const positionInches = quarterInch * 0.25;
          const position = inchesToPixels(positionInches);
          if (position > rulerLength) break;

          // Línea de marca
          context.strokeStyle = '#d1d5db';
          context.lineWidth = 0.5;
          context.beginPath();
          
          if (isHorizontal) {
            context.moveTo(position, 0);
            context.lineTo(position, 6);
          } else {
            context.moveTo(0, position);
            context.lineTo(6, position);
          }
          context.stroke();
        }
      }
      
      context.fillStrokeShape(shape);
    },
    hitFunc: (context, shape) => {
      context.beginPath();
      context.rect(0, 0, width, height);
      context.closePath();
      context.fillStrokeShape(shape);
    },
  });
}
