<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DtfGang;

class DtfGangController extends Controller
{
    /**
     * Display a listing of the resource with discount information.
     */
    public function index()
    {
        return DtfGang::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($gang) => $this->formatWithDiscount($gang));
    }

    /**
     * Display the specified resource with discount info.
     */
    public function show(string $id)
    {
        $gang = DtfGang::findOrFail($id);
        return $this->formatWithDiscount($gang);
    }

    /**
     * Format DTF Gang with discount information
     */
    private function formatWithDiscount(DtfGang $gang)
    {
        $promotion = $gang->activePromotion; // Gets active promotion if exists

        $data = $gang->toArray();

        if ($promotion) {
            $data['promotion'] = [
                'id' => $promotion->id,
                'titulo' => $promotion->titulo,
                'descripcion' => $promotion->descripcion,
                'discount_type' => $promotion->discount_type,
                'discount_value' => $promotion->discount_value,
                'discount_amount' => $promotion->calculateDiscount($gang->price),
                'final_price' => $promotion->getFinalPrice($gang->price),
                'inicio' => $promotion->inicio,
                'fin' => $promotion->fin,
            ];
        } else {
            $data['promotion'] = null;
        }

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
