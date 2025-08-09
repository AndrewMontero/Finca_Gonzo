@extends('layouts.app')
@section('title','Facturas')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Facturas</h1>
    <a href="{{ route('facturas.create') }}" class="btn btn-primary">
      <i class="bi bi-receipt"></i> Nueva Factura
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
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
                <td>{{ $f->id }}</td>
                <td>
                  @if(property_exists($f,'cliente') && $f->cliente)
                    {{ $f->cliente->nombre }}
                  @else
                    —
                  @endif
                </td>
                <td>₡{{ number_format($f->total, 2) }}</td>
                <td>{{ $f->created_at }}</td>
                <td class="text-end">
                  <a href="{{ route('facturas.show', $f) }}" class="btn btn-sm btn-outline-secondary">
                    Ver
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No hay facturas registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3">
    {{ $facturas->links() }}
  </div>
</div>
@endsection
