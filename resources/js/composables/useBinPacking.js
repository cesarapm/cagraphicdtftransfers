/**
 * Advanced Bin Packing Algorithms for Gang Sheet Builder
 * 
 * This file contains improved algorithms for automatically arranging
 * images on a gang sheet to maximize space usage.
 */

import { ref, computed } from 'vue';

export function useBinPacking() {
  
  /**
   * MaxRects Algorithm (Best Short Side Fit)
   * This is one of the most efficient 2D bin packing algorithms
   */
  const maxRectsBSSF = (images, sheetWidth, sheetHeight, margin = 0.25, dpi = 72) => {
    const placedImages = [];
    const freeRectangles = [{
      x: 0,
      y: 0,
      width: sheetWidth * dpi,
      height: sheetHeight * dpi
    }];

    // Sort images by area (largest first)
    const sortedImages = [...images].sort((a, b) => {
      const areaA = (a.originalWidth || 300) * (a.originalHeight || 300);
      const areaB = (b.originalWidth || 300) * (b.originalHeight || 300);
      return areaB - areaA;
    });

    for (const uploadedImage of sortedImages) {
      // Default size: 4 inches, maintain aspect ratio
      const defaultWidthInches = 4;
      const aspectRatio = (uploadedImage.originalHeight || 300) / (uploadedImage.originalWidth || 300);
      const defaultHeightInches = defaultWidthInches * aspectRatio;
      
      const imgWidth = defaultWidthInches * dpi + (margin * dpi * 2);
      const imgHeight = defaultHeightInches * dpi + (margin * dpi * 2);

      // Find best rectangle using BSSF heuristic
      let bestRect = null;
      let bestShortSideFit = Infinity;
      let bestLongSideFit = Infinity;

      for (const rect of freeRectangles) {
        if (rect.width >= imgWidth && rect.height >= imgHeight) {
          const leftoverHoriz = rect.width - imgWidth;
          const leftoverVert = rect.height - imgHeight;
          const shortSideFit = Math.min(leftoverHoriz, leftoverVert);
          const longSideFit = Math.max(leftoverHoriz, leftoverVert);

          if (shortSideFit < bestShortSideFit || 
              (shortSideFit === bestShortSideFit && longSideFit < bestLongSideFit)) {
            bestRect = { ...rect };
            bestShortSideFit = shortSideFit;
            bestLongSideFit = longSideFit;
          }
        }

        // Also try rotated (if image is not square)
        if (Math.abs(imgWidth - imgHeight) > 1 && rect.width >= imgHeight && rect.height >= imgWidth) {
          const leftoverHoriz = rect.width - imgHeight;
          const leftoverVert = rect.height - imgWidth;
          const shortSideFit = Math.min(leftoverHoriz, leftoverVert);
          const longSideFit = Math.max(leftoverHoriz, leftoverVert);

          if (shortSideFit < bestShortSideFit || 
              (shortSideFit === bestShortSideFit && longSideFit < bestLongSideFit)) {
            bestRect = { ...rect, rotated: true };
            bestShortSideFit = shortSideFit;
            bestLongSideFit = longSideFit;
          }
        }
      }

      if (bestRect) {
        const actualWidth = bestRect.rotated ? imgHeight : imgWidth;
        const actualHeight = bestRect.rotated ? imgWidth : imgHeight;

        placedImages.push({
          id: Date.now() + Math.random(),
          x: bestRect.x + (margin * dpi),
          y: bestRect.y + (margin * dpi),
          width: actualWidth - (margin * dpi * 2),
          height: actualHeight - (margin * dpi * 2),
          imageObj: uploadedImage.imageObj,
          name: uploadedImage.name,
          rotated: bestRect.rotated || false,
        });

        // Split the used rectangle
        splitFreeRectangles(freeRectangles, bestRect, actualWidth, actualHeight);
      }
    }

    return placedImages;
  };

  /**
   * Split free rectangles after placing an image
   */
  const splitFreeRectangles = (freeRects, usedRect, usedWidth, usedHeight) => {
    const newRects = [];

    for (let i = freeRects.length - 1; i >= 0; i--) {
      const rect = freeRects[i];

      // If the rectangles intersect
      if (!(usedRect.x >= rect.x + rect.width || 
            usedRect.x + usedWidth <= rect.x ||
            usedRect.y >= rect.y + rect.height || 
            usedRect.y + usedHeight <= rect.y)) {
        
        // Remove the intersecting rectangle
        freeRects.splice(i, 1);

        // Create new rectangles from the split
        // Top
        if (usedRect.y > rect.y) {
          newRects.push({
            x: rect.x,
            y: rect.y,
            width: rect.width,
            height: usedRect.y - rect.y
          });
        }

        // Bottom
        if (usedRect.y + usedHeight < rect.y + rect.height) {
          newRects.push({
            x: rect.x,
            y: usedRect.y + usedHeight,
            width: rect.width,
            height: rect.y + rect.height - (usedRect.y + usedHeight)
          });
        }

        // Left
        if (usedRect.x > rect.x) {
          newRects.push({
            x: rect.x,
            y: rect.y,
            width: usedRect.x - rect.x,
            height: rect.height
          });
        }

        // Right
        if (usedRect.x + usedWidth < rect.x + rect.width) {
          newRects.push({
            x: usedRect.x + usedWidth,
            y: rect.y,
            width: rect.x + rect.width - (usedRect.x + usedWidth),
            height: rect.height
          });
        }
      }
    }

    freeRects.push(...newRects);
    
    // Remove redundant rectangles
    pruneFreeRectangles(freeRects);
  };

  /**
   * Remove rectangles that are completely contained within other rectangles
   */
  const pruneFreeRectangles = (freeRects) => {
    for (let i = freeRects.length - 1; i >= 0; i--) {
      for (let j = freeRects.length - 1; j >= 0; j--) {
        if (i !== j) {
          if (isContainedIn(freeRects[i], freeRects[j])) {
            freeRects.splice(i, 1);
            break;
          }
        }
      }
    }
  };

  /**
   * Check if rectangle A is contained within rectangle B
   */
  const isContainedIn = (a, b) => {
    return a.x >= b.x && 
           a.y >= b.y &&
           a.x + a.width <= b.x + b.width &&
           a.y + a.height <= b.y + b.height;
  };

  /**
   * Guillotine Algorithm (simpler but still effective)
   */
  const guillotineAlgorithm = (images, sheetWidth, sheetHeight, margin = 0.25, dpi = 72) => {
    const placedImages = [];
    const freeRects = [{
      x: 0,
      y: 0,
      width: sheetWidth * dpi,
      height: sheetHeight * dpi
    }];

    const sortedImages = [...images].sort((a, b) => {
      const heightA = a.originalHeight || 300;
      const heightB = b.originalHeight || 300;
      return heightB - heightA;
    });

    for (const uploadedImage of sortedImages) {
      const defaultWidthInches = 4;
      const aspectRatio = (uploadedImage.originalHeight || 300) / (uploadedImage.originalWidth || 300);
      const defaultHeightInches = defaultWidthInches * aspectRatio;
      
      const imgWidth = defaultWidthInches * dpi + (margin * dpi * 2);
      const imgHeight = defaultHeightInches * dpi + (margin * dpi * 2);

      // Find first fitting rectangle
      let placed = false;
      for (let i = 0; i < freeRects.length; i++) {
        const rect = freeRects[i];

        if (rect.width >= imgWidth && rect.height >= imgHeight) {
          placedImages.push({
            id: Date.now() + Math.random(),
            x: rect.x + (margin * dpi),
            y: rect.y + (margin * dpi),
            width: imgWidth - (margin * dpi * 2),
            height: imgHeight - (margin * dpi * 2),
            imageObj: uploadedImage.imageObj,
            name: uploadedImage.name,
          });

          // Split the rectangle
          freeRects.splice(i, 1);

          // Add two new rectangles (guillotine cut)
          const horizontalCut = rect.width - imgWidth < rect.height - imgHeight;

          if (horizontalCut) {
            // Cut horizontally
            if (rect.height - imgHeight > 0) {
              freeRects.push({
                x: rect.x,
                y: rect.y + imgHeight,
                width: rect.width,
                height: rect.height - imgHeight
              });
            }
            if (rect.width - imgWidth > 0) {
              freeRects.push({
                x: rect.x + imgWidth,
                y: rect.y,
                width: rect.width - imgWidth,
                height: imgHeight
              });
            }
          } else {
            // Cut vertically
            if (rect.width - imgWidth > 0) {
              freeRects.push({
                x: rect.x + imgWidth,
                y: rect.y,
                width: rect.width - imgWidth,
                height: rect.height
              });
            }
            if (rect.height - imgHeight > 0) {
              freeRects.push({
                x: rect.x,
                y: rect.y + imgHeight,
                width: imgWidth,
                height: rect.height - imgHeight
              });
            }
          }

          placed = true;
          break;
        }
      }

      if (!placed) {
        console.warn('Could not place image:', uploadedImage.name);
      }
    }

    return placedImages;
  };

  /**
   * Simple Shelf Algorithm (row-based packing)
   */
  const shelfAlgorithm = (images, sheetWidth, sheetHeight, margin = 0.25, dpi = 72) => {
    const placedImages = [];
    let currentX = margin * dpi;
    let currentY = margin * dpi;
    let rowHeight = 0;

    const sortedImages = [...images].sort((a, b) => {
      const heightA = a.originalHeight || 300;
      const heightB = b.originalHeight || 300;
      return heightB - heightA;
    });

    for (const uploadedImage of sortedImages) {
      const defaultWidthInches = 4;
      const aspectRatio = (uploadedImage.originalHeight || 300) / (uploadedImage.originalWidth || 300);
      const defaultHeightInches = defaultWidthInches * aspectRatio;
      
      const imgWidth = defaultWidthInches * dpi;
      const imgHeight = defaultHeightInches * dpi;
      const marginPx = margin * dpi;

      // Check if image fits in current row
      if (currentX + imgWidth + marginPx > sheetWidth * dpi) {
        // Move to next row
        currentX = marginPx;
        currentY += rowHeight + marginPx;
        rowHeight = 0;
      }

      // Check if image fits vertically
      if (currentY + imgHeight + marginPx <= sheetHeight * dpi) {
        placedImages.push({
          id: Date.now() + Math.random(),
          x: currentX,
          y: currentY,
          width: imgWidth,
          height: imgHeight,
          imageObj: uploadedImage.imageObj,
          name: uploadedImage.name,
        });

        currentX += imgWidth + marginPx;
        rowHeight = Math.max(rowHeight, imgHeight);
      } else {
        console.warn('Image does not fit on sheet:', uploadedImage.name);
      }
    }

    return placedImages;
  };

  /**
   * Calculate efficiency metrics
   */
  const calculateEfficiency = (placedImages, sheetWidth, sheetHeight, dpi = 72) => {
    const totalSheetArea = sheetWidth * sheetHeight;
    
    let usedArea = 0;
    placedImages.forEach(img => {
      const widthInches = img.width / dpi;
      const heightInches = img.height / dpi;
      usedArea += widthInches * heightInches;
    });

    const efficiency = (usedArea / totalSheetArea) * 100;
    const wastedArea = totalSheetArea - usedArea;
    
    return {
      efficiency: efficiency.toFixed(2),
      usedArea: usedArea.toFixed(2),
      wastedArea: wastedArea.toFixed(2),
      totalArea: totalSheetArea.toFixed(2),
      imageCount: placedImages.length,
    };
  };

  return {
    maxRectsBSSF,
    guillotineAlgorithm,
    shelfAlgorithm,
    calculateEfficiency,
  };
}
