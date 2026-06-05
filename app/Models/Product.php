<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'codigo',
        'description',
        'price',
        'stock',
        'category',
        'image',
        'galeria_imagenes',
        'collection',
        'is_active',
        'material',
        'peso',
        'dimensiones',
        'destacado',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'destacado' => 'boolean',
        'galeria_imagenes' => 'array',
    ];
protected static function boot()
{
    parent::boot();

    static::updated(function ($product) {
        // Manejar imagen principal
        if ($product->wasChanged('image')) {
            $oldImage = $product->getOriginal('image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        // Manejar galería de imágenes
        if ($product->wasChanged('galeria_imagenes')) {
            $oldGallery = $product->getOriginal('galeria_imagenes') ?? [];
            $currentGallery = $product->galeria_imagenes ?? [];

            $removedImages = array_diff($oldGallery, $currentGallery);

            foreach ($removedImages as $oldImage) {
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        }
    });

    static::deleting(function ($product) {
        // Eliminar imagen principal
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Eliminar imágenes de la galería
        if (is_array($product->galeria_imagenes)) {
            foreach ($product->galeria_imagenes as $image) {
                if ($image && Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }
    });
}




    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Métodos helper para galería de imágenes
    public function addImageToGallery(string $imagePath): void
    {
        $gallery = $this->galeria_imagenes ?? [];
        $gallery[] = $imagePath;
        $this->galeria_imagenes = $gallery;
        $this->save();
    }

    public function removeImageFromGallery(string $imagePath): void
    {
        $gallery = $this->galeria_imagenes ?? [];
        $gallery = array_filter($gallery, fn($img) => $img !== $imagePath);
        $this->galeria_imagenes = array_values($gallery);
        $this->save();

        // Eliminar archivo físico
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    public function getGalleryUrls(): array
    {
        if (!$this->galeria_imagenes) {
            return [];
        }

        return array_map(
            fn($path) => Storage::url($path),
            $this->galeria_imagenes
        );
    }

    public function hasGallery(): bool
    {
        return !empty($this->galeria_imagenes);
    }

    public function getGalleryCount(): int
    {
        return count($this->galeria_imagenes ?? []);
    }
}
