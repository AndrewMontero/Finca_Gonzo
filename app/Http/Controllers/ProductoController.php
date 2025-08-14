<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::orderBy('nombre')->paginate(10);

        // Estadísticas
        $totalProductos = Producto::count();
        $stockBajo = Producto::whereRaw('stock_actual <= stock_minimo')->count();
        $stockNormal = $totalProductos - $stockBajo;
        $valorInventario = Producto::selectRaw('SUM(stock_actual * precio_unitario) as total')->first()->total ?? 0;

        return view('productos.index', compact(
            'productos',
            'totalProductos',
            'stockBajo',
            'stockNormal',
            'valorInventario'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos',
            'unidad_medida' => 'required|string|in:kg,lb,unidad,caja,saco,litro',
            'precio_unitario' => 'required|numeric|min:0|max:999999.99',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:1|gt:stock_minimo',
        ], [
            'nombre.unique' => 'Ya existe un producto con este nombre.',
            'stock_maximo.gt' => 'El stock máximo debe ser mayor al mínimo.',
        ]);

        $producto = Producto::create($request->all());

        // Log de auditoría
        try {
            Auditoria::create([
                'user_id' => Auth::id(),
                'accion' => 'crear',
                'tabla' => 'productos',
                'registro_id' => $producto->id,
                'datos_nuevos' => json_encode($producto->toArray())
            ]);
        } catch (\Exception $e) {
            // Si no existe la tabla auditorías, continúa sin error
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        // Calcular estadísticas completas del producto
        $estadisticas = [
            'dias_creado' => $producto->created_at->diffInDays(now()),
            'valor_total_stock' => $producto->stock_actual * $producto->precio_unitario,
            'valor_inventario' => $producto->stock_actual * $producto->precio_unitario, // Agregada esta clave
            'porcentaje_stock' => $producto->stock_maximo > 0 ?
                round(($producto->stock_actual / $producto->stock_maximo) * 100, 2) : 0,
            'estado_stock' => $producto->stock_actual <= $producto->stock_minimo ? 'Bajo' :
                ($producto->stock_actual >= $producto->stock_maximo * 0.8 ? 'Alto' : 'Normal'),
            'necesita_reposicion' => $producto->stock_actual <= $producto->stock_minimo,
            'stock_disponible' => max(0, $producto->stock_maximo - $producto->stock_actual),
            'rotacion_estimada' => $producto->stock_actual > 0 ?
                round($producto->stock_actual / max(1, $producto->stock_minimo), 2) : 0,
            'alerta_critica' => $producto->stock_actual == 0,
            'margen_seguridad' => $producto->stock_actual - $producto->stock_minimo,
            'capacidad_utilizada' => $producto->stock_maximo > 0 ?
                round(($producto->stock_actual / $producto->stock_maximo) * 100, 1) : 0
        ];

        return view('productos.show', compact('producto', 'estadisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $producto->id,
            'unidad_medida' => 'required|string|in:kg,lb,unidad,caja,saco,litro',
            'precio_unitario' => 'required|numeric|min:0|max:999999.99',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:1|gt:stock_minimo',
        ]);

        $datosAnteriores = $producto->toArray();
        $producto->update($request->all());

        // Log de auditoría
        try {
            Auditoria::create([
                'user_id' => Auth::id(),
                'accion' => 'actualizar',
                'tabla' => 'productos',
                'registro_id' => $producto->id,
                'datos_anteriores' => json_encode($datosAnteriores),
                'datos_nuevos' => json_encode($producto->fresh()->toArray())
            ]);
        } catch (\Exception $e) {
            // Si no existe la tabla auditorías, continúa sin error
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $datosAnteriores = $producto->toArray();

        // Log de auditoría antes de eliminar
        try {
            Auditoria::create([
                'user_id' => Auth::id(),
                'accion' => 'eliminar',
                'tabla' => 'productos',
                'registro_id' => $producto->id,
                'datos_anteriores' => json_encode($datosAnteriores)
            ]);
        } catch (\Exception $e) {
            // Si no existe la tabla auditorías, continúa sin error
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * Mostrar productos con stock bajo
     */
    public function stockBajo()
    {
        $productos = Producto::whereRaw('stock_actual <= stock_minimo')
            ->orderBy('stock_actual', 'asc')
            ->get();

        return view('productos.stock-bajo', compact('productos'));
    }

    /**
     * Actualizar stock (método original)
     */
    public function actualizarStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $producto = Producto::findOrFail($id);
        $stockAnterior = $producto->stock_actual;
        $producto->stock_actual = $request->stock;
        $producto->save();

        return redirect()->route('productos.index')
            ->with('success', 'Stock actualizado correctamente.');
    }

    /**
     * API para obtener productos
     */
    public function api()
    {
        $productos = Producto::select('id', 'nombre', 'precio_unitario', 'unidad_medida', 'stock_actual')
            ->orderBy('nombre')
            ->get();

        return response()->json($productos);
    }

    /**
     * Actualizar stock via AJAX (nuevo método para el modal)
     */
    public function actualizarStockAjax(Request $request, $id)
    {
        $request->validate([
            'stock_actual' => 'required|integer|min:0',
            'motivo' => 'nullable|string|max:500'
        ]);

        try {
            $producto = Producto::findOrFail($id);
            $stockAnterior = $producto->stock_actual;

            // Validar que no exceda el stock máximo
            if ($request->stock_actual > $producto->stock_maximo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El stock no puede ser mayor al máximo permitido (' . $producto->stock_maximo . ')'
                ], 400);
            }

            $producto->stock_actual = $request->stock_actual;
            $producto->save();

            // Log de auditoría
            try {
                Auditoria::create([
                    'user_id' => Auth::id(),
                    'accion' => 'actualizar_stock',
                    'tabla' => 'productos',
                    'registro_id' => $producto->id,
                    'datos_anteriores' => json_encode(['stock_actual' => $stockAnterior]),
                    'datos_nuevos' => json_encode([
                        'stock_actual' => $producto->stock_actual,
                        'motivo' => $request->motivo ?? 'Sin motivo especificado'
                    ])
                ]);
            } catch (\Exception $e) {
                // Si no existe la tabla auditorías, continúa sin error
            }

            return response()->json([
                'success' => true,
                'message' => 'Stock actualizado correctamente',
                'nuevo_stock' => $producto->stock_actual
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
