@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex-grow-1">
                    <h2 class="mb-0">
                        <i class="fas fa-seedling text-success me-2"></i>
                        {{ $producto->nombre }}
                    </h2>
                    <p class="text-muted mb-0">Detalle completo del producto</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button class="btn btn-outline-danger"
                            onclick="confirmarEliminacion({{ $producto->id }}, '{{ $producto->nombre }}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>

            <!-- Información Principal -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary"></i>
                        Información General
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">Nombre del Producto</label>
                            <div class="h5">{{ $producto->nombre }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">Unidad de Medida</label>
                            <div>
                                <span class="badge bg-light text-dark fs-6">{{ $producto->unidad_medida }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">Precio Unitario</label>
                            <div class="h5 text-success">₡{{ number_format($producto->precio_unitario, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">Fecha de Registro</label>
                            <div>{{ $producto->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @if($producto->updated_at != $producto->created_at)
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted fw-bold">Última Actualización</label>
                            <div>{{ $producto->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información de Stock -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes text-warning"></i>
                        Control de Inventario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted fw-bold">Stock Actual</label>
                            <div class="d-flex align-items-center">
                                <span class="h4 mb-0 me-2">{{ number_format($producto->stock_actual) }}</span>
                                <button class="btn btn-sm btn-outline-primary" onclick="mostrarModalStock()">
                                    <i class="fas fa-edit"></i> Ajustar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted fw-bold">Stock Mínimo</label>
                            <div class="h5 text-danger">{{ number_format($producto->stock_minimo) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted fw-bold">Stock Máximo</label>
                            <div class="h5 text-info">{{ number_format($producto->stock_maximo) }}</div>
                        </div>
                    </div>

                    <!-- Barra de progreso del stock -->
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Nivel de Stock</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar
                                @if($estadisticas['porcentaje_stock'] <= 25) bg-danger
                                @elseif($estadisticas['porcentaje_stock'] <= 50) bg-warning
                                @else bg-success
                                @endif"
                                role="progressbar"
                                style="width: {{ min(100, $estadisticas['porcentaje_stock']) }}%">
                                {{ $estadisticas['porcentaje_stock'] }}%
                            </div>
                        </div>
                    </div>

                    <!-- Estado del Stock -->
                    <div class="alert
                        @if($estadisticas['necesita_reposicion']) alert-danger
                        @elseif($estadisticas['porcentaje_stock'] >= 80) alert-success
                        @else alert-info
                        @endif">
                        <div class="d-flex align-items-center">
                            <i class="fas
                                @if($estadisticas['necesita_reposicion']) fa-exclamation-triangle
                                @elseif($estadisticas['porcentaje_stock'] >= 80) fa-check-circle
                                @else fa-info-circle
                                @endif me-2">
                            </i>
                            <div>
                                @if($estadisticas['necesita_reposicion'])
                                    <strong>¡Stock Bajo!</strong> El producto necesita reposición urgente.
                                @elseif($estadisticas['porcentaje_stock'] >= 80)
                                    <strong>Stock Óptimo</strong> El nivel de inventario es adecuado.
                                @else
                                    <strong>Stock Normal</strong> El nivel de inventario está dentro del rango normal.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Valor del Inventario -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign text-success"></i>
                        Valor del Inventario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border-end">
                                <div class="h3 text-success mb-0">
                                    ₡{{ number_format($estadisticas['valor_inventario'], 0, ',', '.') }}
                                </div>
                                <small class="text-muted">Valor Total Actual</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-end">
                                <div class="h3 text-info mb-0">
                                    ₡{{ number_format($producto->stock_maximo * $producto->precio_unitario, 0, ',', '.') }}
                                </div>
                                <small class="text-muted">Valor Máximo Posible</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h3 text-warning mb-0">
                                ₡{{ number_format($producto->stock_minimo * $producto->precio_unitario, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">Valor Mínimo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar con estadísticas -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary"></i>
                        Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Estado:</span>
                                    @if($estadisticas['necesita_reposicion'])
                                        <span class="badge bg-danger">Necesita Reposición</span>
                                    @elseif($estadisticas['porcentaje_stock'] >= 80)
                                        <span class="badge bg-success">Stock Óptimo</span>
                                    @else
                                        <span class="badge bg-primary">Stock Normal</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Rotación:</span>
                                    <span class="fw-bold">{{ $estadisticas['porcentaje_stock'] }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Unidades faltantes:</span>
                                    <span class="fw-bold text-info">
                                        {{ max(0, $producto->stock_maximo - $producto->stock_actual) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-warning"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="mostrarModalStock()">
                            <i class="fas fa-edit"></i> Ajustar Stock
                        </button>
                        <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-cog"></i> Configurar Producto
                        </a>
                        @if($estadisticas['necesita_reposicion'])
                            <button class="btn btn-outline-success">
                                <i class="fas fa-plus"></i> Programar Reposición
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ajustar stock -->
<div class="modal fade" id="modalAjustarStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar Stock - {{ $producto->nombre }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAjustarStock">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Stock Actual</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="nuevoStock"
                                   value="{{ $producto->stock_actual }}"
                                   min="0" max="{{ $producto->stock_maximo }}">
                            <span class="input-group-text">{{ $producto->unidad_medida }}</span>
                        </div>
                        <small class="text-muted">Máximo permitido: {{ $producto->stock_maximo }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Motivo del Ajuste (Opcional)</label>
                        <textarea class="form-control" id="motivoAjuste" rows="2"
                                  placeholder="Ej: Recepción de mercancía, corrección de inventario, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el producto <strong id="nombreProducto"></strong>?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>¡Atención!</strong> Esta acción no se puede deshacer y se perderán todos los datos del producto.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar Definitivamente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function mostrarModalStock() {
    new bootstrap.Modal(document.getElementById('modalAjustarStock')).show();
}

function confirmarEliminacion(id, nombre) {
    document.getElementById('nombreProducto').textContent = nombre;
    document.getElementById('formEliminar').action = `/productos/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

// Manejar ajuste de stock
document.getElementById('formAjustarStock').addEventListener('submit', function(e) {
    e.preventDefault();

    const nuevoStock = document.getElementById('nuevoStock').value;
    const motivo = document.getElementById('motivoAjuste').value;

    // Aquí harías la petición AJAX para actualizar el stock
    fetch(`/productos/{{ $producto->id }}/actualizar-stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            stock_actual: nuevoStock,
            motivo: motivo
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recargar página para mostrar cambios
        } else {
            alert('Error al actualizar el stock: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el stock');
    });
});
</script>
@endpush
