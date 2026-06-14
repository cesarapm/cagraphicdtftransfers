<?php

namespace Tests\Feature;

use Tests\TestCase;

class PhpConfigurationTest extends TestCase
{
    /**
     * Test: Verify PHP limits are configured for large image uploads
     */
    public function test_php_limits_are_configured()
    {
        $post_max_size = $this->getBytes(ini_get('post_max_size'));
        $upload_max_filesize = $this->getBytes(ini_get('upload_max_filesize'));
        $memory_limit = $this->getBytes(ini_get('memory_limit'));
        $max_execution_time = (int)ini_get('max_execution_time');

        // Expected values (50M for uploads, 256M for memory)
        $expected_upload = 50 * 1024 * 1024; // 50 MB
        $expected_memory = 256 * 1024 * 1024; // 256 MB
        $expected_execution = 300; // 300 seconds

        echo "\n🔍 PHP Configuration Check:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        // Check post_max_size
        echo sprintf("✓ post_max_size: %s (Expected: 50M)\n", 
            ini_get('post_max_size'));
        $this->assertGreaterThanOrEqual($expected_upload, $post_max_size,
            "post_max_size must be at least 50M for image uploads");

        // Check upload_max_filesize
        echo sprintf("✓ upload_max_filesize: %s (Expected: 50M)\n",
            ini_get('upload_max_filesize'));
        $this->assertGreaterThanOrEqual($expected_upload, $upload_max_filesize,
            "upload_max_filesize must be at least 50M");

        // Check memory_limit
        echo sprintf("✓ memory_limit: %s (Expected: 256M)\n",
            ini_get('memory_limit'));
        $this->assertGreaterThanOrEqual($expected_memory, $memory_limit,
            "memory_limit must be at least 256M for PNG generation");

        // Check max_execution_time (0 means unlimited, which is OK)
        echo sprintf("✓ max_execution_time: %s seconds (Expected: 300s or 0=unlimited)\n",
            $max_execution_time ?: "unlimited");
        $this->assertTrue(
            $max_execution_time === 0 || $max_execution_time >= $expected_execution,
            "max_execution_time must be unlimited (0) or at least 300 seconds"
        );

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ All PHP limits are correctly configured!\n\n";
    }

    /**
     * Test: Verify storage directories exist and are writable
     */
    public function test_storage_directories_exist_and_writable()
    {
        echo "\n🔍 Storage Directory Check:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        $directories = [
            storage_path('app/public/gang-sheets') => 'Gang Sheet Uploads',
            storage_path('app/public/exports') => 'PNG Exports',
            storage_path('logs') => 'Error Logs',
        ];

        foreach ($directories as $path => $label) {
            if (is_dir($path)) {
                $writable = is_writable($path) ? "✅ writable" : "❌ NOT writable";
                echo sprintf("✓ %s: %s (%s)\n", $label, $writable, $path);
                $this->assertTrue(is_writable($path), "$label directory must be writable: $path");
            } else {
                echo "❌ MISSING: $label ($path)\n";
                $this->fail("Directory not found: $path. Please create it.");
            }
        }

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ All storage directories are ready!\n\n";
    }

    /**
     * Test: Check if GD extension is available for image processing
     */
    public function test_gd_extension_available()
    {
        echo "\n🔍 Image Processing Check:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        $gdLoaded = extension_loaded('gd');
        echo sprintf("✓ GD Extension: %s\n", $gdLoaded ? "✅ LOADED" : "❌ NOT LOADED");
        
        $this->assertTrue($gdLoaded, 
            "GD extension is required for image processing. Please enable it in php.ini");

        if ($gdLoaded) {
            $gdInfo = gd_info();
            echo sprintf("✓ PNG Support: %s\n", $gdInfo['PNG Support'] ? "✅ YES" : "❌ NO");
            $this->assertTrue($gdInfo['PNG Support'], "PNG support required in GD");
        }

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ Image processing is ready!\n\n";
    }

    /**
     * Convert PHP byte format (like "50M") to actual bytes
     */
    private function getBytes(string $value): int
    {
        $value = trim($value);
        
        if ($value === '-1') {
            return PHP_INT_MAX;
        }

        $last = strtoupper(substr($value, -1));
        $num = (int)substr($value, 0, -1);

        switch ($last) {
            case 'G':
                return $num * 1024 * 1024 * 1024;
            case 'M':
                return $num * 1024 * 1024;
            case 'K':
                return $num * 1024;
            default:
                return (int)$value;
        }
    }
}
