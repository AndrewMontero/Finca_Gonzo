<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Uso:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,repartidor')
     *   ->middleware('role:admin|repartidor')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Normaliza "admin,repartidor" o "admin|repartidor"
        $allowed = [];
        foreach ($roles as $r) {
            foreach (preg_split('/[,\|]/', (string) $r) as $piece) {
                $piece = trim($piece);
                if ($piece !== '') $allowed[] = $piece;
            }
        }

        if (empty($allowed)) {
            abort(403, 'Acceso no autorizado');
        }

        $userRole = (string) (Auth::user()->rol ?? '');
        if ($userRole === '' || !in_array($userRole, $allowed, true)) {
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}
