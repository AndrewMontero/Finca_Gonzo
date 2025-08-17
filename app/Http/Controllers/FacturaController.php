<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

// 👇 Importaciones correctas
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\FacturaMail;



class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['entrega.cliente', 'entrega.productos'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $clientes  = class_exists(\App\Models\Cliente::class)  ? \App\Models\Cliente::orderBy('nombre')->get()   : collect();
        $productos = class_exists(\App\Models\Producto::class) ? \App\Models\Producto::orderBy('nombre')->get() : collect();

        return view('facturas.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'   => ['nullable', 'integer', 'exists:clientes,id'],
            'subtotal'     => ['required', 'numeric', 'min:0'],
            'total'        => ['required', 'numeric', 'min:0'],
            'productos'    => ['nullable', 'array'],
            'productos.*'  => ['integer', 'exists:productos,id'],
            'cantidades'   => ['nullable', 'array'],
            'cantidades.*' => ['integer', 'min:1'],
        ]);

        try {
            $entrega = Entrega::create([
                'cliente_id'    => $data['cliente_id'] ?? null,
                'repartidor_id' => null,
                'fecha_hora'    => now(),
                'estado'        => 'pendiente',
            ]);

            if (!empty($data['productos']) && !empty($data['cantidades'])) {
                $sync = [];
                foreach ($data['productos'] as $i => $productoId) {
                    $cantidad = (int)($data['cantidades'][$i] ?? 1);
                    $sync[$productoId] = ['cantidad' => $cantidad];
                }
                $entrega->productos()->sync($sync);
            }

            $factura = Factura::create([
                'entrega_id' => $entrega->id,
                'subtotal'   => $data['subtotal'],
                'total'      => $data['total'],
            ]);

            return redirect()
                ->route('facturas.index')
                ->with('success', 'Factura creada correctamente (ID: ' . $factura->id . ').');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    public function show(Factura $factura)
    {
        $factura->load(['entrega.cliente', 'entrega.productos']);
        return view('facturas.show', compact('factura'));
    }

    // 🖨️ Imprimir / ver PDF
    public function print(Factura $factura)
    {
        $factura->load(['entrega.cliente', 'entrega.productos']);

        $pdf = Pdf::loadView('facturas.pdf', ['factura' => $factura])
            ->setPaper('letter');

        return $pdf->stream('factura-' . $factura->id . '.pdf');
    }

    // ✉️ Enviar por correo con PDF adjunto
    public function email(Factura $factura)
    {
        $factura->load(['entrega.cliente', 'entrega.productos']);

        $correo = optional(optional($factura->entrega)->cliente)->correo;
        if (!$correo) {
            return back()->with('error', 'No hay correo de cliente para enviar la factura.');
        }

        $pdfBinary = Pdf::loadView('facturas.pdf', ['factura' => $factura])
            ->setPaper('letter')
            ->output();

        Mail::to($correo)->send(new \App\Mail\FacturaMail($factura, $pdfBinary));

        return back()->with('success', 'Factura enviada a ' . $correo);
    }


    // 🗑️ Eliminar
    public function destroy(Factura $factura): RedirectResponse
    {
        $factura->delete();

        return redirect()
            ->route('facturas.index')
            ->with('success', 'Factura eliminada correctamente.');
    }
}
