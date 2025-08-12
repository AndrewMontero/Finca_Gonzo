<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Entrega;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    /**
     * Listado básico de facturas
     */
    public function index()
    {
        $facturas = Factura::with('entrega')->orderByDesc('created_at')->paginate(10);
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
            'subtotal'   => ['required', 'numeric', 'min:0'],
            'notas'      => ['nullable', 'string', 'max:500'],
        ], [
            'total.required' => 'El total es obligatorio.',
            'total.numeric'  => 'El total debe ser numérico.',
            'total.min'      => 'El total no puede ser negativo.',
            'subtotal.required' => 'El subtotal es obligatorio.',
            'subtotal.numeric'  => 'El subtotal debe ser numérico.',
            'subtotal.min'      => 'El subtotal no puede ser negativo.',
        ]);

        try {
            // 1. PRIMERO: Crear la entrega (obligatoria para la factura)
            $entrega = Entrega::create([
                'cliente_id' => $data['cliente_id'] ?? null,
                'repartidor_id' => null, // Puedes asignar un repartidor por defecto si quieres
                'fecha_hora' => now(),
                'estado' => 'pendiente', // o el estado que manejes
            ]);

            // 2. SEGUNDO: Crear la factura asociada a la entrega
            $factura = Factura::create([
                'entrega_id' => $entrega->id, // ✅ ESTO es lo que faltaba
                'subtotal'   => $data['subtotal'],
                'total'      => $data['total'],
            ]);

            // ✅ REDIRIGIR a la lista de facturas con mensaje de éxito
            return redirect()
                ->route('facturas.index')
                ->with('success', 'Factura creada correctamente (ID: '.$factura->id.').');

        } catch (\Exception $e) {
            // Si algo falla, regresar con error
            return back()
                ->withInput()
                ->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    /**
     * (Opcional) Detalle simple de una factura
     */
    public function show(Factura $factura)
    {
        $factura->load('entrega.cliente'); // Cargar relaciones
        return view('facturas.show', compact('factura'));
    }
}
