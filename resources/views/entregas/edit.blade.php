@extends('layouts.app')

@section('title', 'Editar entrega #'.$entrega->id)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Editar Entrega #{{ $entrega->id }}
                    </h4>
                </div>
                <div class="card-body">

                    {{-- Mensajes de éxito --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Mensajes de error --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Errores de validación --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle me-2"></i>Corrige los siguientes errores:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('entregas.update', $entrega->id) }}" method="POST" id="formEntrega">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Cliente --}}
                            <div class="col-md-6 mb-3">
                                <label for="cliente_id" class="form-label">
                                    <i class="bi bi-person me-1"></i>Cliente <span class="text-danger">*</span>
                                </label>
                                <select name="cliente_id" id="cliente_id" 
                                        class="form-select @error('cliente_id') is-invalid @enderror" required>
                                    <option value="">Selecciona un cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}"
                                            @selected(old('cliente_id', $entrega->cliente_id) == $cliente->id)>
                                            {{ $cliente->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Repartidor --}}
                            <div class="col-md-6 mb-3">
                                <label for="repartidor_id" class="form-label">
                                    <i class="bi bi-truck me-1"></i>Repartidor <span class="text-danger">*</span>
                                </label>
                                <select name="repartidor_id" id="repartidor_id" 
                                        class="form-select @error('repartidor_id') is-invalid @enderror" required>
                                    <option value="">Selecciona un repartidor</option>
                                    @foreach($repartidores as $repartidor)
                                        <option value="{{ $repartidor->id }}"
                                            @selected(old('repartidor_id', $entrega->repartidor_id) == $repartidor->id)>
                                            {{ $repartidor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('repartidor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Fecha y hora --}}
                            <div class="col-md-6 mb-3">
                                <label for="fecha_hora" class="form-label">
                                    <i class="bi bi-calendar-event me-1"></i>Fecha y Hora <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="fecha_hora" id="fecha_hora"
                                       class="form-control @error('fecha_hora') is-invalid @enderror"
                                       value="{{ old('fecha_hora', \Carbon\Carbon::parse($entrega->fecha_hora)->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('fecha_hora')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Estado --}}
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">
                                    <i class="bi bi-flag me-1"></i>Estado <span class="text-danger">*</span>
                                </label>
                                <select name="estado" id="estado" 
                                        class="form-select @error('estado') is-invalid @enderror" required>
                                    <option value="">Selecciona un estado</option>
                                    <option value="pendiente" 
                                        @selected(old('estado', $entrega->estado) === 'pendiente')>
                                        Pendiente
                                    </option>
                                    <option value="realizada" 
                                        @selected(old('estado', $entrega->estado) === 'realizada')>
                                        Realizada
                                    </option>
                                    <option value="cancelada" 
                                        @selected(old('estado', $entrega->estado) === 'cancelada')>
                                        Cancelada
                                    </option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Si cambias a "Realizada", se descontará el stock y se generará una factura automáticamente.
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Productos --}}
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="bi bi-box-seam me-2"></i>Productos de la Entrega
                            </h5>

                            @php
                                $seleccionados = collect(old('productos', $entrega->productos->pluck('id')->all()))
                                    ->map(fn($v) => (int)$v)->all();
                            @endphp

                            <div class="row g-3">
                                @forelse($productos as $producto)
                                    @php
                                        $isChecked = in_array($producto->id, $seleccionados, true);
                                        $cantidad = old("cantidades.{$producto->id}", $cantidadesActuales[$producto->id] ?? '');
                                    @endphp

                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 producto-card @if($isChecked) border-primary bg-primary bg-opacity-10 @endif">
                                            <div class="card-body">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="prod_{{ $producto->id }}" name="productos[]"
                                                           value="{{ $producto->id }}" @checked($isChecked)
                                                           onchange="toggleProducto({{ $producto->id }})">
                                                    <label class="form-check-label fw-bold" for="prod_{{ $producto->id }}">
                                                        {{ $producto->nombre }}
                                                    </label>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-box me-1"></i>Stock: {{ $producto->stock_actual }}
                                                    </small>
                                                    @if(isset($producto->precio_unitario))
                                                        <small class="text-muted d-block">
                                                            <i class="bi bi-currency-dollar me-1"></i>Precio: ${{ number_format($producto->precio_unitario, 2) }}
                                                        </small>
                                                    @endif
                                                </div>

                                                <div class="cantidad-grupo">
                                                    <label class="form-label small mb-1">Cantidad</label>
                                                    <input type="number" min="1" max="{{ $producto->stock_actual }}"
                                                           class="form-control form-control-sm" 
                                                           name="cantidades[{{ $producto->id }}]"
                                                           id="cantidad_{{ $producto->id }}" 
                                                           value="{{ $cantidad }}"
                                                           placeholder="Cantidad" @if(!$isChecked) disabled @endif>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>No hay productos disponibles para seleccionar.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('entregas.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Volver al Listado
                            </a>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-warning">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Restablecer
                                </button>
                                <button type="submit" class="btn btn-success" id="btnActualizar">
                                    <i class="bi bi-check-lg me-1"></i>Actualizar Entrega
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
// Función para manejar selección de productos
function toggleProducto(productoId) {
    const checkbox = document.getElementById(`prod_${productoId}`);
    const cantidadInput = document.getElementById(`cantidad_${productoId}`);
    const card = checkbox.closest('.producto-card');
    
    if (checkbox.checked) {
        cantidadInput.disabled = false;
        cantidadInput.focus();
        if (!cantidadInput.value || cantidadInput.value <= 0) {
            cantidadInput.value = 1;
        }
        card.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    } else {
        cantidadInput.disabled = true;
        cantidadInput.value = '';
        card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    }
}

// Inicializar estado al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Configurar productos existentes
    const checkboxes = document.querySelectorAll('input[name="productos[]"]');
    checkboxes.forEach(checkbox => {
        toggleProducto(checkbox.value);
    });
});

// Confirmación antes de enviar si se cambia a realizada
document.getElementById('formEntrega').addEventListener('submit', function(e) {
    const estadoSelect = document.getElementById('estado');
    const estadoActual = '{{ $entrega->estado }}';
    const btnActualizar = document.getElementById('btnActualizar');
    
    if (estadoSelect.value === 'realizada' && estadoActual !== 'realizada') {
        if (!confirm('¿Estás seguro de marcar esta entrega como REALIZADA?\n\nEsto realizará las siguientes acciones:\n• Se descontará el stock de los productos\n• Se generará una factura automáticamente\n\nEsta acción no se puede deshacer.')) {
            e.preventDefault();
            return false;
        }
    }
    
    // Mostrar indicador de carga
    btnActualizar.disabled = true;
    btnActualizar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';
});
</script>

<style>
.producto-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.producto-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.producto-card.border-primary {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.cantidad-grupo input:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>
@endsection