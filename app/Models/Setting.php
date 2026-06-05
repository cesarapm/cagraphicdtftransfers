<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Boot del modelo para limpiar cache automáticamente
     */
    protected static function booted(): void
    {
        // Limpiar cache cuando se crea o actualiza un setting
        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
        });
    }
    /**
     * Obtener un valor de configuración con cache
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", now()->addHours(24), function () use ($key, $default) {
            return self::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Establecer un valor de configuración y limpiar cache
     */
    public static function set(string $key, mixed $value, ?string $description = null): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
            ]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Limpiar todo el cache de configuraciones
     */
    public static function clearCache(): void
    {
        Cache::tags(['settings'])->flush();
    }
}
