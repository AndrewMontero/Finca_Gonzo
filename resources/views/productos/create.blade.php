@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Nuevo Producto</h1>
                    <p class="text-muted">Registra un nuevo producto en el inventario</p>
                </div>
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Productos
                </a>
            </div>

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form action="{{ route('productos.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <!-- Nombre del Producto -->
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('nombre') is-invalid @enderror"
                                               id="nombre"
                                               name="nombre"
                                               value="{{ old('nombre') }}"
                                               placeholder="Ej: Tomate Cherry"
                                               required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Unidad de Medida -->
                                    <div class="col-md-6 mb-3">
                                        <label for="unidad_medida" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                                        <select class="form-select @error('unidad_medida') is-invalid @enderror"
                                                id="unidad_medida"
                                                name="unidad_medida"
                                                required>
                                            <option value="">Seleccionar unidad</option>
                                            <option value="kg" {{ old('unidad_medida') == 'kg' ? 'selected' : '' }}>Kilogramos (kg)</option>
                                            <option value="lb" {{ old('unidad_medida') == 'lb' ? 'selected' : '' }}>Libras (lb)</option>
                                            <option value="unidad" {{ old('unidad_medida') == 'unidad' ? 'selected' : '' }}>Unidades</option>
                                            <option value="caja" {{ old('unidad_medida') == 'caja' ? 'selected' : '' }}>Cajas</option>
                                            <option value="saco" {{ old('unidad_medida') == 'saco' ? 'selected' : '' }}>Sacos</option>
                                            <option value="litro" {{ old('unidad_medida') == 'litro' ? 'selected' : '' }}>Litros</option>
                                        </select>
                                        @error('unidad_medida')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Precio Unitario -->
                                    <div class="col-md-6 mb-3">
                                        <label for="precio_unitario" class="form-label">Precio Unitario (₡) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₡</span>
                                            <input type="number"
                                                   class="form-control @error('precio_unitario') is-invalid @enderror"
                                                   id="precio_unitario"
                                                   name="precio_unitario"
                                                   value="{{ old('precio_unitario') }}"
                                                   step="0.01"
                                                   min="0"
                                                   max="999999.99"
                                                   placeholder="0.00"
                                                   required>
                                            @error('precio_unitario')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Stock Actual -->
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_actual" class="form-label">Stock Actual <span class="text-danger">*</span></label>
                                        <input type="number"
                                               class="form-control @error('stock_actual') is-invalid @enderror"
                                               id="stock_actual"
                                               name="stock_actual"
                                               value="{{ old('stock_actual') }}"
                                               min="0"
                                               placeholder="0"
                                               required>
                                        @error('stock_actual')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Stock Mínimo -->
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_minimo" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                                        <input type="number"
                                               class="form-control @error('stock_minimo') is-invalid @enderror"
                                               id="stock_minimo"
                                               name="stock_minimo"
                                               value="{{ old('stock_minimo') }}"
                                               min="0"
                                               placeholder="5"
                                               required>
                                        @error('stock_minimo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Cantidad mínima antes de alertar por stock bajo</div>
                                    </div>

                                    <!-- Stock Máximo -->
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_maximo" class="form-label">Stock Máximo <span class="text-danger">*</span></label>
                                        <input type="number"
                                               class="form-control @error('stock_maximo') is-invalid @enderror"
                                               id="stock_maximo"
                                               name="stock_maximo"
                                               value="{{ old('stock_maximo') }}"
                                               min="1"
                                               placeholder="100"
                                               required>
                                        @error('stock_maximo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Capacidad máxima de almacenamiento</div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('productos.index') }}" class="btn btn-light">
                                                <i class="fas fa-times"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save"></i> Guardar Producto
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para validaciones del cliente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockMinimo = document.getElementById('stock_minimo');
    const stockMaximo = document.getElementById('stock_maximo');
    const stockActual = document.getElementById('stock_actual');

    function validarStocks() {
        const minimo = parseInt(stockMinimo.value) || 0;
        const maximo = parseInt(stockMaximo.value) || 0;
        const actual = parseInt(stockActual.value) || 0;

        // Validar que máximo > mínimo
        if (maximo > 0 && minimo >= maximo) {
            stockMaximo.setCustomValidity('El stock máximo debe ser mayor al mínimo');
        } else {
            stockMaximo.setCustomValidity('');
        }

        // Validar que actual <= máximo
        if (maximo > 0 && actual > maximo) {
            stockActual.setCustomValidity('El stock actual no puede ser mayor al máximo');
        } else {
            stockActual.setCustomValidity('');
        }
    }

    stockMinimo.addEventListener('input', validarStocks);
    stockMaximo.addEventListener('input', validarStocks);
    stockActual.addEventListener('input', validarStocks);
});
</script>
@endsection
