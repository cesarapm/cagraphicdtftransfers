<?php

namespace Database\Seeders;

use App\Models\SheetSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SheetSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            // Feet sizes
            [
                'name' => '22\' × 10\' (Feet)',
                'width' => 22,
                'height' => 10,
                'unit' => 'feet',
                'price' => 165.00,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '22\' × 5\' (Feet)',
                'width' => 22,
                'height' => 5,
                'unit' => 'feet',
                'price' => 95.00,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '11\' × 5\' (Feet)',
                'width' => 11,
                'height' => 5,
                'unit' => 'feet',
                'price' => 65.00,
                'is_active' => true,
                'sort_order' => 3,
            ],
            
            // Inches sizes
            [
                'name' => '22" × 120" (Inches)',
                'width' => 22,
                'height' => 120,
                'unit' => 'inches',
                'price' => 165.00,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '22" × 60" (Inches)',
                'width' => 22,
                'height' => 60,
                'unit' => 'inches',
                'price' => 95.00,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '13" × 19" (Inches)',
                'width' => 13,
                'height' => 19,
                'unit' => 'inches',
                'price' => 28.00,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => '11" × 17" (Inches)',
                'width' => 11,
                'height' => 17,
                'unit' => 'inches',
                'price' => 22.00,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($sizes as $size) {
            SheetSize::create($size);
        }
    }
}
