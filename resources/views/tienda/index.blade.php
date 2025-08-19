@extends('layouts.app')
@section('title','Tienda')

@section('content')
<div class="container">
  <h1 class="h4 mb-3">Tienda</h1>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-3">
    @forelse($productos as $p)
      @php $qtyActual = $cart[$p->id]['cantidad'] ?? 0; @endphp
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">{{ $p->nombre }}</h5>
            <p class="mb-1 text-muted">Stock: {{ $p->stock_actual }}</p>
            @isset($p->precio_unitario)
              <p class="mb-3">Precio: ₡{{ number_format($p->precio_unitario,2) }}</p>
            @endisset

            <form method="POST" action="{{ route('tienda.agregar', $p) }}" class="d-flex gap-2">
              @csrf
              <input type="number" name="qty" value="1" min="1" max="{{ $p->stock_actual }}" class="form-control form-control-sm" />
              <button class="btn btn-success btn-sm">
                <i class="bi bi-cart-plus me-1"></i>Agregar
              </button>
            </form>

            @if($qtyActual > 0)
              <span class="badge text-bg-primary mt-2">En carrito: {{ $qtyActual }}</span>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info">No hay productos disponibles.</div>
      </div>
    @endforelse
  </div>

  <div class="mt-4">
    <a href="{{ route('tienda.carrito') }}" class="btn btn-outline-primary">
      <i class="bi bi-cart me-1"></i>Ver carrito
    </a>
  </div>
</div>
@endsection
