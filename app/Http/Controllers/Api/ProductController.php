<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function resolveCollectionFilter(?string $collection): ?string
    {
        if (!$collection) {
            return null;
        }

        return match ($collection) {
            'cosmologia-maya', 'ancestral', 'Coleccion Cosmologia Maya' => 'Coleccion Cosmologia Maya',
            'maya-contemporanea', 'contemporaneo', 'Coleccion Maya Contemporanea' => 'Coleccion Maya Contemporanea',
            default => $collection,
        };
    }

    private function resolveProductImage($product): ?string
    {
        if ($product->image) {
            return asset('storage/' . $product->image);
        }

        $gallery = $product->getGalleryUrls();

        return $gallery[0] ?? null;
    }

    private function serializeProduct($product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'codigo' => $product->codigo,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'category' => $product->category,
            'collection' => $product->collection,
            'image' => $this->resolveProductImage($product),
            'gallery' => $product->getGalleryUrls(),
            'is_active' => $product->is_active,
            'material' => $product->material,
            'peso' => $product->peso,
            'dimensiones' => $product->dimensiones,
            'destacado' => $product->destacado,
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);
        $perPage = 8;

        // Filtrar por categoría
        if ($request->has('category') && $request->category !== 'Todas') {
            $query->where('category', $request->category);
        }

        if ($request->filled('collection') && $request->collection !== 'Todas') {
            $query->where('collection', $this->resolveCollectionFilter($request->collection));
        }

        // Buscar por nombre o descripción
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Ordenar
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price-asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price-desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query
            ->paginate($perPage)
            ->through(fn ($product) => $this->serializeProduct($product))
            ->withQueryString();

        return response()->json($products);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::where('is_active', true)->find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($this->serializeProduct($product));
    }

    /**
     * Get featured products
     */
    public function featured()
    {
        $products = Product::where('is_active', true)
            ->where('destacado', true)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(fn ($product) => $this->serializeProduct($product));

        return response()->json($products);
    }
}
