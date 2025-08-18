<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') {
            abort(403, 'No autorizado');
        }
        return $next($request);
    }
}
