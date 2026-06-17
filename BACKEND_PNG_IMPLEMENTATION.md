# Backend Implementation Guide - PNG Transparente para DTF

## 🔧 Controlador Laravel Actualizado

### Guardar Gang Sheet con Transparencia

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\GangSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GangSheetController extends Controller
{
    /**
     * Guardar gang sheet con soporte para PNG transparente
     */
    public function save(Request $request)
    {
        try {
            Log::info('💾 Guardando gang sheet...', [
                'format' => $request->input('format'),
                'dpi' => $request->input('dpi'),
                'width' => $request->input('width'),
                'height' => $request->input('height'),
            ]);

            // Validación
            $validated = $request->validate([
                'width' => 'required|numeric|min:1',
                'height' => 'required|numeric|min:1',
                'unit' => 'required|in:inches,cm,mm',
                'format' => 'nullable|in:png,jpg', // Format indica tipo de transparencia
                'dpi' => 'nullable|numeric|in:150,200,300',
                'images' => 'required|json',
                'gang_sheet_image' => 'nullable|image|mimes:png,jpg,jpeg',
                'image_files' => 'nullable|array',
                'image_files.*' => 'nullable|image|mimes:png,jpg,jpeg,svg',
            ]);

            // Crear registro de gang sheet
            $gangSheet = GangSheet::create([
                'width' => $validated['width'],
                'height' => $validated['height'],
                'unit' => $validated['unit'],
                'format' => $validated['format'] ?? 'png', // Default PNG
                'dpi' => $validated['dpi'] ?? 200,
                'images_metadata' => $validated['images'],
                'user_id' => auth()->id(),
            ]);

            Log::info('✅ Gang sheet creado', ['id' => $gangSheet->id]);

            // ⭐ Procesar imagen compilada (PNG transparente)
            if ($request->hasFile('gang_sheet_image')) {
                $this->saveGangSheetImage($gangSheet, $request->file('gang_sheet_image'));
            }

            // Procesar imágenes originales como respaldo
            if ($request->hasFile('image_files')) {
                $this->saveOriginalImages($gangSheet, $request->file('image_files'));
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Gang sheet guardado exitosamente',
                'data' => [
                    'id' => $gangSheet->id,
                    'url' => $gangSheet->getImageUrl(),
                    'format' => $gangSheet->format,
                    'dpi' => $gangSheet->dpi,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('❌ Error guardando gang sheet:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar gang sheet: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Guardar imagen compilada del gang sheet
     * ⭐ CRÍTICO: Preservar PNG transparente
     */
    private function saveGangSheetImage(GangSheet $gangSheet, $imageFile)
    {
        try {
            Log::info('📸 Guardando imagen del gang sheet...', [
                'original_name' => $imageFile->getClientOriginalName(),
                'size' => $imageFile->getSize(),
                'mime' => $imageFile->getMimeType(),
            ]);

            // ⭐ Validar que sea PNG si format es 'png'
            if ($gangSheet->format === 'png') {
                if ($imageFile->getMimeType() !== 'image/png') {
                    throw new \Exception('Formato esperado: PNG, recibido: ' . $imageFile->getMimeType());
                }
                Log::info('✅ Validado: Es PNG');
            }

            // Generar nombre de archivo
            $filename = "gang-sheet-{$gangSheet->id}.{$this->getExtension($gangSheet->format)}";
            $path = "gang-sheets/{$gangSheet->id}";

            // ⭐ Guardar archivo preservando formato original
            // NO procesar, NO convertir, NO modificar
            $stored = Storage::disk('public')->putFileAs(
                $path,
                $imageFile,
                $filename,
                'public'
            );

            Log::info('✅ Archivo guardado', [
                'path' => $stored,
                'format' => $gangSheet->format,
            ]);

            // Actualizar modelo con ruta
            $gangSheet->update([
                'image_path' => $stored,
            ]);

            // ⭐ Verificar integridad del PNG transparente
            if ($gangSheet->format === 'png') {
                $this->validatePngTransparency($gangSheet);
            }

        } catch (\Exception $e) {
            Log::error('❌ Error guardando imagen:', [
                'error' => $e->getMessage(),
                'gang_sheet_id' => $gangSheet->id,
            ]);
            throw $e;
        }
    }

    /**
     * Guardar imágenes originales como respaldo
     */
    private function saveOriginalImages(GangSheet $gangSheet, $files)
    {
        try {
            Log::info('📁 Guardando imágenes originales...', [
                'count' => count($files),
            ]);

            $path = "gang-sheets/{$gangSheet->id}/originals";

            foreach ($files as $index => $file) {
                if (!$file) continue;

                $filename = $index . '_' . $file->getClientOriginalName();

                Storage::disk('public')->putFileAs(
                    $path,
                    $file,
                    $filename,
                    'public'
                );

                Log::info("✓ Imagen original {$index} guardada", [
                    'name' => $filename,
                    'size' => $file->getSize(),
                ]);
            }

        } catch (\Exception $e) {
            Log::warning('⚠️ Error guardando imágenes originales (no crítico):', [
                'error' => $e->getMessage(),
            ]);
            // No fallar el proceso completo
        }
    }

    /**
     * ⭐ Verificar que PNG tiene canal alpha (transparencia)
     */
    private function validatePngTransparency(GangSheet $gangSheet)
    {
        try {
            $imagePath = Storage::disk('public')->path($gangSheet->image_path);
            
            // Leer cabecera PNG
            $fp = fopen($imagePath, 'rb');
            if (!$fp) {
                throw new \Exception('No se puede leer archivo PNG');
            }

            // PNG signature (primeros 8 bytes)
            $signature = fread($fp, 8);
            if ($signature !== "\x89PNG\r\n\x1a\n") {
                throw new \Exception('Archivo no es PNG válido');
            }

            // Leer chunk IHDR (color type)
            fread($fp, 4); // length
            $chunkType = fread($fp, 4);
            
            if ($chunkType === 'IHDR') {
                fread($fp, 4); // width
                fread($fp, 4); // height
                fread($fp, 1); // bit depth
                $colorType = ord(fread($fp, 1));
                
                // Color types:
                // 0 = Grayscale
                // 2 = RGB
                // 3 = Indexed
                // 4 = Grayscale + Alpha
                // 6 = RGBA (con transparencia) ✅
                
                $hasTransparency = in_array($colorType, [4, 6]);
                
                Log::info('🔍 Análisis PNG:', [
                    'color_type' => $colorType,
                    'has_transparency' => $hasTransparency,
                    'description' => $this->getColorTypeDescription($colorType),
                ]);

                if (!$hasTransparency) {
                    Log::warning('⚠️ PNG sin canal alpha detectado', [
                        'gang_sheet_id' => $gangSheet->id,
                        'color_type' => $colorType,
                    ]);
                }
            }

            fclose($fp);

        } catch (\Exception $e) {
            Log::warning('⚠️ No se pudo verificar PNG: ' . $e->getMessage());
            // No fallar el proceso
        }
    }

    /**
     * Obtener descripción del tipo de color PNG
     */
    private function getColorTypeDescription($colorType)
    {
        $descriptions = [
            0 => 'Grayscale',
            2 => 'RGB',
            3 => 'Indexed (Palette)',
            4 => 'Grayscale + Alpha',
            6 => 'RGBA (Con Transparencia) ✅',
        ];

        return $descriptions[$colorType] ?? 'Desconocido';
    }

    /**
     * Obtener extensión según formato
     */
    private function getExtension($format)
    {
        return $format === 'png' ? 'png' : 'jpg';
    }

    /**
     * Obtener gang sheet con validaciones
     */
    public function show($id)
    {
        $gangSheet = GangSheet::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $gangSheet->id,
                'width' => $gangSheet->width,
                'height' => $gangSheet->height,
                'unit' => $gangSheet->unit,
                'format' => $gangSheet->format,
                'dpi' => $gangSheet->dpi,
                'url' => $gangSheet->getImageUrl(),
                'image_path' => $gangSheet->image_path,
                'images_metadata' => json_decode($gangSheet->images_metadata, true),
                'created_at' => $gangSheet->created_at,
            ]
        ]);
    }

    /**
     * Descargar PNG transparente
     */
    public function download($id)
    {
        $gangSheet = GangSheet::findOrFail($id);

        // ⭐ Validar que es PNG
        if ($gangSheet->format !== 'png') {
            return response()->json([
                'error' => 'Gang sheet no está en formato PNG transparente'
            ], 400);
        }

        $path = Storage::disk('public')->path($gangSheet->image_path);

        if (!file_exists($path)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }

        // Servir PNG con headers correctos para preservar transparencia
        return response()->download($path, null, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
```

---

## 🗄️ Modelo GangSheet Actualizado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GangSheet extends Model
{
    protected $fillable = [
        'width',
        'height',
        'unit',
        'format', // ⭐ 'png' o 'jpg'
        'dpi',
        'image_path',
        'images_metadata',
        'user_id',
    ];

    protected $casts = [
        'images_metadata' => 'json',
    ];

    /**
     * Relación con usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ⭐ Obtener URL de la imagen preservando transparencia
     */
    public function getImageUrl()
    {
        if (!$this->image_path) {
            return null;
        }

        // ⭐ IMPORTANTE: Usar URL pública sin procesar
        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * Verificar si tiene transparencia
     */
    public function hasTransparency()
    {
        return $this->format === 'png';
    }

    /**
     * Obtener información de resolución
     */
    public function getResolutionInfo()
    {
        $dpi = $this->dpi ?? 200;

        return [
            'width_inches' => $this->width,
            'height_inches' => $this->height,
            'width_pixels' => (int)round($this->width * $dpi),
            'height_pixels' => (int)round($this->height * $dpi),
            'megapixels' => round(($this->width * $dpi * $this->height * $dpi) / 1000000, 2),
            'dpi' => $dpi,
        ];
    }

    /**
     * Obtener información de imágenes
     */
    public function getImagesInfo()
    {
        if (!$this->images_metadata) {
            return [];
        }

        $metadata = is_string($this->images_metadata) 
            ? json_decode($this->images_metadata, true) 
            : $this->images_metadata;

        return array_map(function ($img) {
            return [
                'index' => $img['index'] ?? 0,
                'name' => $img['name'] ?? 'Unknown',
                'position' => [
                    'x_inches' => $img['x'] ?? 0,
                    'y_inches' => $img['y'] ?? 0,
                ],
                'size' => [
                    'width_inches' => $img['width'] ?? 0,
                    'height_inches' => $img['height'] ?? 0,
                    'original_width' => $img['originalWidth'] ?? 0,
                    'original_height' => $img['originalHeight'] ?? 0,
                ],
            ];
        }, $metadata);
    }
}
```

---

## 🗃️ Migración de Base de Datos

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gang_sheets', function (Blueprint $table) {
            $table->id();
            
            // Dimensiones
            $table->decimal('width', 10, 2);
            $table->decimal('height', 10, 2);
            $table->enum('unit', ['inches', 'cm', 'mm'])->default('inches');
            
            // ⭐ Formato (png para transparencia, jpg para fondo sólido)
            $table->enum('format', ['png', 'jpg'])->default('png');
            
            // ⭐ DPI usado en la exportación
            $table->integer('dpi')->default(200);
            
            // Rutas de archivos
            $table->string('image_path')->nullable();
            
            // Metadata de imágenes en JSON
            $table->json('images_metadata')->nullable();
            
            // Usuario propietario
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('user_id');
            $table->index('format');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gang_sheets');
    }
};
```

---

## 🛣️ Rutas de API

```php
<?php

// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    
    // Gang Sheets
    Route::prefix('gang-sheets')->group(function () {
        
        // ⭐ Guardar gang sheet con PNG transparente
        Route::post('save', [GangSheetController::class, 'save'])
            ->name('gang-sheets.save');
        
        // Obtener detalles
        Route::get('{id}', [GangSheetController::class, 'show'])
            ->name('gang-sheets.show');
        
        // ⭐ Descargar PNG transparente
        Route::get('{id}/download', [GangSheetController::class, 'download'])
            ->name('gang-sheets.download');
        
        // Listar gang sheets del usuario
        Route::get('user/list', [GangSheetController::class, 'userList'])
            ->name('gang-sheets.list');
        
        // Eliminar
        Route::delete('{id}', [GangSheetController::class, 'destroy'])
            ->name('gang-sheets.destroy');
    });
});
```

---

## 📝 Logging Sugerido

```php
// Para monitorear correctamente los PNG transparentes

Log::info('🎨 Procesamiento de Gang Sheet', [
    'id' => $gangSheet->id,
    'format' => 'PNG (transparente)',
    'width' => $gangSheet->width,
    'height' => $gangSheet->height,
    'dpi' => $gangSheet->dpi,
    'file_size_mb' => filesize($path) / 1024 / 1024,
    'has_transparency' => $gangSheet->hasTransparency(),
]);
```

---

## ⚠️ Checklist de Implementación Backend

- [ ] Validar que `format` sea 'png' en controller
- [ ] NO convertir PNG a JPEG
- [ ] NO aplicar procesamiento de imagen
- [ ] Guardar PNG original sin modificaciones
- [ ] Preservar canal alpha en todo momento
- [ ] Validar que PNG tiene tipo de color 6 (RGBA)
- [ ] Servir PNG con header `Content-Type: image/png`
- [ ] Documentar que PNG debe ser transparente
- [ ] Incluir DPI en metadata
- [ ] Crear logs detallados

---

## 🚀 Testing

### Probar con cURL:

```bash
#!/bin/bash

# Crear gang sheet con PNG transparente
curl -X POST http://localhost:8000/api/gang-sheets/save \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: multipart/form-data" \
  -F "width=264" \
  -F "height=120" \
  -F "unit=inches" \
  -F "format=png" \
  -F "dpi=200" \
  -F "images=[...]" \
  -F "gang_sheet_image=@/path/to/gang-sheet.png"
```

### Verificar PNG en servidor:

```bash
# Ver tipo de color PNG
file /path/to/gang-sheet.png

# Debe mostrar algo como:
# PNG image data, 52800 x 24000, 8-bit/color RGBA
#                                              ^^^^ = Con transparencia
```

---

**Última actualización:** 2026-06-17  
**Version:** 1.0  
**Status:** ✅ Implementado
