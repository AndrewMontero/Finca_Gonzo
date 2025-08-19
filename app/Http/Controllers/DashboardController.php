<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Producto;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;   // 👈 IMPORTANTE
use Illuminate\Support\Facades\Route;  // 👈 IMPORTANTE

class DashboardController extends Controller
{
    public function index()
    {
        // ✅ Si el usuario es CLIENTE, redirigir siempre a la Tienda
        if (Auth::check() && (Auth::user()->rol ?? null) === 'cliente' && Route::has('tienda.index')) {
            return redirect()->route('tienda.index');
        }

        // KPIs
        $totalVentas         = (float) Factura::sum('total');
        $entregasCompletadas = Entrega::where('estado', 'realizada')->count();
        $entregasPendientes  = Entrega::where('estado', 'pendiente')->count();

        // Productos con bajo stock
        $productosBajoStock = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual')
            ->take(10)
            ->get();

        // Top 5 productos más vendidos
        $productosMasVendidos = Producto::withCount('entregas')
            ->orderByDesc('entregas_count')
            ->take(5)
            ->get();

        // Ventas por mes
        $driver   = DB::getDriverName();
        $monthCol = match ($driver) {
            'sqlite' => "CAST(strftime('%m', created_at) AS INTEGER)",
            'pgsql'  => "EXTRACT(MONTH FROM created_at)",
            default  => "MONTH(created_at)",
        };

        $rows = Factura::selectRaw("$monthCol AS mes, SUM(total) AS total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $serie = array_fill(0, 12, 0.0);
        foreach ($rows as $r) {
            $i = max(1, min(12, (int) $r->mes)) - 1;
            $serie[$i] = (float) $r->total;
        }

        return view('dashboard', compact(
            'totalVentas',
            'entregasCompletadas',
            'entregasPendientes',
            'productosBajoStock',
            'productosMasVendidos',
            'serie'
        ));
    }
}
