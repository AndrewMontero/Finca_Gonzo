@extends('layouts.app')
@section('title', 'Factura #'.$factura->id)

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Factura #{{ $factura->id }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('facturas.print', $factura) }}" class="btn btn-outline-secondary" target="_blank">
                <i class="bi bi-printer"></i> Imprimir
            </a>
            <form action="{{ route('facturas.email', $factura) }}" method="post">
                @csrf
                <button class="btn btn-primary"><i class="bi bi-envelope"></i> Enviar</button>
            </form>
            <a href="{{ route('facturas.index') }}" class="btn btn-light">Volver</a>
        </div>
    </div>

    @php
        $entrega   = $factura->entrega;
        $cliente   = optional($entrega)->cliente;
        $productos = optional($entrega)->productos ?? collect();
        $fmt = fn($n) => '₡' . number_format($n, 2);
    @endphp

    <div class="card mb-3">
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-4">
                    <div class="text-muted small">Cliente</div>
                    <div class="fw-semibold">{{ $cliente->nombre ?? '—' }}</div>
                    <div>{{ $cliente->correo ?? '—' }}</div>
                    <div class="small">Tel: {{ $cliente->telefono ?? '—' }}</div>
                    <div class="small">{{ $cliente->ubicacion ?? '' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Fecha</div>
                    <div class="fw-semibold">{{ $factura->created_at }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Total</div>
                    <div class="fw-semibold">{{ $fmt($factura->total) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $p)
                        @php
                            $cant = (int) ($p->pivot->cantidad ?? 1);
                            $sub  = $p->precio_unitario * $cant;
                        @endphp
                        <tr>
                            <td>{{ $p->nombre }}</td>
                            <td class="text-center">{{ $cant }}</td>
                            <td class="text-end">{{ $fmt($sub) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td>Entrega #{{ $entrega->id }}</td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ $fmt($factura->subtotal) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end gap-4">
            <div><strong>Subtotal:</strong> {{ $fmt($factura->subtotal) }}</div>
            <div><strong>Total:</strong> {{ $fmt($factura->total) }}</div>
        </div>
    </div>
</div>
@endsection
