<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function reseedFacturas(): RedirectResponse
    {
        $next = (int) (DB::table('facturas')->max('id') ?? 0) + 1;
        DB::statement('ALTER TABLE facturas AUTO_INCREMENT = '.$next);
        return back()->with('success', 'AUTO_INCREMENT de facturas ajustado a '.$next);
    }

    public function reseedEntregas(): RedirectResponse
    {
        $next = (int) (DB::table('entregas')->max('id') ?? 0) + 1;
        DB::statement('ALTER TABLE entregas AUTO_INCREMENT = '.$next);
        return back()->with('success', 'AUTO_INCREMENT de entregas ajustado a '.$next);
    }
}
