<?php

namespace App\Http\Controllers;

use App\Models\GangSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class GangSheetController extends Controller
{
    /**
     * Get all gang sheets for the authenticated customer
     */
    public function index(Request $request)
    {
        $gangSheets = GangSheet::with(['customer', 'order'])
            ->where('customer_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($gangSheets);
    }

    /**
     * Get a specific gang sheet (must belong to authenticated customer)
     */
    public function show($id)
    {
        $gangSheet = GangSheet::with(['customer', 'order'])
            ->where('customer_id', auth()->id())
            ->findOrFail($id);
        
        return response()->json($gangSheet);
    }

    /**
     * Save a new gang sheet (can be anonymous or authenticated)
     */
    public function store(Request $request)
    {
        // \Log::info("=== STORE REQUEST START ===");
        // \Log::info("Has image_files: " . ($request->hasFile('image_files') ? 'YES' : 'NO'));
        if ($request->hasFile('image_files')) {
            // \Log::info("Number of image_files: " . count($request->file('image_files')));
        }
        
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'width' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
            'unit' => 'nullable|in:feet,inches',
            'images' => 'required|string', // JSON string of images
            'image_files' => 'nullable|array', // Image files from multipart
            'image_files.*' => 'nullable|file|image|max:50000', // Each file max 50MB
            'preview' => 'nullable|string', // Base64 preview
            'order_id' => 'nullable|exists:orders,id',
            'notes' => 'nullable|string',
        ]);

        // Decode images from JSON string
        $imagesData = json_decode($validated['images'], true);
        // \Log::info("Decoded images count: " . count($imagesData ?? []));
        
        if (!is_array($imagesData) || empty($imagesData)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid images data. Must be non-empty array.',
            ], 422);
        }

        // Process uploaded image files and store them
        if ($request->hasFile('image_files')) {
            $imageFiles = $request->file('image_files');
            $storagePaths = [];
            $basePath = 'gang-sheets/' . uniqid() . '/'; // Single directory for all images
            // \Log::info("Created basePath: {$basePath}");
            
            foreach ($imageFiles as $index => $file) {
                if ($file && $file->isValid()) {
                    try {
                        // Generate unique filename
                        $filename = 'image_' . uniqid() . '_' . $index . '.' . $file->getClientOriginalExtension();
                        
                        // Store file
                        $storagePath = $file->storeAs($basePath, $filename, 'public');
                        $storagePaths[$index] = $storagePath;
                        
                        // Log storage
                        // \Log::info("Stored image[{$index}]: {$storagePath} (basePath={$basePath})");
                    } catch (\Exception $e) {
                        \Log::warning("Failed to store image file at index {$index}: " . $e->getMessage());
                    }
                }
            }
            
            // Add storage paths to images data instead of base64
            foreach ($imagesData as $index => &$imageData) {
                if (isset($storagePaths[$index])) {
                    // Store both path and remove any base64 data
                    $imageData['path'] = $storagePaths[$index];
                    unset($imageData['base64']);
                    // \Log::info("Assigned path to image[{$index}]: " . $storagePaths[$index]);
                }
            }
        }
        
        $validated['images'] = $imagesData;

        try {
            // Get unit early for consistent use throughout
            $unit = $validated['unit'] ?? 'inches';
            
            // Save preview image - with size validation and compression
            $previewPath = null;
            if (!empty($validated['preview'])) {
                // Extract base64 data
                $imageData = $validated['preview'];
                if (strpos($imageData, 'data:image') === 0) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                }
                
                // Check size before decoding (base64 is ~33% larger than binary)
                $binarySize = strlen(base64_decode($imageData, true));
                
                // Different limits for feet (larger) vs inches (smaller)
                $maxSize = $unit === 'feet' ? 400000000 : 40000000; // 400MB for feet, 40MB for inches
                
                if ($binarySize > 0 && $binarySize < $maxSize) {
                    $imageContent = base64_decode($imageData);
                    if ($imageContent !== false) {
                        // Determine extension based on unit (PNG for feet, JPEG for inches)
                        $extension = $unit === 'feet' ? 'png' : 'jpg';
                        
                        $filename = 'preview_' . uniqid() . '.' . $extension;
                        $path = 'previews/' . $filename;
                        Storage::disk('public')->put($path, $imageContent);
                        $previewPath = $path;
                    }
                }
            }

            // Calculate total area
            $totalArea = 0;
            foreach ($validated['images'] as $image) {
                $totalArea += ($image['width'] ?? 0) * ($image['height'] ?? 0);
            }

            // DPI: 150 DPI for professional DTF printing quality
            // 150 DPI = 1.8 GB memory for 22x10ft sheet (manageable)
            $dpi = 150;

            $gangSheet = GangSheet::create([
                'customer_id' => auth()->id() ?? null, // Allow anonymous saves
                'order_id' => $validated['order_id'] ?? null,
                'name' => $validated['name'] ?? 'Gang Sheet ' . now()->format('Y-m-d H:i'),
                'width' => $validated['width'],
                'height' => $validated['height'],
                'unit' => $unit,
                'dpi' => $dpi, // Fixed 100 DPI
                'images_data' => $validated['images'],
                'preview_path' => $previewPath,
                'total_area' => $totalArea,
                'image_count' => count($validated['images']),
                'status' => 'draft',
                'notes' => $validated['notes'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gang sheet saved successfully',
                'data' => $gangSheet,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving gang sheet: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing gang sheet (must belong to authenticated customer)
     */
    public function update(Request $request, $id)
    {
        $gangSheet = GangSheet::where('customer_id', auth()->id())->findOrFail($id);

        // Check if gang sheet can be edited
        if (!$gangSheet->canBeEdited()) {
            return response()->json([
                'success' => false,
                'message' => 'Este Gang Sheet ya no puede ser editado. Estado actual: ' . $gangSheet->status,
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'width' => 'nullable|numeric|min:1',
            'height' => 'nullable|numeric|min:1',
            'images' => 'nullable|array',
            'preview' => 'nullable|string',
            'status' => 'nullable|in:draft,processing,completed,failed',
            'notes' => 'nullable|string',
        ]);

        try {
            // Update preview if provided
            if (!empty($validated['preview'])) {
                // Delete old preview
                if ($gangSheet->preview_path) {
                    Storage::disk('public')->delete($gangSheet->preview_path);
                }
                $validated['preview_path'] = $this->saveBase64Image($validated['preview'], 'previews');
                unset($validated['preview']);
            }

            // Recalculate total area if images updated
            if (isset($validated['images'])) {
                $totalArea = 0;
                foreach ($validated['images'] as $image) {
                    $totalArea += ($image['width'] ?? 0) * ($image['height'] ?? 0);
                }
                $validated['total_area'] = $totalArea;
                $validated['image_count'] = count($validated['images']);
                $validated['images_data'] = $validated['images'];
                unset($validated['images']);
            }

            $gangSheet->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Gang sheet updated successfully',
                'data' => $gangSheet->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating gang sheet: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate high-resolution final image (must belong to authenticated customer)
     */
    public function generateFinal(Request $request, $id)
    {
        $gangSheet = GangSheet::where('customer_id', auth()->id())->findOrFail($id);

        try {
            $gangSheet->update(['status' => 'processing']);

            // TODO: Implement high-resolution image generation using Imagick
            // This will create a 300 DPI PNG with all images positioned correctly
            $finalPath = $this->generateHighResImage($gangSheet);

            $gangSheet->update([
                'final_path' => $finalPath,
                'status' => 'completed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Final image generated successfully',
                'download_url' => Storage::url($finalPath),
            ]);

        } catch (\Exception $e) {
            $gangSheet->update(['status' => 'failed']);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating final image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a gang sheet (must belong to authenticated customer)
     */
    public function destroy($id)
    {
        $gangSheet = GangSheet::where('customer_id', auth()->id())->findOrFail($id);

        // Only allow deletion if gang sheet is still in draft
        if ($gangSheet->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes eliminar Gang Sheets en estado borrador',
            ], 403);
        }

        // Delete associated files
        if ($gangSheet->preview_path) {
            Storage::disk('public')->delete($gangSheet->preview_path);
        }
        if ($gangSheet->final_path) {
            Storage::disk('public')->delete($gangSheet->final_path);
        }

        $gangSheet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gang sheet deleted successfully',
        ]);
    }

    /**
     * Save base64 image to storage
     */
    private function saveBase64Image($base64String, $folder = 'gang-sheets')
    {
        // Remove data URI prefix if present
        $base64String = preg_replace('#^data:image/\w+;base64,#i', '', $base64String);
        
        // Decode base64
        $imageData = base64_decode($base64String);
        
        // Generate unique filename
        $filename = Str::random(40) . '.png';
        $path = $folder . '/' . $filename;
        
        // Save to storage
        Storage::disk('public')->put($path, $imageData);
        
        return $path;
    }

    /**
     * Testing endpoint - Generate image without payment
     * GET /api/gang-sheets/{id}/test-generate
     */
    public function testGenerateImage($id)
    {
        $gangSheet = GangSheet::findOrFail($id);

        try {
            $gangSheet->update(['status' => 'processing']);

            $finalPath = $this->generateHighResImage($gangSheet);

            $gangSheet->update([
                'final_path' => $finalPath,
                'status' => 'completed',
            ]);

            // Retornar información del archivo generado desde public/downloads/
            $filePath = public_path($finalPath);
            $fileSize = filesize($filePath);

            // \Log::info("Test generate image success", [
            //     'id' => $id,
            //     'final_path' => $finalPath,
            //     'file_size_mb' => round($fileSize / 1024 / 1024, 2),
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Image generated successfully',
                'download_url' => url($finalPath),
                'file_path' => $finalPath,
                'file_size_mb' => round($fileSize / 1024 / 1024, 2),
                'download_link' => "/api/gang-sheets/{$id}/download",
            ]);

        } catch (\Exception $e) {
            $gangSheet->update(['status' => 'failed']);
            \Log::error("Test generate image failed", [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download final gang sheet image
     * GET /api/gang-sheets/{id}/download
     */
    public function downloadFinal($id)
    {
        $gangSheet = GangSheet::findOrFail($id);

        if (!$gangSheet->final_path) {
            return response()->json(['error' => 'Image not generated yet'], 404);
        }

        // Ruta directa en public/downloads/
        $filePath = public_path($gangSheet->final_path);
        
        if (!file_exists($filePath)) {
            \Log::error("Final path does not exist: {$filePath}");
            return response()->json(['error' => 'Image not found'], 404);
        }

        // \Log::info("Downloading gang sheet", [
        //     'id' => $id,
        //     'file_path' => $filePath,
        //     'file_size' => filesize($filePath),
        // ]);
        
        return response()->download(
            $filePath,
            "gang-sheet-{$gangSheet->width}x{$gangSheet->height}-{$gangSheet->unit}.png",
            ['Content-Type' => 'image/png']
        );
    }

    /**
     * Generate high-resolution image for DTF printing
     * Uses Imagick for professional quality output
     */
    private function generateHighResImage(GangSheet $gangSheet)
    {
        $width = $gangSheet->width;
        $height = $gangSheet->height;
        $unit = $gangSheet->unit;
        $dpi = 120; // 120 DPI - optimal balance: professional quality without exceeding memory limits (INT_MAX 2.1GB)
        
        // Convert dimensions to pixels at 120 DPI
        if ($unit === 'feet') {
            // Convert feet to inches first, then to pixels
            $widthInches = $width * 12;
            $heightInches = $height * 12;
        } else {
            $widthInches = $width;
            $heightInches = $height;
        }
        
        $widthPixels = ceil($widthInches * $dpi);
        $heightPixels = ceil($heightInches * $dpi);
        
        try {
            // Use GD (compatible with all PHP installations)
            // Imagick support can be added later if needed
            return $this->generateWithGD($gangSheet, $widthPixels, $heightPixels, $dpi);
        } catch (\Exception $e) {
            \Log::error('Error generating high-res image: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate image using GD (primary method)
     */
    private function generateWithGD(GangSheet $gangSheet, int $widthPixels, int $heightPixels, int $dpi)
    {
        // \Log::info("=== GENERATE WITH GD START ===");
        // \Log::info("Dimensions: {$widthPixels}x{$heightPixels}px @ {$dpi}DPI");
        // \Log::info("Gang Sheet ID: {$gangSheet->id}, Images count: " . count($gangSheet->images_data ?? []));
        
        // Create blank image with white background
        $image = imagecreatetruecolor($widthPixels, $heightPixels);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);
        
        // Process each image
        $imagesData = $gangSheet->images_data ?? [];
        // \Log::info("Processing " . count($imagesData) . " images");
        
        foreach ($imagesData as $index => $imageData) {
            // \Log::info("Processing image[{$index}]: " . json_encode($imageData));
            
            // All dimensions from frontend are in INCHES (x, y, width, height)
            // We need to convert to pixels at the export DPI
            $x = $imageData['x'] ?? 0;
            $y = $imageData['y'] ?? 0;
            $width = $imageData['width'] ?? 0;
            $height = $imageData['height'] ?? 0;
            
            // Convert from inches to pixels at export DPI
            $scaledX = (int)($x * $dpi);
            $scaledY = (int)($y * $dpi);
            $scaledWidth = (int)($width * $dpi);
            $scaledHeight = (int)($height * $dpi);
            
            // \Log::info("Converted: x={$scaledX}, y={$scaledY}, width={$scaledWidth}, height={$scaledHeight} (from {$x}in x {$y}in x {$width}in x {$height}in)");
            
            $sourceImage = null;
            
            // Try to load from stored file path FIRST (more efficient)
            if (!empty($imageData['path'])) {
                // \Log::info("Checking path: " . $imageData['path']);
                if (Storage::disk('public')->exists($imageData['path'])) {
                    try {
                        $realPath = Storage::disk('public')->path($imageData['path']);
                        // \Log::info("Real path: {$realPath}");
                        $sourceImage = $this->loadImageGD($realPath);
                        // \Log::info("✓ Loaded image from path: {$imageData['path']}");
                    } catch (\Exception $e) {
                        \Log::warning('✗ Failed to load image from path: ' . $e->getMessage());
                    }
                } else {
                    \Log::warning('✗ Path does not exist: ' . $imageData['path']);
                }
            } else {
                \Log::warning('✗ No path in imageData');
            }
            
            // Fallback to base64 data URL if file not available
            if (!$sourceImage && !empty($imageData['base64'])) {
                $sourceImage = $this->loadImageFromBase64($imageData['base64']);
                // \Log::info("✓ Loaded image from base64 (fallback)");
            }
            
            // Paste image if loaded successfully
            if ($sourceImage) {
                try {
                    imagecopyresampled(
                        $image,
                        $sourceImage,
                        $scaledX,
                        $scaledY,
                        0,
                        0,
                        $scaledWidth,
                        $scaledHeight,
                        imagesx($sourceImage),
                        imagesy($sourceImage)
                    );
                    // \Log::info("✓ Pasted image at ({$scaledX}, {$scaledY}) size {$scaledWidth}x{$scaledHeight}");
                    imagedestroy($sourceImage);
                } catch (\Exception $e) {
                    \Log::warning('✗ Failed to paste image: ' . ($imageData['name'] ?? 'unknown'));
                }
            } else {
                \Log::warning('✗ No source image available for: ' . ($imageData['name'] ?? 'unknown') . ' [index=' . $index . ']');
            }
        }
        
        // Save as PNG - Directamente en public/downloads (accesible desde web sin symlinks)
        $filename = 'gang-sheet-' . $gangSheet->id . '-' . time() . '.png';
        $path = public_path('downloads/' . $filename);
        
        $pngResult = imagepng($image, $path, 9); // 9 = maximum compression
        imagedestroy($image);
        
        // \Log::info("=== GENERATE WITH GD END ===");
        // \Log::info("PNG save result: " . ($pngResult ? 'SUCCESS' : 'FAILED'));
        // \Log::info("PNG file path: {$path}");
        // \Log::info("PNG file size: " . filesize($path) . " bytes");
        // \Log::info("Direct URL: /downloads/{$filename}");
        
        return 'downloads/' . $filename;
    }

    /**
     * Load image from base64 data URL
     */
    private function loadImageFromBase64(string $base64Data)
    {
        try {
            // Extract base64 content from data URL
            if (strpos($base64Data, 'data:image') === 0) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            }
            
            // Decode base64
            $imageData = base64_decode($base64Data, true);
            if ($imageData === false) {
                return null;
            }
            
            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tempFile, $imageData);
            
            // Detect MIME type and load
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tempFile);
            finfo_close($finfo);
            
            $image = null;
            switch ($mimeType) {
                case 'image/png':
                    $image = imagecreatefrompng($tempFile);
                    break;
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($tempFile);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($tempFile);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($tempFile);
                    break;
            }
            
            // Clean up temp file
            @unlink($tempFile);
            
            return $image;
        } catch (\Exception $e) {
            \Log::warning('Failed to load base64 image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Load image using GD (supports PNG, JPG, etc)
     */
    private function loadImageGD(string $path)
    {
        $mimeType = mime_content_type($path);
        
        switch ($mimeType) {
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return null;
        }
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,svg|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('image');
            
            // Store original
            $path = $file->store('uploads', 'public');
            
            // Get image dimensions
            $imageInfo = getimagesize($file->getRealPath());
            
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => Storage::url($path),
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'size' => $file->getSize(),
                'name' => $file->getClientOriginalName(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage(),
            ], 500);
        }
    }
}

