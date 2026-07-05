<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\DtfGang;
use App\Models\SheetSize;
use App\Models\DtfSize;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Get all active promotions
     */
    public function index()
    {
        return Promotion::active()->get();
    }

    /**
     * Get promotions for a specific promotionable item (DtfGang, DtfSize, etc.)
     * 
     * Example: GET /api/promotions/dtf-gangs/1
     */
    public function getForItem(string $type, int $id)
    {
        $modelClass = $this->getModelClass($type);
        
        if (!$modelClass) {
            return response()->json(['error' => 'Invalid type'], 404);
        }

        $item = $modelClass::findOrFail($id);
        $promotion = $item->promotion()->first();

        return response()->json([
            'item' => $item,
            'promotion' => $promotion,
            'has_active_promotion' => $item->activePromotion ? true : false,
        ]);
    }

    /**
     * Create or update promotion for an item
     */
    public function store(Request $request, string $type, int $id)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'inicio' => 'nullable|date',
            'fin' => 'nullable|date|after_or_equal:inicio',
            'is_active' => 'boolean',
        ]);

        $modelClass = $this->getModelClass($type);
        
        if (!$modelClass) {
            return response()->json(['error' => 'Invalid type'], 404);
        }

        $item = $modelClass::findOrFail($id);

        // Create or update promotion (polymorphic)
        $promotion = $item->promotion()->updateOrCreate([], $validated);

        return response()->json([
            'message' => 'Promotion created/updated successfully',
            'promotion' => $promotion,
        ], 201);
    }

    /**
     * Delete promotion for an item
     */
    public function destroy(string $type, int $id)
    {
        $modelClass = $this->getModelClass($type);
        
        if (!$modelClass) {
            return response()->json(['error' => 'Invalid type'], 404);
        }

        $item = $modelClass::findOrFail($id);
        $item->promotion()->delete();

        return response()->json(['message' => 'Promotion deleted successfully']);
    }

    /**
     * Get model class from type string
     */
    private function getModelClass(string $type)
    {
        return match($type) {
            'dtf-gangs' => DtfGang::class,
            'dtf-sizes' => DtfSize::class,
            'sheet-sizes' => SheetSize::class,
            'products' => \App\Models\Product::class,
            default => null,
        };
    }
}
