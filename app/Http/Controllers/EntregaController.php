<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntregaController extends Controller
{
    public function index()
    {
        $entregas = Entrega::with(['cliente', 'repartidor', 'productos'])->paginate(10);
        return view('entregas.index', compact('entregas'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $repartidores = User::where('rol', 'repartidor')->get();
        $productos = Producto::all();

        return view('entregas.create', compact('clientes', 'repartidores', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'repartidor_id' => 'required|exists:users,id',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:pendiente,realizada,cancelada',
            'productos' => 'required|array',
            'productos.*' => 'exists:productos,id',
            'cantidades' => 'required|array',
            'cantidades.*' => 'integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $entrega = Entrega::create($request->only(['cliente_id', 'repartidor_id', 'fecha_hora', 'estado']));

            foreach ($request->productos as $index => $productoId) {
                $cantidad = $request->cantidades[$index];
                $entrega->productos()->attach($productoId, ['cantidad' => $cantidad]);
            }
        });

        return redirect()->route('entregas.index')->with('success', 'Entrega creada exitosamente.');
    }

    public function show(Entrega $entrega)
    {
        $entrega->load(['cliente', 'repartidor', 'productos']);
        return view('entregas.show', compact('entrega'));
    }

    public function edit(Entrega $entrega)
    {
        $clientes = Cliente::all();
        $repartidores = User::where('rol', 'repartidor')->get();
        $productos = Producto::all();
        $entrega->load('productos');

        return view('entregas.edit', compact('entrega', 'clientes', 'repartidores', 'productos'));
    }

    public function update(Request $request, Entrega $entrega)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'repartidor_id' => 'required|exists:users,id',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:pendiente,realizada,cancelada',
            'productos' => 'required|array',
            'productos.*' => 'exists:productos,id',
            'cantidades' => 'required|array',
            'cantidades.*' => 'integer|min:1',
        ]);

        DB::transaction(function () use ($request, $entrega) {
            $estadoAnterior = $entrega->estado;

            $entrega->update($request->only(['cliente_id', 'repartidor_id', 'fecha_hora', 'estado']));
            $syncData = [];
            foreach ($request->productos as $index => $productoId) {
                $syncData[$productoId] = ['cantidad' => $request->cantidades[$index]];
            }
            $entrega->productos()->sync($syncData);

            if ($request->estado === 'realizada' && $estadoAnterior !== 'realizada') {
                $total = 0;

                foreach ($entrega->productos as $producto) {
                    $cantidad = $producto->pivot->cantidad;
                    $total += $producto->precio_unitario * $cantidad;

                    $producto->decrement('stock_actual', $cantidad);
                }

                \App\Models\Factura::firstOrCreate(
                    ['entrega_id' => $entrega->id],
                    [
                        'subtotal' => $total,
                        'total' => $total
                    ]
                );

                if (class_exists(\App\Models\Auditoria::class)) {
                    \App\Models\Auditoria::create([
                        'usuario_id' => auth()->id(), // ✅ Funciona correctamente
                        'accion' => "Entrega #{$entrega->id} completada, factura generada.",
                    ]);
                }
            }
        });

        return redirect()->route('entregas.index')->with('success', 'Entrega actualizada correctamente.');
    }

    public function destroy(Entrega $entrega)
    {
        $entrega->delete();
        return redirect()->route('entregas.index')->with('success', 'Entrega eliminada.');
    }
}
