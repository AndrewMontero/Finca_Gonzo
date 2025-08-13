@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-seedling text-success me-2"></i>
                Gestión de Productos
            </h2>
            <p class="text-muted">Administra el inventario de productos hortícolas</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('productos.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
            <a href="{{ route('productos.stock-bajo') }}" class="btn btn-warning ms-2">
                <i class="fas fa-exclamation-triangle"></i> Stock Bajo
                @if($productosStockBajo->count() > 0)
                    <span class="badge bg-danger">{{ $productosStockBajo->count() }}</span>
                @endif
            </a>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tarjetas de Resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-boxes text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold h5 mb-0">{{ $productos->count() }}</div>
                            <small class="text-muted">Total Productos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold h5 mb-0">
                                {{ $productos->where('stock_actual', '>', 'stock_minimo')->count() }}
                            </div>
                            <small class="text-muted">Con Stock Normal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold h5 mb-0">{{ $productosStockBajo->count() }}</div>
                            <small class="text-muted">Stock Bajo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-dollar-sign text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold h5 mb-0">
                                ₡{{ number_format($productos->sum(function($p) { return $p->stock_actual * $p->precio_unitario; }), 0, ',', '.') }}
                            </div>
                            <small class="text-muted">Valor Inventario</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">Lista de Productos</h5>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar productos...">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($productos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="productosTable">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Unidad</th>
                                <th>Precio Unitario</th>
                                <th>Stock Actual</th>
                                <th>Stock Mín/Máx</th>
                                <th>Estado</th>
                                <th>Valor Total</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $producto->nombre }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $producto->unidad_medida }}</span>
                                    </td>
                                    <td>₡{{ number_format($producto->precio_unitario, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($producto->stock_actual) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ number_format($producto->stock_minimo) }} / {{ number_format($producto->stock_maximo) }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($producto->stock_actual <= $producto->stock_minimo)
                                            <span class="badge bg-danger">Stock Bajo</span>
                                        @elseif($producto->stock_actual >= $producto->stock_maximo * 0.8)
                                            <span class="badge bg-success">Stock Alto</span>
                                        @else
                                            <span class="badge bg-primary">Stock Normal</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">
                                        ₡{{ number_format($producto->stock_actual * $producto->precio_unitario, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('productos.show', $producto->id) }}"
                                               class="btn btn-outline-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('productos.edit', $producto->id) }}"
                                               class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-danger"
                                                    onclick="confirmarEliminacion({{ $producto->id }}, '{{ $producto->nombre }}')"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-seedling text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No hay productos registrados</h4>
                    <p class="text-muted">Comienza agregando tu primer producto hortícola.</p>
                    <a href="{{ route('productos.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el producto <strong id="nombreProducto"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta acción no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Buscador en tiempo real
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('#productosTable tbody tr');

    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    document.getElementById('nombreProducto').textContent = nombre;
    document.getElementById('formEliminar').action = `/productos/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

// Auto-ocultar alertas después de 5 segundos
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endpush
