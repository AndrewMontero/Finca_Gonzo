<?php

namespace App\Http\Controllers;

use App\Models\DetalleEntrega;
use App\Models\Entrega;
use App\Models\Producto;
use Illuminate\Http\Request;

class DetalleEntregaController extends Controller
{
    public function index()
    {
        $detalles = DetalleEntrega::with(['entrega', 'producto'])->get();
        return view('detalle_entregas.index', compact('detalles'));
    }

    public function create()
    {
        $entregas = Entrega::all();
        $productos = Producto::all();
        return view('detalle_entregas.create', compact('entregas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entrega_id' => 'required|exists:entregas,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        DetalleEntrega::create($request->all());

        return redirect()->route('detalle-entregas.index')
            ->with('success', 'Detalle de entrega creado correctamente.');
    }

    public function show(DetalleEntrega $detalleEntrega)
    {
        return view('detalle_entregas.show', compact('detalleEntrega'));
    }

    public function edit(DetalleEntrega $detalleEntrega)
    {
        $entregas = Entrega::all();
        $productos = Producto::all();
        return view('detalle_entregas.edit', compact('detalleEntrega', 'entregas', 'productos'));
    }

    public function update(Request $request, DetalleEntrega $detalleEntrega)
    {
        $request->validate([
            'entrega_id' => 'required|exists:entregas,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $detalleEntrega->update($request->all());

        return redirect()->route('detalle-entregas.index')
            ->with('success', 'Detalle de entrega actualizado correctamente.');
    }

    public function destroy(DetalleEntrega $detalleEntrega)
    {
        $detalleEntrega->delete();
        return redirect()->route('detalle-entregas.index')
            ->with('success', 'Detalle de entrega eliminado correctamente.');
    }
}
