<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Producto;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $totalVentas = (float) Factura::sum('total');
        $entregasCompletadas = Entrega::where('estado', 'realizada')->count();
        $entregasPendientes  = Entrega::where('estado', 'pendiente')->count();

        // Productos con bajo stock
        $productosBajoStock = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual')
            ->take(10)
            ->get();

        // Top 5 productos más vendidos por cantidad de entregas
        $productosMasVendidos = Producto::withCount('entregas')
            ->orderByDesc('entregas_count')
            ->take(5)
            ->get();

        // ---- Ventas por mes (multi-motor) ----
        $driver = DB::getDriverName();
        $monthExpr = match ($driver) {
            'sqlite' => "CAST(strftime('%m', created_at) AS INTEGER)", // 1..12
            'pgsql'  => "EXTRACT(MONTH FROM created_at)",
            default  => "MONTH(created_at)", // mysql
        };

        $rows = Factura::selectRaw("$monthExpr AS mes, SUM(total) AS total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Serie fija de 12 meses
        $serie = array_fill(0, 12, 0.0);
        foreach ($rows as $r) {
            $idx = max(1, min(12, (int)$r->mes)) - 1;
            $serie[$idx] = (float) $r->total;
        }

        // Pasar datos a la vista
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
