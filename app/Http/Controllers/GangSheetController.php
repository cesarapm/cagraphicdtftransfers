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
     * Save a new gang sheet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'width' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
            'images' => 'required|array|min:1',
            'preview' => 'nullable|string', // Base64 preview
            'order_id' => 'nullable|exists:orders,id',
            'notes' => 'nullable|string',
        ]);

        try {
            // Save preview image if provided
            $previewPath = null;
            if (!empty($validated['preview'])) {
                $previewPath = $this->saveBase64Image($validated['preview'], 'previews');
            }

            // Calculate total area
            $totalArea = 0;
            foreach ($validated['images'] as $image) {
                $totalArea += ($image['width'] ?? 0) * ($image['height'] ?? 0);
            }

            $gangSheet = GangSheet::create([
                'customer_id' => auth()->id(),
                'order_id' => $validated['order_id'] ?? null,
                'name' => $validated['name'] ?? 'Gang Sheet ' . now()->format('Y-m-d H:i'),
                'width' => $validated['width'],
                'height' => $validated['height'],
                'dpi' => 300, // Default to 300 DPI for final export
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
     * Generate high-resolution image (placeholder - needs Imagick implementation)
     */
    private function generateHighResImage(GangSheet $gangSheet)
    {
        // TODO: Implement with Imagick for proper 300 DPI output
        // For now, this is a placeholder
        
        $dpi = 300;
        $width = $gangSheet->width * $dpi;
        $height = $gangSheet->height * $dpi;
        
        // This is a simplified version - in production, you'd use Imagick
        // to properly composite images at 300 DPI with transparency
        
        $filename = 'final-' . Str::random(40) . '.png';
        $path = 'gang-sheets/' . $filename;
        
        // Placeholder: Return path for now
        // In production, implement proper image generation here
        
        return $path;
    }

    /**
     * Upload and process image for gang sheet
     */
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

