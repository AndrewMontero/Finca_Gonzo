@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Gestión de Productos</h1>
                    <p class="text-muted">Administra el inventario de productos</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('productos.stock-bajo') }}" class="btn btn-warning">
                        <i class="fas fa-exclamation-triangle"></i> Stock Bajo ({{ $stockBajo }})
                    </a>
                    <a href="{{ route('productos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </a>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-boxes fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-muted">Total Productos</div>
                                    <div class="h5 mb-0">{{ number_format($totalProductos) }}</div>
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
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-muted">Stock Bajo</div>
                                    <div class="h5 mb-0">{{ number_format($stockBajo) }}</div>
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
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-muted">Stock Normal</div>
                                    <div class="h5 mb-0">{{ number_format($stockNormal) }}</div>
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
                                    <i class="fas fa-dollar-sign fa-2x text-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-muted">Valor Inventario</div>
                                    <div class="h5 mb-0">₡{{ number_format($valorInventario, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Tabla de productos -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Lista de Productos</h5>
                </div>
                <div class="card-body">
                    @if($productos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Unidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Stock Actual</th>
                                        <th>Stock Mínimo</th>
                                        <th>Stock Máximo</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productos as $producto)
                                        <tr>
                                            <td>
                                                <strong>{{ $producto->nombre }}</strong>
                                            </td>
                                            <td>{{ ucfirst($producto->unidad_medida) }}</td>
                                            <td>₡{{ number_format($producto->precio_unitario, 2) }}</td>
                                            <td>
                                                <span id="stock-{{ $producto->id }}">{{ $producto->stock_actual }}</span>
                                            </td>
                                            <td>{{ $producto->stock_minimo }}</td>
                                            <td>{{ $producto->stock_maximo }}</td>
                                            <td>
                                                @if($producto->stock_actual <= $producto->stock_minimo)
                                                    <span class="badge bg-danger">Stock Bajo</span>
                                                @elseif($producto->stock_actual >= $producto->stock_maximo * 0.8)
                                                    <span class="badge bg-success">Stock Alto</span>
                                                @else
                                                    <span class="badge bg-primary">Stock Normal</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex gap-2 justify-content-end">
                                                    <!-- Ver Detalles - Azul -->
                                                    <a href="{{ route('productos.show', $producto) }}"
                                                       class="btn btn-sm btn-info text-white d-flex align-items-center gap-1"
                                                       title="Ver información completa del producto">
                                                        <i class="fas fa-eye"></i>
                                                        <span class="d-none d-md-inline">Ver Detalles</span>
                                                    </a>

                                                    <!-- Editar - Verde -->
                                                    <a href="{{ route('productos.edit', $producto) }}"
                                                       class="btn btn-sm btn-success text-white d-flex align-items-center gap-1"
                                                       title="Modificar información del producto">
                                                        <i class="fas fa-edit"></i>
                                                        <span class="d-none d-md-inline">Editar</span>
                                                    </a>

                                                    <!-- Eliminar - Rojo -->
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger text-white d-flex align-items-center gap-1"
                                                            title="Eliminar producto permanentemente"
                                                            onclick="if(confirm('⚠️ ¿Está seguro de eliminar este producto?\n\nEsta acción no se puede deshacer.')) { document.getElementById('delete-form-{{ $producto->id }}').submit(); }">
                                                        <i class="fas fa-trash"></i>
                                                        <span class="d-none d-md-inline">Eliminar</span>
                                                    </button>
                                                    <form id="delete-form-{{ $producto->id }}"
                                                          action="{{ route('productos.destroy', $producto) }}"
                                                          method="POST"
                                                          style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $productos->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay productos registrados</h5>
                            <p class="text-muted">Comience agregando su primer producto al inventario</p>
                            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Agregar Primer Producto
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar stock - YA NO SE USA -->

<script>
// Scripts relacionados con stock y duplicar productos eliminados
</script>
@endsection
