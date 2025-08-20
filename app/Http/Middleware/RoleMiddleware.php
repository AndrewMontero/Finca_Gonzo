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
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Si no hay usuario autenticado, que pase al middleware 'auth' a fallar
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $rolUsuario = auth()->user()->rol ?? null;

        // Si no se pasó ningún rol, deja continuar (comportamiento permisivo)
        if (empty($roles)) {
            return $next($request);
        }

        // Si el rol del usuario está dentro de los permitidos → OK
        if (in_array($rolUsuario, $roles, true)) {
            return $next($request);
        }

        // Si no tiene permiso, 403
        abort(403, 'No tienes permiso para acceder a esta ruta.');
    }
}
