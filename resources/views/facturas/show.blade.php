@extends('layouts.app')
@section('title','Factura #'.$factura->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Factura #{{ $factura->id }}</h1>
        <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="text-muted">Cliente</div>
                    <div class="fw-semibold">
                        @if(method_exists($factura,'cliente') && $factura->cliente)
                        {{ $factura->cliente->nombre }}
                        @else
                        —
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="text-muted">Total</div>
                    <div class="fw-semibold">₡{{ number_format($factura->total, 2) }}</div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="text-muted">Fecha</div>
                    <div class="fw-semibold">{{ $factura->created_at }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection