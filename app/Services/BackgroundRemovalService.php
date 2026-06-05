<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

/**
 * Background Removal Service
 * 
 * This service provides methods to remove backgrounds from images
 * using different providers (remove.bg, local processing, etc.)
 */
class BackgroundRemovalService
{
    /**
     * Remove background using remove.bg API
     * 
     * @param string $imagePath Path to the image file
     * @return string Path to the processed image
     * @throws Exception
     */
    public function removeBackgroundRemoveBg($imagePath)
    {
        $apiKey = config('services.removebg.api_key');
        
        if (!$apiKey) {
            throw new Exception('Remove.bg API key not configured');
        }

        $imageData = Storage::disk('public')->get($imagePath);

        $response = Http::withHeaders([
            'X-Api-Key' => $apiKey,
        ])->attach(
            'image_file',
            $imageData,
            basename($imagePath)
        )->post('https://api.remove.bg/v1.0/removebg', [
            'size' => 'auto',
            'type' => 'product', // or 'person', 'auto'
            'format' => 'png',
        ]);

        if ($response->successful()) {
            $filename = 'no-bg-' . Str::random(40) . '.png';
            $path = 'processed/' . $filename;
            
            Storage::disk('public')->put($path, $response->body());
            
            return $path;
        }

        throw new Exception('Failed to remove background: ' . $response->body());
    }

    /**
     * Remove background using Python rembg library
     * 
     * Requires Python with rembg installed:
     * pip install rembg pillow
     * 
     * @param string $imagePath Path to the image file
     * @return string Path to the processed image
     * @throws Exception
     */
    public function removeBackgroundRembg($imagePath)
    {
        $fullPath = Storage::disk('public')->path($imagePath);
        $outputFilename = 'no-bg-' . Str::random(40) . '.png';
        $outputPath = 'processed/' . $outputFilename;
        $fullOutputPath = Storage::disk('public')->path($outputPath);

        // Ensure output directory exists
        $outputDir = dirname($fullOutputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Python script path
        $scriptPath = base_path('scripts/remove_background.py');
        
        if (!file_exists($scriptPath)) {
            throw new Exception('Python script not found: ' . $scriptPath);
        }

        // Execute Python script
        $command = sprintf(
            'python3 %s %s %s 2>&1',
            escapeshellarg($scriptPath),
            escapeshellarg($fullPath),
            escapeshellarg($fullOutputPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception('Background removal failed: ' . implode("\n", $output));
        }

        if (!file_exists($fullOutputPath)) {
            throw new Exception('Output file was not created');
        }

        return $outputPath;
    }

    /**
     * Remove background using ImageMagick (basic method)
     * 
     * This is a simple color-based removal and may not work well for complex images
     * 
     * @param string $imagePath Path to the image file
     * @param string $backgroundColor Color to remove (hex or name)
     * @param int $fuzz Tolerance level (0-100)
     * @return string Path to the processed image
     * @throws Exception
     */
    public function removeBackgroundImageMagick($imagePath, $backgroundColor = 'white', $fuzz = 10)
    {
        if (!extension_loaded('imagick')) {
            throw new Exception('ImageMagick extension not installed');
        }

        $fullPath = Storage::disk('public')->path($imagePath);
        $outputFilename = 'no-bg-' . Str::random(40) . '.png';
        $outputPath = 'processed/' . $outputFilename;
        $fullOutputPath = Storage::disk('public')->path($outputPath);

        // Ensure output directory exists
        $outputDir = dirname($fullOutputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        try {
            $image = new \Imagick($fullPath);
            
            // Set the fuzz factor (color tolerance)
            $fuzzPercentage = $fuzz * 655.35; // Convert to Imagick's scale
            $image->setImageBackgroundColor(new \ImagickPixel('transparent'));
            
            // Remove background color
            $image->transparentPaintImage(
                new \ImagickPixel($backgroundColor),
                0,
                $fuzzPercentage,
                false
            );
            
            // Set format to PNG (supports transparency)
            $image->setImageFormat('png');
            
            // Save the image
            $image->writeImage($fullOutputPath);
            $image->clear();
            $image->destroy();
            
            return $outputPath;
            
        } catch (\ImagickException $e) {
            throw new Exception('ImageMagick processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Auto-detect and remove background using best available method
     * 
     * @param string $imagePath Path to the image file
     * @return array Result with path and method used
     */
    public function removeBackgroundAuto($imagePath)
    {
        // Try remove.bg first (best quality)
        if (config('services.removebg.api_key')) {
            try {
                $path = $this->removeBackgroundRemoveBg($imagePath);
                return [
                    'path' => $path,
                    'method' => 'remove.bg',
                    'url' => Storage::url($path),
                ];
            } catch (Exception $e) {
                \Log::warning('remove.bg failed, trying alternative: ' . $e->getMessage());
            }
        }

        // Try Python rembg
        if (file_exists(base_path('scripts/remove_background.py'))) {
            try {
                $path = $this->removeBackgroundRembg($imagePath);
                return [
                    'path' => $path,
                    'method' => 'rembg',
                    'url' => Storage::url($path),
                ];
            } catch (Exception $e) {
                \Log::warning('rembg failed, trying ImageMagick: ' . $e->getMessage());
            }
        }

        // Fallback to ImageMagick
        try {
            $path = $this->removeBackgroundImageMagick($imagePath);
            return [
                'path' => $path,
                'method' => 'imagemagick',
                'url' => Storage::url($path),
                'warning' => 'Used basic method - quality may vary',
            ];
        } catch (Exception $e) {
            throw new Exception('All background removal methods failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate image for background removal
     * 
     * @param string $imagePath Path to the image file
     * @return array Validation result
     */
    public function validateImage($imagePath)
    {
        $fullPath = Storage::disk('public')->path($imagePath);
        
        if (!file_exists($fullPath)) {
            return [
                'valid' => false,
                'error' => 'File not found',
            ];
        }

        $imageInfo = @getimagesize($fullPath);
        
        if (!$imageInfo) {
            return [
                'valid' => false,
                'error' => 'Invalid image file',
            ];
        }

        $minSize = 100;
        $maxSize = 25000; // 25 megapixels
        
        $megapixels = ($imageInfo[0] * $imageInfo[1]) / 1000000;
        
        if ($imageInfo[0] < $minSize || $imageInfo[1] < $minSize) {
            return [
                'valid' => false,
                'error' => 'Image too small (minimum ' . $minSize . 'px)',
            ];
        }
        
        if ($megapixels > $maxSize) {
            return [
                'valid' => false,
                'error' => 'Image too large (maximum ' . $maxSize . ' megapixels)',
            ];
        }

        return [
            'valid' => true,
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'megapixels' => round($megapixels, 2),
            'mime' => $imageInfo['mime'],
        ];
    }

    /**
     * Calculate cost for background removal (if using paid service)
     * 
     * @param int $imageCount Number of images
     * @return array Cost breakdown
     */
    public function calculateCost($imageCount)
    {
        // Example pricing for remove.bg
        $pricePerImage = 0.10; // $0.10 per image for API calls
        
        $total = $imageCount * $pricePerImage;
        
        // Volume discounts
        if ($imageCount >= 100) {
            $discount = 0.20; // 20% off
            $total *= (1 - $discount);
        } elseif ($imageCount >= 50) {
            $discount = 0.10; // 10% off
            $total *= (1 - $discount);
        }

        return [
            'image_count' => $imageCount,
            'price_per_image' => $pricePerImage,
            'subtotal' => $imageCount * $pricePerImage,
            'discount' => $discount ?? 0,
            'total' => round($total, 2),
        ];
    }
}
