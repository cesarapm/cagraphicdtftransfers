<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class AdminOnly
{
    /**
     * Maneja una solicitud entrante.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect(Filament::getLoginUrl()) // Redirigir al login correcto
                ->with('error', 'Acceso denegado. Solo administradores.');
        }

        return $next($request);
    }
}
