<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Entrega;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiendaController extends Controller
{
    /** Catálogo simple de productos */
    public function catalogo()
    {
        $productos = Producto::orderBy('nombre')->get();
        $cart = session('cart', []); // para mostrar cantidades actuales
        return view('tienda.index', compact('productos','cart'));
    }

    /** Agregar al carrito en sesión */
    public function agregar(Request $request, Producto $producto)
    {
        $qty = max(1, (int) $request->input('qty', 1));

        $cart = session('cart', []);
        $cart[$producto->id] = [
            'nombre'   => $producto->nombre,
            'precio'   => (float) ($producto->precio_unitario ?? 0),
            'cantidad' => ($cart[$producto->id]['cantidad'] ?? 0) + $qty,
            'stock'    => (int) $producto->stock_actual,
        ];

        session(['cart' => $cart]);

        return back()->with('success', "{$producto->nombre} agregado al carrito.");
    }

    /** Ver carrito */
    public function carrito()
    {
        $cart  = session('cart', []);
        $items = collect($cart);
        $total = $items->reduce(fn($acc,$i) => $acc + ($i['precio'] * $i['cantidad']), 0.0);

        return view('tienda.carrito', compact('items','total'));
    }

    /** Actualizar cantidades / eliminar */
    public function actualizar(Request $request)
    {
        $cantidades = (array) $request->input('cantidades', []);
        $cart = session('cart', []);

        foreach ($cart as $pid => &$item) {
            $nueva = (int) ($cantidades[$pid] ?? $item['cantidad']);
            if ($nueva <= 0) {
                unset($cart[$pid]);
            } else {
                $item['cantidad'] = $nueva;
            }
        }
        unset($item);

        session(['cart' => $cart]);
        return back()->with('success', 'Carrito actualizado.');
    }

    /** Finalizar compra -> crea Entrega + detalle_entrega (estado: pendiente) */
    public function finalizar(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Tu carrito está vacío.');
        }

        // 👇 localizar o crear el Cliente relacionado con el usuario
        // (ajusta según los campos obligatorios que tengas en "clientes")
        $user = auth()->user();
        $cliente = Cliente::firstOrCreate(
            ['nombre' => $user->name],   // clave simple para evitar duplicados
            []                           // completa más campos si tu tabla los exige
        );

        DB::beginTransaction();
        try {
            // Crear Entrega pendiente sin repartidor asignado
            $entrega = Entrega::create([
                'cliente_id'    => $cliente->id,
                'repartidor_id' => null,
                'fecha_hora'    => now(),
                'estado'        => 'pendiente',
            ]);

            // Adjuntar productos al pivote con cantidad
            $sync = [];
            foreach ($cart as $productoId => $item) {
                $sync[$productoId] = ['cantidad' => (int) $item['cantidad']];
            }
            $entrega->productos()->sync($sync);

            DB::commit();

            // limpiar carrito
            session()->forget('cart');

            return redirect()
                ->route('tienda.carrito')
                ->with('success', "¡Pedido creado! Tu entrega #{$entrega->id} quedó pendiente. El administrador la gestionará.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'No se pudo finalizar la compra: '.$e->getMessage());
        }
    }
}
