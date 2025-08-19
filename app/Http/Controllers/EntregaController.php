<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Producto;
use App\Models\Factura as FacturaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class EntregaController extends Controller
{
    public function index()
    {
        $entregas = Entrega::with(['cliente', 'repartidor', 'productos'])
            ->latest('id')
            ->paginate(10);

        return view('entregas.index', compact('entregas'));
    }

    public function create()
    {
        $clientes     = Cliente::all();
        $repartidores = User::where('rol', 'repartidor')->get();
        $productos    = Producto::all();

        return view('entregas.create', compact('clientes', 'repartidores', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'    => 'required|exists:clientes,id',
            'repartidor_id' => 'nullable|exists:users,id',
            'fecha_hora'    => 'required|date',
            'estado'        => 'required|in:pendiente,realizada,cancelada',
            'productos'     => 'required|array',
            'productos.*'   => 'exists:productos,id',
            'cantidades'    => 'required|array',
            'cantidades.*'  => 'integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $entrega = Entrega::create($request->only([
                'cliente_id',
                'repartidor_id',
                'fecha_hora',
                'estado'
            ]));

            foreach ($request->productos as $idx => $productoId) {
                $entrega->productos()->attach($productoId, [
                    'cantidad' => $request->cantidades[$idx]
                ]);
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
        $entrega->load('productos');

        $clientes     = Cliente::orderBy('nombre')->get();
        $repartidores = User::where('rol', 'repartidor')->orderBy('name')->get();
        $productos    = Producto::orderBy('nombre')->get();

        // Cantidades actuales indexadas por id de producto
        $cantidadesActuales = $entrega->productos
            ->pluck('pivot.cantidad', 'id')
            ->toArray();

        return view('entregas.edit', compact(
            'entrega', 'clientes', 'repartidores', 'productos', 'cantidadesActuales'
        ));
    }

    public function update(Request $request, Entrega $entrega)
    {
        $validatedData = $request->validate([
            'cliente_id'    => 'required|exists:clientes,id',
            'repartidor_id' => 'required|exists:users,id',
            'fecha_hora'    => 'required|date',
            'estado'        => 'required|in:pendiente,realizada,cancelada',
            'productos'     => 'nullable|array',
            'productos.*'   => 'exists:productos,id',
            'cantidades'    => 'nullable|array',
            'cantidades.*'  => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $estadoAnterior = $entrega->estado;
            $estadoNuevo    = $validatedData['estado'];

            $entrega->update([
                'cliente_id'    => $validatedData['cliente_id'],
                'repartidor_id' => $validatedData['repartidor_id'],
                'fecha_hora'    => $validatedData['fecha_hora'],
                'estado'        => $estadoNuevo,
            ]);

            // --- sincronizado de productos/cantidades ---
            $productos  = $request->input('productos', []);
            $cantidades = $request->input('cantidades', []);
            $syncData   = [];

            foreach ($productos as $idx => $productoId) {
                $cantidad = (int) ($cantidades[$productoId] ?? $cantidades[$idx] ?? 0);
                if ($cantidad > 0) {
                    $syncData[$productoId] = ['cantidad' => $cantidad];
                }
            }
            $entrega->productos()->sync($syncData);

            // --- si pasa a REALIZADA: descuenta stock y crea factura ---
            if ($estadoNuevo === 'realizada' && $estadoAnterior !== 'realizada') {
                $total = 0.0;

                $entrega->load('productos');
                foreach ($entrega->productos as $p) {
                    $c = (int) $p->pivot->cantidad;
                    $total += (float) $p->precio_unitario * $c;
                    $p->decrement('stock_actual', $c);
                }

                FacturaModel::updateOrCreate(
                    ['entrega_id' => $entrega->id],
                    ['subtotal' => $total, 'total' => $total, 'fecha_emision' => now()]
                );

                // 👇 REGISTRO DE AUDITORÍA OPCIONAL
                // Si la tabla 'auditorias' NO existe, se salta sin romper la transacción.
                if (class_exists(\App\Models\Auditoria::class) && Schema::hasTable('auditorias')) {
                    try {
                        \App\Models\Auditoria::create([
                            'usuario_id' => Auth::id(),
                            'accion'     => "Entrega #{$entrega->id} marcada como realizada. Factura generada por $" . number_format($total, 2),
                        ]);
                    } catch (\Throwable $e) {
                        // Opcional: loguea si quieres, pero NO detengas la operación principal
                        // \Log::warning('No se pudo guardar auditoría: '.$e->getMessage());
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('entregas.index')
                ->with('success', "Entrega #{$entrega->id} actualizada a {$estadoNuevo}.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar la entrega: '.$e->getMessage());
        }
    }

    /**
     * Cambio rápido solo de estado (desde el listado)
     */
    public function updateEstado(Request $request, Entrega $entrega)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,realizada,cancelada',
        ]);

        $entrega->update(['estado' => $request->estado]);

        return back()->with('success', "Entrega #{$entrega->id} actualizada a {$request->estado}.");
    }

    public function destroy(Entrega $entrega)
    {
        $entrega->delete();
        return redirect()->route('entregas.index')->with('success', 'Entrega eliminada.');
    }
}