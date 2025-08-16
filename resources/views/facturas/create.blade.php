@extends('layouts.app')
@section('title','Nueva Factura')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Nueva Factura</h1>
        <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">Ver Facturas</a>
    </div>

    {{-- Mostrar errores si existen --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Mostrar mensaje de error --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('facturas.store') }}" id="facturaForm" class="card shadow-sm border-0">
        @csrf
        <div class="card-body">
            {{-- Cliente --}}
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Cliente (opcional)</label>
                    @if($clientes->count())
                    <select name="cliente_id" class="form-select">
                        <option value="">— Seleccionar —</option>
                        @foreach($clientes as $c)
                        <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="text" class="form-control" placeholder="Nombre del cliente (informativo)">
                    <div class="form-text">No hay catálogo de clientes, este campo es solo informativo.</div>
                    @endif
                </div>
            </div>

            <hr class="my-4">

            {{-- Items de factura (front-only por ahora) --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Ítems</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="agregarFila">
                    <i class="bi bi-plus-lg"></i> Agregar ítem
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="tablaItems">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 220px">Producto</th>
                            <th style="width: 120px">Cantidad</th>
                            <th style="width: 140px">Precio (₡)</th>
                            <th style="width: 140px">Subtotal</th>
                            <th style="width: 60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Fila inicial --}}
                        <tr>
                            <td>
                                @if($productos->count())
                                <select class="form-select productoSelect">
                                    <option value="">— Seleccionar —</option>
                                    @foreach($productos as $p)
                                    <option value="{{ $p->id }}" data-precio="{{ $p->precio ?? 0 }}">{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                                @else
                                <input type="text" class="form-control" placeholder="Nombre del producto">
                                @endif
                            </td>
                            <td><input type="number" min="0" step="1" class="form-control cantidadInput" value="1"></td>
                            <td><input type="number" min="0" step="0.01" class="form-control precioInput" value="0"></td>
                            <td class="subtotalCell">₡0.00</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger quitarFila">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Notas --}}
            <div class="mt-3">
                <label class="form-label">Notas (opcional)</label>
                <textarea name="notas" rows="3" class="form-control" placeholder="Observaciones...">{{ old('notas') }}</textarea>
            </div>

            <hr class="my-4">

            {{-- Total --}}
            <div class="d-flex justify-content-end">
                <div class="text-end">
                    <div class="text-muted">Subtotal</div>
                    <div class="h5 fw-semibold" id="subtotalTexto">₡0.00</div>

                    <div class="text-muted">Total</div>
                    <div class="display-6 fw-semibold" id="totalTexto">₡0.00</div>

                    {{-- inputs hidden que se envían al backend --}}
                    <input type="hidden" name="subtotal" id="subtotalInput" value="0">
                    <input type="hidden" name="total" id="totalInput" value="0">
                </div>
            </div>
        </div>

        <div class="card-footer bg-white d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-outline-secondary">Limpiar</button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle"></i> Guardar Factura
            </button>
        </div>
    </form>
</div>

{{-- JS de la factura (suma, filas, etc.) --}}
<script>
    (function() {
        const tabla = document.getElementById('tablaItems').querySelector('tbody');
        const subtotalTexto = document.getElementById('subtotalTexto');
        const subtotalInput = document.getElementById('subtotalInput');
        const totalTexto = document.getElementById('totalTexto');
        const totalInput = document.getElementById('totalInput');
        const btnAgregar = document.getElementById('agregarFila');

        function formatoCRC(n) {
            return '₡' + new Intl.NumberFormat('es-CR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(n ?? 0);
        }

        function recalcularFila(tr) {
            const qty = parseFloat(tr.querySelector('.cantidadInput')?.value || 0);
            const price = parseFloat(tr.querySelector('.precioInput')?.value || 0);
            const subtotal = Math.max(0, qty * price);
            tr.querySelector('.subtotalCell').textContent = formatoCRC(subtotal);
            return subtotal;
        }

        function recalcularTotal() {
            let subtotalGeneral = 0;
            tabla.querySelectorAll('tr').forEach(tr => {
                subtotalGeneral += recalcularFila(tr);
            });

            // Por ahora subtotal = total (sin impuestos)
            // Puedes agregar cálculo de impuestos aquí si necesitas
            const totalGeneral = subtotalGeneral; // + impuestos si aplica

            subtotalTexto.textContent = formatoCRC(subtotalGeneral);
            subtotalInput.value = subtotalGeneral.toFixed(2);

            totalTexto.textContent = formatoCRC(totalGeneral);
            totalInput.value = totalGeneral.toFixed(2);
        }

        function bindRowEvents(tr) {
            tr.querySelectorAll('.cantidadInput, .precioInput').forEach(inp => {
                inp.addEventListener('input', recalcularTotal);
            });

            const select = tr.querySelector('.productoSelect');
            if (select) {
                select.addEventListener('change', (e) => {
                    const precio = parseFloat(e.target.selectedOptions[0]?.dataset?.precio || 0);
                    const precioInput = tr.querySelector('.precioInput');
                    if (precioInput && !precioInput.value) {
                        precioInput.value = precio.toFixed(2);
                    }
                    recalcularTotal();
                });
            }

            const btnQuitar = tr.querySelector('.quitarFila');
            btnQuitar.addEventListener('click', () => {
                if (tabla.querySelectorAll('tr').length === 1) {
                    // limpiar en vez de quitar la última
                    tr.querySelectorAll('input').forEach(i => i.value = i.classList.contains('cantidadInput') ? 1 : 0);
                    const sel = tr.querySelector('.productoSelect');
                    if (sel) sel.value = '';
                } else {
                    tr.remove();
                }
                recalcularTotal();
            });
        }

        // evento agregar fila
        btnAgregar.addEventListener('click', () => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
      <td>
        @if($productos->count())
          <select class="form-select productoSelect">
            <option value="">— Seleccionar —</option>
            @foreach($productos as $p)
              <option value="{{ $p->id }}" data-precio="{{ $p->precio ?? 0 }}">{{ $p->nombre }}</option>
            @endforeach
          </select>
        @else
          <input type="text" class="form-control" placeholder="Nombre del producto">
        @endif
      </td>
      <td><input type="number" min="0" step="1" class="form-control cantidadInput" value="1"></td>
      <td><input type="number" min="0" step="0.01" class="form-control precioInput" value="0"></td>
      <td class="subtotalCell">₡0.00</td>
      <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger quitarFila">
          <i class="bi bi-x-lg"></i>
        </button>
      </td>
    `;
            tabla.appendChild(tr);
            bindRowEvents(tr);
            recalcularTotal();
        });

        // Validación antes de enviar el formulario
        document.getElementById('facturaForm').addEventListener('submit', function(e) {
            const total = parseFloat(totalInput.value || 0);
            if (total <= 0) {
                e.preventDefault();
                alert('El total debe ser mayor a cero.');
                return false;
            }
        });

        // enlazar eventos a la fila inicial
        bindRowEvents(tabla.querySelector('tr'));
        recalcularTotal();
    })();
</script>
@endsection
