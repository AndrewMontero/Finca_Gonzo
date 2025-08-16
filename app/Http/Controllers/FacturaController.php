<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use PDF;     // barryvdh/laravel-dompdf
use Mail;    // envío de mails si lo usas
use App\Mail\FacturaMail;


class FacturaController extends Controller
{
    /**
     * Listado de facturas
     */
    public function index()
    {
        // Trae entrega, cliente y productos para que el dashboard tenga todo
        $facturas = Factura::with(['entrega.cliente', 'entrega.productos'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('facturas.index', compact('facturas'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $clientes  = class_exists(\App\Models\Cliente::class)  ? \App\Models\Cliente::orderBy('nombre')->get()   : collect();
        $productos = class_exists(\App\Models\Producto::class) ? \App\Models\Producto::orderBy('nombre')->get() : collect();

        return view('facturas.create', compact('clientes', 'productos'));
    }

    /**
     * Guardar factura.
     * - Crea una Entrega (necesaria para el modelo actual).
     * - Si llegan productos[] y cantidades[], los adjunta a la entrega.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'          => ['nullable', 'integer', 'exists:clientes,id'],
            'subtotal'            => ['required', 'numeric', 'min:0'],
            'total'               => ['required', 'numeric', 'min:0'],
            'productos'           => ['nullable', 'array'],
            'productos.*'         => ['integer', 'exists:productos,id'],
            'cantidades'          => ['nullable', 'array'],
            'cantidades.*'        => ['integer', 'min:1'],
        ]);

        try {
            // 1) Crear entrega base
            $entrega = Entrega::create([
                'cliente_id'    => $data['cliente_id'] ?? null,
                'repartidor_id' => null,
                'fecha_hora'    => now(),
                'estado'        => 'pendiente',
            ]);

            // 2) Adjuntar productos (opcional)
            if (!empty($data['productos']) && !empty($data['cantidades'])) {
                $sync = [];
                foreach ($data['productos'] as $i => $productoId) {
                    $cantidad = (int)($data['cantidades'][$i] ?? 1);
                    $sync[$productoId] = ['cantidad' => $cantidad];
                }
                $entrega->productos()->sync($sync);
            }

            // 3) Crear factura
            $factura = Factura::create([
                'entrega_id' => $entrega->id,
                'subtotal'   => $data['subtotal'],
                'total'      => $data['total'],
            ]);

            return redirect()
                ->route('facturas.index')
                ->with('success', 'Factura creada correctamente (ID: '.$factura->id.').');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    /**
     * Detalle de una factura
     */
    public function show(Factura $factura)
    {
        $factura->load(['entrega.cliente', 'entrega.productos']);
        return view('facturas.show', compact('factura'));
    }

    /**
     * PDF (vista previa/impresión)
     */
    public function print(Factura $factura)
    {
        $factura->load(['entrega.cliente', 'entrega.productos']);
        $pdf = PDF::loadView('facturas.pdf', ['factura' => $factura])->setPaper('letter');

        return $pdf->stream('factura-' . $factura->id . '.pdf');
    }

    /**
     * Enviar por correo (adjunta el PDF)
     */
    public function email(Factura $factura)
    {
        $factura->load(['entrega.cliente', 'entrega.productos']);

        $cliente = optional(optional($factura->entrega)->cliente);
        $correo  = $cliente->correo ?? null;

        if (!$correo) {
            return back()->with('error', 'No hay correo de cliente para enviar la factura.');
        }

        $pdfBinary = PDF::loadView('facturas.pdf', ['factura' => $factura])
                        ->setPaper('letter')
                        ->output();

        Mail::to($correo)->send(new FacturaMail($factura, $pdfBinary));

        return back()->with('success', 'Factura enviada a ' . $correo);
    }
     /**
     * 👉 Eliminar factura
     */
    public function destroy(Factura $factura): RedirectResponse
    {
        // Solo borramos la factura. Si quieres, podrías decidir también
        // borrar la entrega asociada si no se usa en otro lado.
        $factura->delete();

        return redirect()
            ->route('facturas.index')
            ->with('success', 'Factura eliminada correctamente.');
    }
}
