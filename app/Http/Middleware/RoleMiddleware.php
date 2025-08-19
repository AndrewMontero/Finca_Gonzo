<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Restringe acceso por rol.
     *
     * Se puede invocar:
     *   - Por alias: 'role:admin'    (si usas el alias en Kernel)
     *   - Por clase: RoleMiddleware::class . ':admin'  ✅ (recomendado para evitar caché de alias)
     *
     * Acepta varios roles:
     *   'admin,repartidor' o 'admin|repartidor'
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Normaliza los roles recibidos (coma o pipe)
        $allowed = [];
        foreach ($roles as $r) {
            foreach (preg_split('/[,\|]/', (string) $r) as $piece) {
                $piece = trim($piece);
                if ($piece !== '') {
                    $allowed[] = $piece;
                }
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
