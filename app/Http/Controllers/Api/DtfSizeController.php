<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DtfSize;
use Illuminate\Http\Request;

class DtfSizeController extends Controller
{
    /**
     * Display a listing of the resource with discount information.
     */
    public function index()
    {
        return DtfSize::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($size) => $this->formatWithDiscount($size));
    }

    /**
     * Display the specified resource with discount info.
     */
    public function show(string $id)
    {
        $size = DtfSize::findOrFail($id);
        return $this->formatWithDiscount($size);
    }

    /**
     * Format DTF Size with discount information
     */
    private function formatWithDiscount(DtfSize $size)
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
