<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Entrega;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function generarFactura($entregaId)
    {
        $entrega = Entrega::with(['cliente', 'repartidor', 'productos'])->findOrFail($entregaId);

        $pdf = Pdf::loadView('facturas.plantilla', compact('entrega'));

        return $pdf->download('factura_' . $entrega->id . '.pdf');
    }
}
