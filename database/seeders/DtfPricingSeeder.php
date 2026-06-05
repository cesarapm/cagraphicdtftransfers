<?php

namespace Database\Seeders;

use App\Models\DtfPricing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DtfPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pricings = [
            [
                'name' => '22" × 120" (10 ft)',
                'width' => 22,
                'height' => 120,
                'base_price' => 165.00,
                'min_coverage_discount' => 10, // 10% discount
                'coverage_threshold' => 80, // if coverage > 80%
                'is_active' => true,
                'sort_order' => 1,
                'description' => 'Hoja grande de 10 pies - Ideal para pedidos grandes'
            ],
            [
                'name' => '22" × 60" (5 ft)',
                'width' => 22,
                'height' => 60,
                'base_price' => 95.00,
                'min_coverage_discount' => 10,
                'coverage_threshold' => 80,
                'is_active' => true,
                'sort_order' => 2,
                'description' => 'Hoja mediana de 5 pies - Mejor relación calidad-precio'
            ],
            [
                'name' => '13" × 19"',
                'width' => 13,
                'height' => 19,
                'base_price' => 28.00,
                'min_coverage_discount' => 5,
                'coverage_threshold' => 75,
                'is_active' => true,
                'sort_order' => 3,
                'description' => 'Hoja pequeña - Perfecta para pedidos pequeños o pruebas'
            ],
        ];

        foreach ($pricings as $pricing) {
            DtfPricing::updateOrCreate(
                ['width' => $pricing['width'], 'height' => $pricing['height']],
                $pricing
            );
        }
    }
}
