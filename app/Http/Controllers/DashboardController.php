<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Producto;
use App\Models\Factura;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total de ventas (sumatoria de todas las facturas)
        $totalVentas = Factura::sum('total');

        // Total de entregas completadas
        $entregasCompletadas = Entrega::where('estado', 'realizada')->count();

        // Entregas pendientes
        $entregasPendientes = Entrega::where('estado', 'pendiente')->count();

        // Productos con bajo stock
        $productosBajoStock = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();

        // Productos más vendidos (top 5)
        $productosMasVendidos = Producto::withCount('entregas')
            ->orderBy('entregas_count', 'desc')
            ->take(5)
            ->get();

        // Ventas por mes (gráfico)
        $ventasPorMes = Factura::selectRaw('MONTH(created_at) as mes, SUM(total) as total')
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        return view('dashboard', compact(
            'totalVentas',
            'entregasCompletadas',
            'entregasPendientes',
            'productosBajoStock',
            'productosMasVendidos',
            'ventasPorMes'
        ));
    }
}
