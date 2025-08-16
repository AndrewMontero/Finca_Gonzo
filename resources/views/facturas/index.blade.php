@extends('layouts.app')

@section('title','Facturas')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-3">
  <div>
    <h1 class="h3 mb-1">Facturas</h1>
    <small class="text-muted">Listado de facturas</small>
  </div>
  <a href="{{ route('facturas.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Nueva Factura (desde Entrega)
  </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@php
// Numeración consecutiva por página (NO reescribimos IDs reales en la BD)
$n = ($facturas->currentPage() - 1) * $facturas->perPage();
$fmt = fn($n) => '₡' . number_format($n, 2);
@endphp

<table class="table align-middle">
  <thead>
    <tr>
      <th>#</th>
      <th>Cliente</th>
      <th>Total</th>
      <th>Fecha</th>
      <th class="text-end">Acciones</th>
    </tr>
  </thead>
  <tbody>
    @forelse($facturas as $f)
    <tr>
      <td>{{ ++$n }}</td>
      <td>{{ optional(optional($f->entrega)->cliente)->nombre ?? '—' }}</td>
      <td>{{ $fmt($f->total) }}</td>
      <td>{{ $f->created_at }}</td>
      <td class="text-end">
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('facturas.show', $f) }}" title="Ver"><i class="bi bi-eye"></i></a>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('facturas.print', $f) }}" target="_blank" title="Imprimir"><i class="bi bi-printer"></i></a>
        <form class="d-inline" action="{{ route('facturas.email', $f) }}" method="post" title="Enviar">
          @csrf
          <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-envelope"></i></button>
        </form>
        <form class="d-inline" action="{{ route('facturas.destroy', $f) }}" method="post" onsubmit="return confirm('¿Eliminar la factura #{{ $f->id }}?');">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
        </form>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center text-muted">No hay facturas.</td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="mt-3">
  {{ $facturas->links() }}
</div>


@if($facturas->hasPages())
<div class="card-footer bg-white">
  {{ $facturas->links() }}
</div>
@endif
</div>

@endsection