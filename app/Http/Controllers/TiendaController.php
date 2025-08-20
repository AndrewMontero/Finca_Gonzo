<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Entrega;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiendaController extends Controller
{
    /** Catálogo de productos (sin cambios funcionales) */
    public function catalogo()
    {
        $productos = Producto::orderBy('nombre')->get();
        $cart = session('cart', []);       // [producto_id => [...]]
        return view('tienda.index', compact('productos', 'cart'));
    }

    /** Ver carrito (items con clave = producto_id para poder actualizar por ID) */
    public function carrito()
    {
        $cart  = session('cart', []);      // [producto_id => ['nombre','precio','cantidad','stock']]
        $items = collect($cart);           // ya viene indexado por producto_id
        $total = $items->reduce(fn($acc, $i) => $acc + ($i['precio'] * $i['cantidad']), 0.0);

        // ⬇️ La vista itera por $items como $pid => $item, así mantiene el ID real
        return view('tienda.carrito', compact('items', 'total'));
    }

    /** Agregar al carrito
     *  - Acepta tanto name="cantidad" como name="qty" desde la vista
     *  - Suma a lo que ya hubiera en el carrito
     */
    public function agregar(Request $request, Producto $producto)
    {
        // Soportar ambos nombres de campo sin obligarte a tocar la vista
        $qty = (int) ($request->input('cantidad', $request->input('qty', 1)));
        $qty = max(1, $qty);

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

    /** Actualizar cantidades / eliminar (cantidades[ID] = 0 elimina) */
    public function actualizar(Request $request)
    {
        $cantidades = (array) $request->input('cantidades', []);
        $cart = session('cart', []);

        foreach ($cantidades as $pid => $nueva) {
            $nueva = (int) $nueva;
            if ($nueva <= 0) {
                unset($cart[$pid]);                  // eliminar
            } elseif (isset($cart[$pid])) {
                $cart[$pid]['cantidad'] = $nueva;    // actualizar
            }
        }

        session(['cart' => $cart]);
        return back()->with('success', 'Carrito actualizado.');
    }

    /** Vaciar carrito (usado por el botón "Vaciar Carrito" de la vista) */
    public function vaciarCarrito()
    {
        session()->forget('cart');
        return back()->with('success', 'Carrito vaciado.');
    }

    /** Finalizar compra => crea Entrega (pendiente) + detalle_entrega */

    public function finalizar(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Tu carrito está vacío.');
        }

        $user = auth()->user();

        // Buscar o crear cliente con correo
        $cliente = \App\Models\Cliente::firstOrCreate(
            ['correo' => $user->email], // clave de búsqueda
            [
                'nombre'    => $user->name ?? 'Cliente',
                'telefono'  => 'N/A',          // pon algo si tu tabla no permite NULL
                'ubicacion' => 'N/A',          // igual aquí
            ]
        );


        try {
            DB::beginTransaction();

            $entrega = \App\Models\Entrega::create([
                'cliente_id'    => $cliente->id,
                'repartidor_id' => null,
                'fecha_hora'    => now(),
                'estado'        => 'pendiente',
            ]);

            // Detalle
            foreach ($cart as $productoId => $row) {
                $cantidad = (int) ($row['cantidad'] ?? 0);
                if ($cantidad > 0 && \App\Models\Producto::find($productoId)) {
                    $entrega->productos()->attach($productoId, ['cantidad' => $cantidad]);
                }
            }

            DB::commit();
            session()->forget('cart');

            return redirect()
                ->route('tienda.index')
                ->with('success', "¡Pedido enviado! Tu entrega #{$entrega->id} quedó PENDIENTE.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'No se pudo finalizar el pedido: ' . $e->getMessage());
        }
    }
    public function pedidos()
    {
        $user = auth()->user();

        // Localiza el cliente por correo (tal como se usa en finalizar())
        $cliente = \App\Models\Cliente::where('correo', $user->email)->first();

        // Si aún no existe, mostramos página vacía amable
        if (!$cliente) {
            return view('tienda.pedidos', [
                'entregas' => collect(), // colección vacía
            ]);
        }

        // Trae entregas del cliente con relaciones necesarias
        $entregas = \App\Models\Entrega::with(['productos', 'factura'])
            ->where('cliente_id', $cliente->id)
            ->latest('id')
            ->paginate(10);

        return view('tienda.pedidos', compact('entregas'));
    }
}
