@extends('layouts.app')

@section('title', 'Productos con Stock Bajo')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Productos con Stock Bajo
                    </h2>
                    <p class="text-muted mb-0">Productos que necesitan reposición urgente</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('productos.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
                <button class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download"></i> Exportar
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportarPDF()">
                        <i class="fas fa-file-pdf text-danger"></i> Exportar PDF
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportarExcel()">
                        <i class="fas fa-file-excel text-success"></i> Exportar Excel
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alertas de resumen -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="alert alert-warning border-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fs-3 me-3"></i>
                    <div>
                        <h5 class="mb-0">{{ $productos->count() }}</h5>
                        <small>Productos con stock bajo</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-danger border-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-ban fs-3 me-3"></i>
                    <div>
                        <h5 class="mb-0">{{ $productos->where('stock_actual', 0)->count() }}</h5>
                        <small>Productos sin stock</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-info border-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-dollar-sign fs-3 me-3"></i>
                    <div>
                        <h5 class="mb-0">
                            ₡{{ number_format($productos->sum(function($p) {
                                return ($p->stock_minimo - $p->stock_actual) * $p->precio_unitario;
                            }), 0, ',', '.') }}
                        </h5>
                        <small>Valor estimado de reposición</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de productos -->
    @if($productos->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Lista de Productos Críticos</h5>
                        <small class="text-muted">
                            Ordenados por nivel de stock (menor a mayor)
                        </small>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-primary btn-sm" onclick="marcarTodos()">
                            <i class="fas fa-check-square"></i> Seleccionar Todos
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Producto</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Faltante</th>
                                <th>Valor Reposición</th>
                                <th>Criticidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                <tr class="producto-row" data-producto-id="{{ $producto->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input producto-checkbox"
                                               value="{{ $producto->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold">{{ $producto->nombre }}</div>
                                                <small class="text-muted">
                                                    ₡{{ number_format($producto->precio_unitario, 0, ',', '.') }} / {{ $producto->unidad_medida }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($producto->stock_actual == 0) bg-danger
                                            @else bg-warning
                                            @endif fs-6">
                                            {{ number_format($producto->stock_actual) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ number_format($producto->stock_minimo) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-danger">
                                            {{ number_format($producto->stock_minimo - $producto->stock_actual) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            ₡{{ number_format(($producto->stock_minimo - $producto->stock_actual) * $producto->precio_unitario, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $porcentaje = $producto->stock_maximo > 0 ?
                                                ($producto->stock_actual / $producto->stock_maximo) * 100 : 0;
                                        @endphp

                                        @if($producto->stock_actual == 0)
                                            <span class="badge bg-danger">Sin Stock</span>
                                        @elseif($porcentaje < 10)
                                            <span class="badge bg-danger">Crítico</span>
                                        @elseif($porcentaje < 25)
                                            <span class="badge bg-warning">Muy Bajo</span>
                                        @else
                                            <span class="badge bg-info">Bajo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-success"
                                                    onclick="ajustarStock({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->stock_actual }}, {{ $producto->stock_maximo }})"
                                                    title="Ajustar Stock">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <a href="{{ route('productos.show', $producto->id) }}"
                                               class="btn btn-outline-info" title="Ver Detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('productos.edit', $producto->id) }}"
                                               class="btn btn-outline-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="row align-items-center">
                    <div class="col">
                        <small class="text-muted">
                            Total: {{ $productos->count() }} productos necesitan reposición
                        </small>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success" onclick="accionMasiva('reponer')" id="btnAccionMasiva" disabled>
                            <i class="fas fa-shopping-cart"></i> Generar Orden de Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="text-success mt-3">¡Excelente!</h4>
                <p class="text-muted mb-4">
                    No hay productos con stock bajo en este momento.<br>
                    Todos los productos tienen niveles de inventario adecuados.
                </p>
                <div class="btn-group">
                    <a href="{{ route('productos.index') }}" class="btn btn-primary">
                        <i class="fas fa-boxes"></i> Ver Todos los Productos
                    </a>
                    <a href="{{ route('productos.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Agregar Nuevo Producto
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal para Ajustar Stock -->
<div class="modal fade" id="modalAjustarStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAjustarStock">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong id="nombreProductoModal"></strong>
                        <br>
                        <small>Stock actual: <span id="stockActualModal"></span></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nuevo Stock</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="ajustarCantidad('minimo')">
                                Stock Mín.
                            </button>
                            <input type="number" class="form-control" id="nuevoStockModal" min="0">
                            <button class="btn btn-outline-secondary" type="button" onclick="ajustarCantidad('maximo')">
                                Stock Máx.
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Motivo</label>
                        <select class="form-select" id="motivoModal">
                            <option value="reposicion">Reposición de inventario</option>
                            <option value="compra">Nueva compra</option>
                            <option value="correccion">Corrección de inventario</option>
                            <option value="devolucion">Devolución de mercancía</option>
                            <option value="otro">Otro motivo</option>
                        </select>
                    </div>

                    <div class="mb-3" id="otroMotivoDiv" style="display: none;">
                        <label class="form-label fw-bold">Especificar motivo</label>
                        <textarea class="form-control" id="otroMotivoTexto" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Orden de Compra -->
<div class="modal fade" id="modalOrdenCompra" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generar Orden de Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Productos seleccionados para reposición</strong>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm" id="tablaOrdenCompra">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Stock Actual</th>
                                <th>Cantidad Sugerida</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoOrdenCompra">
                            <!-- Se llena dinámicamente -->
                        </tbody>
                        <tfoot>
                            <tr class="table-warning">
                                <th colspan="4">Total Estimado:</th>
                                <th id="totalOrdenCompra">₡0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">Observaciones</label>
                    <textarea class="form-control" id="observacionesOrden" rows="3"
                              placeholder="Notas adicionales para la orden de compra..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="exportarOrdenCompra()">
                    <i class="fas fa-download"></i> Descargar Orden
                </button>
                <button type="button" class="btn btn-success" onclick="procesarOrdenCompra()">
                    <i class="fas fa-check"></i> Procesar Orden
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let productosSeleccionados = [];
let modalAjustarStock;
let modalOrdenCompra;

document.addEventListener('DOMContentLoaded', function() {
    modalAjustarStock = new bootstrap.Modal(document.getElementById('modalAjustarStock'));
    modalOrdenCompra = new bootstrap.Modal(document.getElementById('modalOrdenCompra'));

    // Manejar cambio en el select de motivo
    document.getElementById('motivoModal').addEventListener('change', function() {
        const otroMotivoDiv = document.getElementById('otroMotivoDiv');
        otroMotivoDiv.style.display = this.value === 'otro' ? 'block' : 'none';
    });

    // Manejar select all
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.producto-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        actualizarProductosSeleccionados();
    });

    // Manejar checkboxes individuales
    document.querySelectorAll('.producto-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarProductosSeleccionados);
    });
});

function marcarTodos() {
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = !selectAll.checked;
    selectAll.dispatchEvent(new Event('change'));
}

function actualizarProductosSeleccionados() {
    const checkboxes = document.querySelectorAll('.producto-checkbox:checked');
    productosSeleccionados = Array.from(checkboxes).map(cb => cb.value);

    const btnAccionMasiva = document.getElementById('btnAccionMasiva');
    btnAccionMasiva.disabled = productosSeleccionados.length === 0;
    btnAccionMasiva.textContent = productosSeleccionados.length > 0
        ? `Generar Orden (${productosSeleccionados.length} productos)`
        : 'Generar Orden de Compra';
}

function ajustarStock(id, nombre, stockActual, stockMaximo) {
    document.getElementById('nombreProductoModal').textContent = nombre;
    document.getElementById('stockActualModal').textContent = stockActual;
    document.getElementById('nuevoStockModal').value = stockActual;
    document.getElementById('nuevoStockModal').setAttribute('max', stockMaximo);
    document.getElementById('nuevoStockModal').setAttribute('data-producto-id', id);
    document.getElementById('nuevoStockModal').setAttribute('data-stock-minimo',
        document.querySelector(`[data-producto-id="${id}"]`).closest('tr')
            .querySelector('td:nth-child(4)').textContent.trim());
    document.getElementById('nuevoStockModal').setAttribute('data-stock-maximo', stockMaximo);

    modalAjustarStock.show();
}

function ajustarCantidad(tipo) {
    const input = document.getElementById('nuevoStockModal');
    const stockMinimo = parseInt(input.getAttribute('data-stock-minimo'));
    const stockMaximo = parseInt(input.getAttribute('data-stock-maximo'));

    if (tipo === 'minimo') {
        input.value = stockMinimo;
    } else if (tipo === 'maximo') {
        input.value = stockMaximo;
    }
}

function accionMasiva(accion) {
    if (accion === 'reponer' && productosSeleccionados.length > 0) {
        generarOrdenCompra();
    }
}

function generarOrdenCompra() {
    const cuerpoTabla = document.getElementById('cuerpoOrdenCompra');
    const totalOrden = document.getElementById('totalOrdenCompra');

    cuerpoTabla.innerHTML = '';
    let totalGeneral = 0;

    productosSeleccionados.forEach(productoId => {
        const fila = document.querySelector(`[data-producto-id="${productoId}"]`).closest('tr');
        const nombre = fila.querySelector('td:nth-child(2) .fw-bold').textContent;
        const stockActual = parseInt(fila.querySelector('td:nth-child(3) .badge').textContent);
        const stockMinimo = parseInt(fila.querySelector('td:nth-child(4)').textContent.trim());
        const precioText = fila.querySelector('td:nth-child(2) small').textContent;
        const precio = parseFloat(precioText.match(/[\d,]+/)[0].replace(',', ''));

        const cantidadSugerida = stockMinimo - stockActual;
        const subtotal = cantidadSugerida * precio;
        totalGeneral += subtotal;

        const filaHTML = `
            <tr>
                <td>${nombre}</td>
                <td>${stockActual}</td>
                <td>
                    <input type="number" class="form-control form-control-sm"
                           value="${cantidadSugerida}" min="1" style="width: 80px;"
                           onchange="recalcularTotal()">
                </td>
                <td>₡${precio.toLocaleString('es-CR')}</td>
                <td class="subtotal">₡${subtotal.toLocaleString('es-CR')}</td>
            </tr>
        `;

        cuerpoTabla.insertAdjacentHTML('beforeend', filaHTML);
    });

    totalOrden.textContent = `₡${totalGeneral.toLocaleString('es-CR')}`;
    modalOrdenCompra.show();
}

function recalcularTotal() {
    let total = 0;
    const filas = document.querySelectorAll('#cuerpoOrdenCompra tr');

    filas.forEach(fila => {
        const cantidad = parseInt(fila.querySelector('input').value) || 0;
        const precioText = fila.querySelector('td:nth-child(4)').textContent;
        const precio = parseFloat(precioText.replace('₡', '').replace(',', ''));
        const subtotal = cantidad * precio;

        fila.querySelector('.subtotal').textContent = `₡${subtotal.toLocaleString('es-CR')}`;
        total += subtotal;
    });

    document.getElementById('totalOrdenCompra').textContent = `₡${total.toLocaleString('es-CR')}`;
}

// Manejar formulario de ajustar stock
document.getElementById('formAjustarStock').addEventListener('submit', function(e) {
    e.preventDefault();

    const productoId = document.getElementById('nuevoStockModal').getAttribute('data-producto-id');
    const nuevoStock = document.getElementById('nuevoStockModal').value;
    const motivo = document.getElementById('motivoModal').value;
    const otroMotivo = document.getElementById('otroMotivoTexto').value;

    const motivoFinal = motivo === 'otro' ? otroMotivo : motivo;

    // Enviar petición AJAX
    fetch(`/productos/${productoId}/actualizar-stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            stock_actual: nuevoStock,
            motivo: motivoFinal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            modalAjustarStock.hide();
            location.reload(); // Recargar para mostrar cambios
        } else {
            alert('Error al actualizar el stock: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el stock');
    });
});

function exportarPDF() {
    window.print(); // Simple implementación, se puede mejorar
}

function exportarExcel() {
    // Implementar exportación a Excel
    alert('Funcionalidad de exportación a Excel en desarrollo');
}

function exportarOrdenCompra() {
    // Implementar exportación de orden de compra
    alert('Descargando orden de compra...');
}

function procesarOrdenCompra() {
    // Implementar procesamiento de orden de compra
    alert('Procesando orden de compra...');
    modalOrdenCompra.hide();
}
</script>
@endpush
