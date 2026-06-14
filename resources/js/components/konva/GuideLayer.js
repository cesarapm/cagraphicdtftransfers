import Konva from 'konva';

/**
 * GuideLayer para mostrar guías de snap visualmente
 * Muestra líneas temporales cuando el usuario está alineando objetos
 */
export function createGuideLayer(config) {
  const {
    guides = [],          // Array de { type, position, label }
    canvasWidthInches,
    canvasHeightInches,
    editorPPI = 10,
    zoom = 1,
  } = config;

  const inchesToPixels = (inches) => inches * editorPPI * zoom;
  const width = inchesToPixels(canvasWidthInches);
  const height = inchesToPixels(canvasHeightInches);

  return new Konva.Shape({
    sceneFunc: (context, shape) => {
      if (!guides || guides.length === 0) return;

      guides.forEach(guide => {
        const position = inchesToPixels(guide.position);

        // Estilo de la guía
        context.strokeStyle = '#3b82f6'; // Azul
        context.lineWidth = 1 / zoom;
        context.setLineDash([5 / zoom, 5 / zoom]);

        context.beginPath();
        
        if (guide.type === 'vertical') {
          // Línea vertical
          context.moveTo(position, 0);
          context.lineTo(position, height);
        } else {
          // Línea horizontal
          context.moveTo(0, position);
          context.lineTo(width, position);
        }
        
        context.stroke();

        // Etiqueta opcional
        if (guide.label && zoom > 0.5) {
          context.fillStyle = '#3b82f6';
          context.fillRect(
            guide.type === 'vertical' ? position + 5 / zoom : 5 / zoom,
            guide.type === 'vertical' ? 5 / zoom : position + 5 / zoom,
            60 / zoom,
            20 / zoom
          );

          context.fillStyle = 'white';
          context.font = `${12 / zoom}px Arial, sans-serif`;
          context.textAlign = 'left';
          context.textBaseline = 'top';
          context.fillText(
            guide.label,
            guide.type === 'vertical' ? position + 10 / zoom : 10 / zoom,
            guide.type === 'vertical' ? 8 / zoom : position + 8 / zoom
          );
        }
      });

      context.setLineDash([]);
      context.fillStrokeShape(shape);
    },
    hitFunc: (context, shape) => {
      // No clickeable
      context.beginPath();
      context.rect(0, 0, width, height);
      context.closePath();
      context.fillStrokeShape(shape);
    },
    listening: false, // No intercepta eventos
  });
}

/**
 * Función de utilidad para crear una línea de guía temporal
 */
export function createTemporaryGuide(type, positionInches, label = '') {
  return {
    type,           // 'vertical' | 'horizontal'
    position: positionInches,
    label,
    timestamp: Date.now(),
  };
}
