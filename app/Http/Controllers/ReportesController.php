<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Entrega;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportesController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    // --- Ventas por rango de fechas ---
    public function reporteVentas(Request $request)
    {
        $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $q = Factura::query();
        if ($desde) $q->whereDate('created_at', '>=', $desde);
        if ($hasta) $q->whereDate('created_at', '<=', $hasta);

        // ✅ Usando entrega_id (la columna real en la tabla facturas)
        $ventas = $q->select('id', 'entrega_id', 'total', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $total = (float) $q->clone()->sum('total');

        return view('reportes.ventas', compact('ventas', 'total', 'desde', 'hasta'));
    }

    // --- Exportar CSV ---
    public function exportVentasCsv(Request $request): StreamedResponse
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $q = Factura::query();
        if ($desde) $q->whereDate('created_at', '>=', $desde);
        if ($hasta) $q->whereDate('created_at', '<=', $hasta);

        // ✅ Usando entrega_id en lugar de cliente_id
        $rows = $q->orderBy('created_at')->get(['id', 'entrega_id', 'total', 'created_at']);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reporte_ventas.csv"',
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Factura ID', 'Entrega ID', 'Total', 'Fecha']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->id, $r->entrega_id, $r->total, $r->created_at]);
            }
            fclose($out);
        }, 200, $headers);
    }

    // --- Entregas por estado y rango ---
    public function reporteEntregas(Request $request)
    {
        $request->validate([
            'estado' => ['nullable', 'in:pendiente,realizada,cancelada'],
            'desde'  => ['nullable', 'date'],
            'hasta'  => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $q = Entrega::query();
        if ($e = $request->estado) $q->where('estado', $e);
        if ($d = $request->desde)  $q->whereDate('created_at', '>=', $d);
        if ($h = $request->hasta)  $q->whereDate('created_at', '<=', $h);

        $entregas = $q->orderByDesc('created_at')->paginate(15)->withQueryString();

        // KPIs
        $kpis = [
            'pendientes' => Entrega::where('estado', 'pendiente')->count(),
            'realizadas' => Entrega::where('estado', 'realizada')->count(),
        ];

        return view('reportes.entregas', compact('entregas', 'kpis'));
    }

    // --- Productos: bajo stock y top vendidos ---
    public function reporteProductos(Request $request)
    {
        $bajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual')->get();

        $top = Producto::withCount('entregas')
            ->orderByDesc('entregas_count')->take(20)->get();

        return view('reportes.productos', compact('bajo', 'top'));
    }
}
