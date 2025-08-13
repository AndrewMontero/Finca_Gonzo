<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::orderBy('nombre')->get();

        // Productos con stock bajo
        $productosStockBajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();

        return view('productos.index', compact('productos', 'productosStockBajo'));
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
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:productos,nombre',
            'unidad_medida' => 'required|string|max:50',
            'precio_unitario' => 'required|numeric|min:0|max:999999.99',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:1',
            'stock_actual' => 'required|integer|min:0',
        ]);

        // Validación personalizada: stock_maximo debe ser mayor que stock_minimo
        $validator->after(function ($validator) use ($request) {
            if ($request->stock_maximo <= $request->stock_minimo) {
                $validator->errors()->add('stock_maximo', 'El stock máximo debe ser mayor al stock mínimo.');
            }

            if ($request->stock_actual > $request->stock_maximo) {
                $validator->errors()->add('stock_actual', 'El stock actual no puede ser mayor al stock máximo.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Producto::create($request->all());

            return redirect()->route('productos.index')
                ->with('success', 'Producto registrado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = Producto::findOrFail($id);

        // Calcular estadísticas del producto
        $estadisticas = [
            'porcentaje_stock' => $producto->stock_maximo > 0 ?
                round(($producto->stock_actual / $producto->stock_maximo) * 100, 2) : 0,
            'necesita_reposicion' => $producto->stock_actual <= $producto->stock_minimo,
            'valor_inventario' => $producto->stock_actual * $producto->precio_unitario,
        ];

        return view('productos.show', compact('producto', 'estadisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $producto = Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $id,
            'unidad_medida' => 'required|string|max:50',
            'precio_unitario' => 'required|numeric|min:0|max:999999.99',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:1',
            'stock_actual' => 'required|integer|min:0',
        ]);

        // Validación personalizada
        $validator->after(function ($validator) use ($request) {
            if ($request->stock_maximo <= $request->stock_minimo) {
                $validator->errors()->add('stock_maximo', 'El stock máximo debe ser mayor al stock mínimo.');
            }

            if ($request->stock_actual > $request->stock_maximo) {
                $validator->errors()->add('stock_actual', 'El stock actual no puede ser mayor al stock máximo.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $producto->update($request->all());

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            // Verificar si el producto tiene entregas asociadas
            if ($producto->entregas()->count() > 0) {
                return redirect()->route('productos.index')
                    ->with('error', 'No se puede eliminar el producto porque tiene entregas asociadas.');
            }

            $nombreProducto = $producto->nombre;
            $producto->delete();

            return redirect()->route('productos.index')
                ->with('success', "Producto '{$nombreProducto}' eliminado exitosamente.");

        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar stock de un producto
     */
    public function actualizarStock(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'stock_actual' => 'required|integer|min:0|max:' . $producto->stock_maximo,
            'motivo' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $stockAnterior = $producto->stock_actual;
            $producto->update(['stock_actual' => $request->stock_actual]);

            return response()->json([
                'success' => true,
                'message' => 'Stock actualizado correctamente',
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $producto->stock_actual,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos con stock bajo
     */
    public function stockBajo()
    {
        $productos = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual')
            ->get();

        return view('productos.stock-bajo', compact('productos'));
    }

    /**
     * API endpoint para obtener productos (para AJAX)
     */
    public function api()
    {
        $productos = Producto::select('id', 'nombre', 'precio_unitario', 'stock_actual', 'unidad_medida')
            ->where('stock_actual', '>', 0)
            ->orderBy('nombre')
            ->get();

        return response()->json($productos);
    }
}
