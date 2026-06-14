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
            ->get();

        return response()->json($sizes);
    }
}
