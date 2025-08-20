{{-- resources/views/tienda/carrito.blade.php --}}
@extends('layouts.app')
@section('title','Carrito')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>
                <a href="{{ route('tienda.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Seguir Comprando
                </a>
            </div>

            {{-- Alertas --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(!empty($items))
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('tienda.carrito.actualizar') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio Unit.</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $pid => $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ $item['nombre'] }}</strong>
                                                </td>
                                                <td>₡{{ number_format($item['precio'], 2) }}</td>
                                                <td>
                                                    <input type="number" 
                                                           name="cantidades[{{ $pid }}]" 
                                                           value="{{ $item['cantidad'] }}" 
                                                           min="0" 
                                                           class="form-control form-control-sm" 
                                                           style="width: 80px;">
                                                </td>
                                                <td>
                                                    <strong>₡{{ number_format($item['precio'] * $item['cantidad'], 2) }}</strong>
                                                </td>
                                                <td>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm"
                                                            onclick="eliminarItem({{ $pid }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th colspan="3">Total:</th>
                                            <th>₡{{ number_format($total, 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-sync"></i> Actualizar Carrito
                                </button>
                                
                                <form method="POST" action="{{ route('tienda.carrito.vaciar') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('¿Vaciar todo el carrito?')">
                                        <i class="fas fa-trash"></i> Vaciar Carrito
                                    </button>
                                </form>
                            </div>
                        </form>

                        {{-- Formulario de Finalizar Compra --}}
                        <hr>
                        <form action="{{ route('tienda.finalizar') }}" method="POST">
                            @csrf
                            <div class="text-center">
                                <h5>Total a Pagar: <span class="text-success">₡{{ number_format($total, 2) }}</span></h5>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle"></i> Finalizar Pedido
                                </button>
                                <p class="small text-muted mt-2">
                                    Tu pedido será enviado al administrador para su procesamiento.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <h4><i class="fas fa-shopping-cart"></i> Tu carrito está vacío</h4>
                    <p>Agrega algunos productos para continuar con tu compra.</p>
                    <a href="{{ route('tienda.index') }}" class="btn btn-primary">
                        <i class="fas fa-store"></i> Ir a la Tienda
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function eliminarItem(productId) {
    if (confirm('¿Eliminar este producto del carrito?')) {
        // Crear form temporal para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("tienda.carrito.actualizar") }}';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Campo cantidad = 0
        const cantidad = document.createElement('input');
        cantidad.type = 'hidden';
        cantidad.name = `cantidades[${productId}]`;
        cantidad.value = '0';
        form.appendChild(cantidad);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection