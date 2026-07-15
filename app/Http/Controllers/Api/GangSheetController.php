<?php

namespace App\Http\Controllers\Api;

use App\Models\GangSheet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GangSheetController extends Controller
{
    /**
     * Get gang sheet details with calculated prices
     * GET /api/gang-sheets/{id}
     */
    public function show($id)
    {
        try {
            $gangSheet = GangSheet::findOrFail($id);
            
            // Verificar permisos: solo el propietario, admin, o si está en una orden
            if ($gangSheet->user_id && auth()->id() !== $gangSheet->user_id && !auth()->user()?->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // ⭐ CALCULAR PRECIO DINÁMICAMENTE (mismo que en save)
            $price = $this->calculateGangSheetPrice(
                $gangSheet->width,
                $gangSheet->height,
                $gangSheet->unit,
                $gangSheet->dpi,
                null // No tenemos tamaño de archivo en GET
            );

            // 🎟️ BUSCAR DESCUENTOS APLICABLES
            $discount = null;
            $discountedPrice = $price;
            
            if (auth()->check()) {
                $activePromotion = \App\Models\SheetSize::where('is_active', true)
                    ->whereNotNull('active_promotion_id')
                    ->whereHas('activePromotion', function ($q) {
                        $q->where('is_active', true)
                          ->where('start_date', '<=', now())
                          ->where('end_date', '>=', now());
                    })
                    ->first();
                
                if ($activePromotion && $activePromotion->activePromotion) {
                    $promotion = $activePromotion->activePromotion;
                    $discount = [
                        'type' => $promotion->discount_type,
                        'value' => $promotion->discount_value,
                        'description' => $promotion->description,
                    ];
                    
                    if ($promotion->discount_type === 'percentage') {
                        $discountedPrice = $price * (1 - ($promotion->discount_value / 100));
                    } else {
                        $discountedPrice = max(0, $price - $promotion->discount_value);
                    }
                }
            }

            // Construir respuesta con precios
            $gangSheetData = $gangSheet->toArray();
            $gangSheetData['original_price'] = round($price, 2);
            $gangSheetData['final_price'] = round($discountedPrice, 2);
            $gangSheetData['discount'] = $discount;

            // \Log::info('✅ Gang sheet retrieved with prices', [
            //     'id' => $id,
            //     'original_price' => $gangSheetData['original_price'],
            //     'final_price' => $gangSheetData['final_price'],
            //     'discount' => $discount,
            // ]);

            return response()->json([
                'success' => true,
                'data' => $gangSheetData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching gang sheet', ['id' => $id, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gang sheet not found',
            ], 404);
        }
    }

    /**
     * Download gang sheet as PNG file
     * GET /api/gang-sheets/{id}/download
     */
    public function download($id)
    {
        try {
            $gangSheet = GangSheet::findOrFail($id);
            
            // Verificar que existe la ruta del archivo
            if (!$gangSheet->final_path) {
                Log::warning('Gang sheet file path is empty', [
                    'id' => $id,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gang sheet file path not found',
                ], 404);
            }

            // Obtener la ruta completa del archivo
            $filePath = Storage::disk('local')->path($gangSheet->final_path);
            
            // Verificar que el archivo existe en el sistema de archivos
            if (!file_exists($filePath)) {
                Log::warning('Gang sheet file not found on disk', [
                    'id' => $id,
                    'full_path' => $filePath,
                    'relative_path' => $gangSheet->final_path,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gang sheet file not found on server',
                ], 404);
            }

            $fileName = "gang-sheet-{$gangSheet->id}-{$gangSheet->width}x{$gangSheet->height}in-{$gangSheet->dpi}dpi.png";

            // Usar response()->download() que es el método estándar y más eficiente
            // No intenta cargar el archivo completo en memoria
            return response()->download($filePath, $fileName, [
                'Content-Type' => 'image/png',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Gang sheet not found', ['id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gang sheet not found',
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error downloading gang sheet', [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error downloading gang sheet',
            ], 500);
        }
    }

    /**
     * Save gang sheet from canvas
     * POST /api/gang-sheets/save
     */
    public function save(Request $request)
    {
        try {
            // Log::info('💾 Gang sheet save request received', [
            //     'method' => $request->method(),
            //     'has_gang_sheet_image' => $request->hasFile('gang_sheet_image'),
            //     'all_fields' => array_keys($request->all()),
            // ]);

            $validated = $request->validate([
                'width' => 'required|numeric|min:1',
                'height' => 'required|numeric|min:1',
                'unit' => 'required|string|in:inches,cm,mm',
                'format' => 'required|string',
                'dpi' => 'required|integer|in:150,200,300',
                'gang_sheet_image' => 'required|file|mimes:png|max:100000', // 100MB max
                'images' => 'nullable|string', // JSON data
            ]);

            // Log::info('✅ Validation passed', ['validated_keys' => array_keys($validated)]);

            // Get authenticated user or use session
            $userId = auth()->id();

            // Store the PNG file
            $file = $request->file('gang_sheet_image');
            // Log::info('📦 File received', [
            //     'file_name' => $file->getClientOriginalName(),
            //     'file_size' => $file->getSize(),
            //     'file_mime' => $file->getMimeType(),
            // ]);
            
            $fileNameFormatted = sprintf(
                '%s-%.0fx%.0fin-%ddpi-%s.png',
                auth()->id() ? 'user-' . auth()->id() : 'anon-' . session()->getId(),
                $validated['width'],
                $validated['height'],
                $validated['dpi'],
                date('YmdHis')
            );

            // Log::info('💾 Storing file', [
            //     'directory' => 'gang-sheets',
            //     'file_name' => $fileNameFormatted,
            // ]);
            
            $path = $file->storeAs('gang-sheets', $fileNameFormatted, 'local');
            
            // Log::info('✅ Gang sheet PNG saved', [
            //     'path' => $path,
            // ]);

            // 💰 CALCULAR PRECIO DINÁMICAMENTE
            $price = $this->calculateGangSheetPrice(
                $validated['width'],
                $validated['height'],
                $validated['unit'],
                $validated['dpi'],
                $file->getSize()
            );

            // Log::info('💰 Price calculated', [
            //     'base_price' => $price,
            //     'width' => $validated['width'],
            //     'height' => $validated['height'],
            //     'dpi' => $validated['dpi'],
            // ]);

            // 🎟️ BUSCAR DESCUENTOS APLICABLES
            $discount = null;
            $discountedPrice = $price;
            
            if (auth()->check()) {
                // Buscar descuentos para usuario autenticado
                $activePromotion = \App\Models\SheetSize::where('is_active', true)
                    ->whereNotNull('active_promotion_id')
                    ->whereHas('activePromotion', function ($q) {
                        $q->where('is_active', true)
                          ->where('start_date', '<=', now())
                          ->where('end_date', '>=', now());
                    })
                    ->first();
                
                if ($activePromotion && $activePromotion->activePromotion) {
                    $promotion = $activePromotion->activePromotion;
                    $discount = [
                        'type' => $promotion->discount_type, // 'percentage' o 'fixed'
                        'value' => $promotion->discount_value,
                        'description' => $promotion->description,
                    ];
                    
                    if ($promotion->discount_type === 'percentage') {
                        $discountedPrice = $price * (1 - ($promotion->discount_value / 100));
                    } else {
                        $discountedPrice = max(0, $price - $promotion->discount_value);
                    }

                    // \Log::info('🎟️ Discount found', [
                    //     'discount_type' => $discount['type'],
                    //     'discount_value' => $discount['value'],
                    //     'original_price' => $price,
                    //     'discounted_price' => $discountedPrice,
                    // ]);
                }
            }

            // Create gang sheet record
            $gangSheet = GangSheet::create([
                'user_id' => $userId,
                'customer_id' => auth()->id(),
                'width' => $validated['width'],
                'height' => $validated['height'],
                'unit' => $validated['unit'],
                'dpi' => $validated['dpi'],
                'format' => $validated['format'],
                'final_path' => $path,
                'images_data' => $validated['images'] ? json_decode($validated['images'], true) : null,
                'status' => 'draft', // será 'completed' cuando se pague
                'price' => round($discountedPrice, 2), // Precio con descuento aplicado
            ]);

            // \Log::info('Gang sheet saved successfully', [
            //     'id' => $gangSheet->id,
            //     'user_id' => $userId,
            //     'path' => $path,
            //     'size' => $file->getSize(),
            //     'price_calculated' => $price,
            //     'price_with_discount' => $discountedPrice,
            //     'discount' => $discount,
            // ]);

            // ⭐ Construir respuesta explícitamente para asegurar que se incluyan todos los precios
            $gangSheetData = $gangSheet->toArray();
            
            // Garantizar que siempre estén presentes los precios calculados
            $gangSheetData['original_price'] = round($price, 2);
            $gangSheetData['final_price'] = round($discountedPrice, 2);
            $gangSheetData['discount'] = $discount;
            $gangSheetData['price'] = round($discountedPrice, 2);  // El precio en la BD es ya el con descuento

            // \Log::info('✅ Response data structure', [
            //     'gang_sheet_id' => $gangSheet->id,
            //     'has_original_price' => isset($gangSheetData['original_price']),
            //     'original_price' => $gangSheetData['original_price'] ?? null,
            //     'has_final_price' => isset($gangSheetData['final_price']),
            //     'final_price' => $gangSheetData['final_price'] ?? null,
            //     'has_discount' => isset($gangSheetData['discount']),
            //     'discount' => $gangSheetData['discount'] ?? null,
            //     'response_keys' => array_keys($gangSheetData),
            // ]);

            // \Log::info('📤 About to return response', [
            //     'status' => 201,
            //     'success' => true,
            //     'data_keys_count' => count($gangSheetData),
            //     'first_few_keys' => array_slice(array_keys($gangSheetData), 0, 10),
            // ]);

            $response = [
                'success' => true,
                'message' => 'Gang sheet saved successfully',
                'data' => $gangSheetData,
            ];

            // \Log::info('📝 Final response about to be sent', [
            //     'response_json' => json_encode($response),
            // ]);

            return response()->json($response, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error saving gang sheet', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error saving gang sheet', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving gang sheet: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calcular precio de un gang sheet basado en tamaño y DPI
     * 
     * Fórmula:
     * - Base: $15 (pago mínimo por trabajo de diseño)
     * - Por área: $0.01 por pulgada cuadrada
     * - Por DPI: multiplicador según calidad
     * - DPI 150: 1.0x (buena)
     * - DPI 200: 1.2x (excelente - recomendado)
     * - DPI 300: 1.5x (máxima)
     */
    private function calculateGangSheetPrice($width, $height, $unit, $dpi, $fileSize = null)
    {
        // Convertir a pulgadas si es necesario
        $widthInches = $unit === 'inches' ? $width : ($unit === 'cm' ? $width / 2.54 : $width / 25.4);
        $heightInches = $unit === 'inches' ? $height : ($unit === 'cm' ? $height / 2.54 : $height / 25.4);
        
        // Calcular área en pulgadas cuadradas
        $areaSquareInches = $widthInches * $heightInches;
        
        // Base price
        $basePrice = 15.00;
        
        // Price per square inch
        $pricePerSquareInch = 0.01;
        $areaCost = $areaSquareInches * $pricePerSquareInch;
        
        // DPI multiplier
        $dpiMultiplier = match($dpi) {
            150 => 1.0,    // Buena calidad
            200 => 1.2,    // Excelente (recomendado)
            300 => 1.5,    // Máxima calidad
            default => 1.0,
        };
        
        // File size surcharge (si es muy grande, cobrar más por procesamiento)
        $fileSizeSurcharge = 0;
        if ($fileSize) {
            $fileSizeMB = $fileSize / (1024 * 1024);
            if ($fileSizeMB > 100) {
                // $0.50 adicional por cada 10 MB sobre 100 MB
                $fileSizeSurcharge = ceil(($fileSizeMB - 100) / 10) * 0.50;
            }
        }
        
        // Calcular precio total
        $totalPrice = ($basePrice + $areaCost) * $dpiMultiplier + $fileSizeSurcharge;
        
        // \Log::info('Gang sheet price calculated', [
        //     'width' => $width,
        //     'height' => $height,
        //     'unit' => $unit,
        //     'dpi' => $dpi,
        //     'area_inches' => round($areaSquareInches, 2),
        //     'base_price' => $basePrice,
        //     'area_cost' => round($areaCost, 2),
        //     'dpi_multiplier' => $dpiMultiplier,
        //     'file_size_surcharge' => round($fileSizeSurcharge, 2),
        //     'final_price' => round($totalPrice, 2),
        // ]);
        
        return round($totalPrice, 2);
    }

    /**
     * Delete a gang sheet
     * DELETE /api/gang-sheets/{id}
     */
    public function destroy($id)
    {
        try {
            $gangSheet = GangSheet::findOrFail($id);
            
            // Verificar permisos: solo el propietario o admin
            if ($gangSheet->user_id && auth()->id() !== $gangSheet->user_id && !auth()->user()?->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Eliminar archivo si existe
            if ($gangSheet->final_path && Storage::disk('local')->exists($gangSheet->final_path)) {
                Storage::disk('local')->delete($gangSheet->final_path);
            }

            // Eliminar registro
            $gangSheet->delete();

            // Log::info('Gang sheet deleted', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Gang sheet deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Gang sheet not found for deletion', ['id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gang sheet not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting gang sheet', ['id' => $id, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting gang sheet: ' . $e->getMessage(),
            ], 500);
        }
    }
}
