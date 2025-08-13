@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editar Producto
                    </h2>
                    <p class="text-muted mb-0">{{ $producto->nombre }}</p>
                </div>
            </div>

            <!-- Formulario -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('productos.update', $producto->id) }}" method="POST" id="formProducto">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Información Básica -->
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle"></i> Información Básica
                                </h5>
                            </div>

                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">
                                    Nombre del Producto <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                       name="nombre" value="{{ old('nombre', $producto->nombre) }}"
                                       placeholder="Ej: Tomate cherry, Lechuga romana, etc.">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    Unidad de Medida <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('unidad_medida') is-invalid @enderror" name="unidad_medida">
                                    <option value="">Seleccionar...</option>
                                    <option value="kg" {{ old('unidad_medida', $producto->unidad_medida) == 'kg' ? 'selected' : '' }}>Kilogramos (kg)</option>
                                    <option value="g" {{ old('unidad_medida', $producto->unidad_medida) == 'g' ? 'selected' : '' }}>Gramos (g)</option>
                                    <option value="lb" {{ old('unidad_medida', $producto->unidad_medida) == 'lb' ? 'selected' : '' }}>Libras (lb)</option>
                                    <option value="unidad" {{ old('unidad_medida', $producto->unidad_medida) == 'unidad' ? 'selected' : '' }}>Unidades</option>
                                    <option value="docena" {{ old('unidad_medida', $producto->unidad_medida) == 'docena' ? 'selected' : '' }}>Docenas</option>
                                    <option value="caja" {{ old('unidad_medida', $producto->unidad_medida) == 'caja' ? 'selected' : '' }}>Cajas</option>
                                    <option value="saco" {{ old('unidad_medida', $producto->unidad_medida) == 'saco' ? 'selected' : '' }}>Sacos</option>
                                </select>
                                @error('unidad_medida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Precios -->
                            <div class="col-12 mt-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-dollar-sign"></i> Precio
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    Precio Unitario (₡) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">₡</span>
                                    <input type="number" class="form-control @error('precio_unitario') is-invalid @enderror"
                                           name="precio_unitario" value="{{ old('precio_unitario', $producto->precio_unitario) }}"
                                           placeholder="0.00" step="0.01" min="0">
                                </div>
                                @error('precio_unitario')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="alert alert-info mb-0">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Valor actual del inventario:</strong><br>
                                        ₡{{ number_format($producto->stock_actual * $producto->precio_unitario, 0, ',', '.') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Control de Stock -->
                            <div class="col-12 mt-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-boxes"></i> Control de Inventario
                                </h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    Stock Mínimo <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror"
                                       name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}"
                                       placeholder="0" min="0" id="stockMinimo">
                                @error('stock_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Nivel para alerta de reposición</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    Stock Máximo <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('stock_maximo') is-invalid @enderror"
                                       name="stock_maximo" value="{{ old('stock_maximo', $producto->stock_maximo) }}"
                                       placeholder="0" min="1" id="stockMaximo">
                                @error('stock_maximo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Capacidad máxima de almacenamiento</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">
                                    Stock Actual <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control @error('stock_actual') is-invalid @enderror"
                                       name="stock_actual" value="{{ old('stock_actual', $producto->stock_actual) }}"
                                       placeholder="0" min="0" id="stockActual">
                                @error('stock_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Cantidad disponible actualmente</small>
                            </div>
                        </div>

                        <!-- Estado Actual -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-light">
                                    <h6><i class="fas fa-chart-line"></i> Estado Actual del Producto</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Estado del Stock:</strong>
                                            @if($producto->stock_actual <= $producto->stock_minimo)
                                                <span class="badge bg-danger">Stock Bajo</span>
                                            @elseif($producto->stock_actual >= $producto->stock_maximo * 0.8)
                                                <span class="badge bg-success">Stock Alto</span>
                                            @else
                                                <span class="badge bg-primary">Stock Normal</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Porcentaje de Stock:</strong>
                                            {{ $producto->stock_maximo > 0 ? round(($producto->stock_actual / $producto->stock_maximo) * 100, 1) : 0 }}%
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Creado:</strong>
                                            {{ $producto->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vista Previa del Cambio -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info d-none" id="vistaPrevia">
                                    <h6><i class="fas fa-calculator"></i> Vista Previa de Cambios</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nuevo Valor del Inventario:</strong>
                                            <span id="valorInventario">₡0</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Nuevo Estado del Stock:</strong>
                                            <span id="estadoStock" class="badge"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-12">
                                <hr class="my-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Actualizar Producto
                                    </button>
                                    <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i> Ver Producto
                                    </a>
                                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const precioInput = document.querySelector('input[name="precio_unitario"]');
    const stockActualInput = document.querySelector('input[name="stock_actual"]');
    const stockMinimoInput = document.querySelector('input[name="stock_minimo"]');
    const stockMaximoInput = document.querySelector('input[name="stock_maximo"]');
    const vistaPrevia = document.getElementById('vistaPrevia');
    const valorInventario = document.getElementById('valorInventario');
    const estadoStock = document.getElementById('estadoStock');

    // Valores originales para comparar cambios
    const valoresOriginales = {
        precio: {{ $producto->precio_unitario }},
        stockActual: {{ $producto->stock_actual }},
        stockMinimo: {{ $producto->stock_minimo }},
        stockMaximo: {{ $producto->stock_maximo }}
    };

    function actualizarVistaPrevia() {
        const precio = parseFloat(precioInput.value) || 0;
        const stockActual = parseInt(stockActualInput.value) || 0;
        const stockMinimo = parseInt(stockMinimoInput.value) || 0;
        const stockMaximo = parseInt(stockMaximoInput.value) || 0;

        // Verificar si hay cambios
        const hayCambios = precio !== valoresOriginales.precio ||
                          stockActual !== valoresOriginales.stockActual ||
                          stockMinimo !== valoresOriginales.stockMinimo ||
                          stockMaximo !== valoresOriginales.stockMaximo;

        if (hayCambios && precio > 0 && stockActual >= 0) {
            const valor = precio * stockActual;
            valorInventario.textContent = '₡' + valor.toLocaleString('es-CR');

            // Determinar nuevo estado del stock
            let estado = '';
            let clase = '';

            if (stockMinimo > 0 && stockMaximo > 0) {
                if (stockActual <= stockMinimo) {
                    estado = 'Stock Bajo';
                    clase = 'bg-danger';
                } else if (stockActual >= stockMaximo * 0.8) {
                    estado = 'Stock Alto';
                    clase = 'bg-success';
                } else {
                    estado = 'Stock Normal';
                    clase = 'bg-primary';
                }
            }

            estadoStock.textContent = estado;
            estadoStock.className = `badge ${clase}`;
            vistaPrevia.classList.remove('d-none');
        } else {
            vistaPrevia.classList.add('d-none');
        }
    }

    // Validación en tiempo real
    stockMaximoInput.addEventListener('input', function() {
        const minimo = parseInt(stockMinimoInput.value) || 0;
        const maximo = parseInt(this.value) || 0;

        if (maximo > 0 && minimo > 0 && maximo <= minimo) {
            this.setCustomValidity('El stock máximo debe ser mayor al stock mínimo');
        } else {
            this.setCustomValidity('');
        }
        actualizarVistaPrevia();
    });

    stockActualInput.addEventListener('input', function() {
        const actual = parseInt(this.value) || 0;
        const maximo = parseInt(stockMaximoInput.value) || 0;

        if (maximo > 0 && actual > maximo) {
            this.setCustomValidity('El stock actual no puede ser mayor al stock máximo');
        } else {
            this.setCustomValidity('');
        }
        actualizarVistaPrevia();
    });

    // Eventos para vista previa
    [precioInput, stockActualInput, stockMinimoInput, stockMaximoInput].forEach(input => {
        input.addEventListener('input', actualizarVistaPrevia);
    });

    // Validación del formulario
    document.getElementById('formProducto').addEventListener('submit', function(e) {
        const minimo = parseInt(stockMinimoInput.value) || 0;
        const maximo = parseInt(stockMaximoInput.value) || 0;
        const actual = parseInt(stockActualInput.value) || 0;

        if (maximo <= minimo) {
            e.preventDefault();
            alert('El stock máximo debe ser mayor al stock mínimo');
            stockMaximoInput.focus();
            return false;
        }

        if (actual > maximo) {
            e.preventDefault();
            alert('El stock actual no puede ser mayor al stock máximo');
            stockActualInput.focus();
            return false;
        }
    });
});
</script>
@endpush
