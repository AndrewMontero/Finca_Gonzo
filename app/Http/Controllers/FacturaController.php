<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    /**
     * Listado básico de facturas
     */
    public function index()
    {
        $facturas = Factura::orderByDesc('created_at')->paginate(10);

        // Si tienes modelo Cliente y relación, aquí podrías eager load:
        // $facturas = Factura::with('cliente')->orderByDesc('created_at')->paginate(10);

        return view('facturas.index', compact('facturas'));
    }

    /**
     * Formulario para crear una factura
     */
    public function create()
    {
        // Intentar cargar clientes y productos si existen esos modelos
        $clientes  = class_exists(\App\Models\Cliente::class)  ? \App\Models\Cliente::orderBy('nombre')->get()   : collect();
        $productos = class_exists(\App\Models\Producto::class) ? \App\Models\Producto::orderBy('nombre')->get() : collect();

        return view('facturas.create', compact('clientes', 'productos'));
    }

    /**
     * Guardar la factura
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => ['nullable', 'integer'],
            'total'      => ['required', 'numeric', 'min:0'],
            'notas'      => ['nullable', 'string', 'max:500'],
            // Si luego guardamos renglones, aquí validamos items[]...
        ], [
            'total.required' => 'El total es obligatorio.',
            'total.numeric'  => 'El total debe ser numérico.',
            'total.min'      => 'El total no puede ser negativo.',
        ]);

        $factura = Factura::create([
            'cliente_id' => $data['cliente_id'] ?? null,
            'total'      => $data['total'],
            // 'detalle'  => json_encode($request->input('items', [])), // si tienes columna
            'created_at' => now(),
        ]);

        // TODO: si en el futuro agregamos "detalle de factura", aquí se guardan los ítems.

        return redirect()
            ->route('facturas.index')
            ->with('success', 'Factura creada correctamente (ID: '.$factura->id.').');
    }

    /**
     * (Opcional) Detalle simple de una factura
     */
    public function show(Factura $factura)
    {
        return view('facturas.show', compact('factura'));
    }
}
