@extends('layouts.app')
@section('title','Carrito')

@section('content')
<div class="container">
  <h1 class="h4 mb-3">Carrito</h1>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      {{ session('error') }}
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($items->isEmpty())
    <div class="alert alert-info">Tu carrito está vacío.</div>
    <a href="{{ route('tienda.index') }}" class="btn btn-primary">
      <i class="bi bi-shop me-1"></i>Ir a la Tienda
    </a>
  @else
    <form method="POST" action="{{ route('tienda.carrito.actualizar') }}">
      @csrf
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th>Producto</th>
              <th class="text-end">Precio</th>
              <th class="text-end">Cantidad</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $pid => $it)
              <tr>
                <td>{{ $it['nombre'] }}</td>
                <td class="text-end">₡{{ number_format($it['precio'],2) }}</td>
                <td class="text-end" style="max-width:120px">
                  <input type="number" min="0" name="cantidades[{{ $pid }}]" value="{{ $it['cantidad'] }}" class="form-control form-control-sm text-end" />
                  <small class="text-muted">0 = eliminar</small>
                </td>
                <td class="text-end">₡{{ number_format($it['precio'] * $it['cantidad'], 2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">Total</th>
              <th class="text-end">₡{{ number_format($total,2) }}</th>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="d-flex justify-content-between">
        <a href="{{ route('tienda.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-shop me-1"></i>Seguir comprando
        </a>

        <div class="d-flex gap-2">
          <button class="btn btn-outline-warning" type="submit">
            <i class="bi bi-arrow-repeat me-1"></i>Actualizar
          </button>

          <form method="POST" action="{{ route('tienda.finalizar') }}">
            @csrf
            <button class="btn btn-success">
              <i class="bi bi-check2-circle me-1"></i>Finalizar pedido
            </button>
          </form>
        </div>
      </div>
    </form>
  @endif
</div>
@endsection
