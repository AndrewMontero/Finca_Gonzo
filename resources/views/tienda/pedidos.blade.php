@extends('layouts.app')
@section('title', 'Mis pedidos')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">
      <i class="bi bi-bag-check me-2"></i>Mis pedidos
    </h1>
    <a href="{{ route('tienda.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-shop me-1"></i> Seguir comprando
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($entregas instanceof \Illuminate\Pagination\LengthAwarePaginator ? $entregas->isEmpty() : $entregas->isEmpty())
    <div class="alert alert-info">
      Aún no tienes pedidos. ¡Empieza en la <a href="{{ route('tienda.index') }}">tienda</a>!
    </div>
  @else
    <div class="card">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="text-center">#</th>
              <th>Fecha / Hora</th>
              <th class="text-center">Estado</th>
              <th class="text-end">Total</th>
              <th>Productos</th>
            </tr>
          </thead>
          <tbody>
          @foreach($entregas as $e)
            @php
              // Badge por estado
              $badge = match($e->estado){
                'realizada' => 'success',
                'cancelada' => 'danger',
                default     => 'warning'
              };

              // Total: si hay factura úsala, si no calcula con precios
              if ($e->relationLoaded('factura') && $e->factura) {
                  $total = (float) $e->factura->total;
              } else {
                  $total = 0.0;
                  if ($e->relationLoaded('productos')) {
                      foreach ($e->productos as $p) {
                          $precio = (float) ($p->precio_unitario ?? 0);
                          $cantidad = (int) ($p->pivot->cantidad ?? 0);
                          $total += $precio * $cantidad;
                      }
                  }
              }
            @endphp
            <tr>
              <td class="text-center fw-bold">{{ $e->id }}</td>
              <td>
                @if($e->fecha_hora)
                  <i class="bi bi-calendar-event me-1"></i>
                  {{ \Carbon\Carbon::parse($e->fecha_hora)->format('d/m/Y H:i') }}
                @else
                  —
                @endif
              </td>
              <td class="text-center">
                <span class="badge bg-{{ $badge }} text-uppercase">{{ $e->estado }}</span>
              </td>
              <td class="text-end">₡{{ number_format($total, 2) }}</td>
              <td>
                @if($e->relationLoaded('productos') && $e->productos->isNotEmpty())
                  <div class="small text-muted">
                    @foreach($e->productos as $p)
                      <span class="me-2">
                        {{ $p->nombre }} <span class="text-secondary">x{{ $p->pivot->cantidad }}</span>
                      </span>
                    @endforeach
                  </div>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      @if($entregas instanceof \Illuminate\Pagination\LengthAwarePaginator && $entregas->hasPages())
        <div class="card-footer bg-white">
          {{ $entregas->links() }}
        </div>
      @endif
    </div>
  @endif
</div>
@endsection
