<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SheetSize;
use Illuminate\Http\Request;

class SheetSizeController extends Controller
{
    // Obtener tamaños por unidad (feet o inches)
    public function getByUnit($unit)
    {
        $sizes = SheetSize::active()
            ->byUnit($unit)
            ->ordered()
            ->get();

        return response()->json($sizes);
    }

    // Obtener todos los tamaños activos
    public function index()
    {
        $sizes = SheetSize::active()
            ->ordered()
            ->get()
            ->map(fn ($size) => $this->formatWithDiscount($size));

        return response()->json($sizes);
    }

    /**
     * Get a specific sheet size by ID
     */
    public function show($id)
    {
        $size = SheetSize::find($id);

        if (!$size) {
            return response()->json(['error' => 'Sheet size not found'], 404);
        }

        return response()->json($this->formatWithDiscount($size));
    }
     /**
     * Format DTF Size with discount information
     */
    private function formatWithDiscount(SheetSize $size)
    {
        $promotion = $size->activePromotion; // Gets active promotion if exists

        $data = $size->toArray();

        if ($promotion) {
            $data['promotion'] = [
                'id' => $promotion->id,
                'titulo' => $promotion->titulo,
                'descripcion' => $promotion->descripcion,
                'discount_type' => $promotion->discount_type,
                'discount_value' => $promotion->discount_value,
                'discount_amount' => $promotion->calculateDiscount($size->price),
                'final_price' => $promotion->getFinalPrice($size->price),
                'inicio' => $promotion->inicio,
                'fin' => $promotion->fin,
            ];
        } else {
            $data['promotion'] = null;
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
