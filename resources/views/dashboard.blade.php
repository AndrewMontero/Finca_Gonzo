@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="container py-4">

  {{-- Encabezado --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
      <h1 class="display-6 fw-bold mb-0">Panel de Indicadores</h1>
      <div class="text-muted">Resumen operativo de Finca Gonzo</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('reportes.index') ?? '#' }}" class="btn btn-outline-primary">
        <i class="bi bi-graph-up"></i> Reportes
      </a>
      <a href="{{ route('facturas.create') ?? (route('facturas.index') ?? '#') }}" class="btn btn-primary">
        <i class="bi bi-receipt"></i> Nueva Factura
      </a>
    </div>
  </div>

  {{-- KPIs --}}
  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-success-subtle text-success p-3">
            <i class="bi bi-cash-stack fs-3"></i>
          </div>
          <div>
            <div class="text-muted">Total Ventas</div>
            <div class="fs-2 fw-semibold">₡{{ number_format($totalVentas, 2) }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-primary-subtle text-primary p-3">
            <i class="bi bi-check2-circle fs-3"></i>
          </div>
          <div>
            <div class="text-muted">Entregas Completadas</div>
            <div class="fs-2 fw-semibold">{{ $entregasCompletadas }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-warning-subtle text-warning p-3">
            <i class="bi bi-truck fs-3"></i>
          </div>
          <div>
            <div class="text-muted">Entregas Pendientes</div>
            <div class="fs-2 fw-semibold">{{ $entregasPendientes }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Gráfico + Listas --}}
  <div class="row g-3">
    <div class="col-12 col-xl-8">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold">Ventas por Mes</h5>
          <small class="text-muted">Últimos 12 meses</small>
        </div>
        <div class="card-body">
          {{-- contenedor con altura para que siempre se vea el esqueleto --}}
          <div style="height: 380px">
            <canvas id="ventasChart" width="400" height="380"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-4 d-flex flex-column gap-3">
      {{-- Bajo stock --}}
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
          <h6 class="mb-0 fw-semibold">Productos con Bajo Stock</h6>
        </div>
        <div class="card-body p-0">
          @if($productosBajoStock->isEmpty())
            <div class="p-3 text-muted">No hay productos con bajo stock.</div>
          @else
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Producto</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Mín.</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($productosBajoStock as $p)
                    <tr>
                      <td class="text-truncate" style="max-width: 200px">{{ $p->nombre }}</td>
                      <td class="text-center">{{ $p->stock_actual }}</td>
                      <td class="text-center">{{ $p->stock_minimo }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- Top vendidos --}}
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0">
          <h6 class="mb-0 fw-semibold">Top 5 Productos Más Vendidos</h6>
        </div>
        <div class="card-body">
          @if($productosMasVendidos->isEmpty())
            <div class="text-muted">Aún no hay ventas registradas.</div>
          @else
            <ol class="mb-0 ps-3">
              @foreach($productosMasVendidos as $p)
                <li class="mb-1">
                  <span class="fw-semibold">{{ $p->nombre }}</span>
                  <span class="text-muted">— {{ $p->entregas_count }} entregas</span>
                </li>
              @endforeach
            </ol>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ====== Scripts del gráfico ====== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
  // Datos del backend
  const labels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
  const serie  = @json($serie ?? []);
  const data   = Array.from({ length: 12 }, (_, i) => Number(serie[i] ?? 0));

  // Plugin para mostrar "Sin datos" cuando todo es 0 (pero dejando ejes y grilla)
  const noDataPlugin = {
    id: 'noData',
    afterDraw(chart, args, opts) {
      const allZero = chart.data.datasets.every(ds => ds.data.every(v => Number(v) === 0));
      if (!allZero) return;
      const { ctx, chartArea } = chart;
      if (!chartArea) return;
      ctx.save();
      ctx.fillStyle = 'rgba(0,0,0,.45)';
      ctx.font = '500 16px system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial';
      ctx.textAlign = 'center';
      ctx.fillText('Sin datos para mostrar', (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
      ctx.restore();
    }
  };

  const ctx = document.getElementById('ventasChart');
  if (!ctx) return;

  new Chart(ctx, {
    type: 'bar', // cambia a 'line' si prefieres línea
    data: {
      labels,
      datasets: [{
        label: 'Ventas Mensuales (₡)',
        data,
        backgroundColor: 'rgba(13,110,253,.45)',
        borderColor: 'rgba(13,110,253,1)',
        borderWidth: 1,
        hoverBackgroundColor: 'rgba(13,110,253,.65)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true }
      },
      scales: {
        x: { grid: { display: true, color: 'rgba(0,0,0,.05)' } },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,.05)' },
          ticks: {
            callback: v => '₡' + new Intl.NumberFormat('es-CR').format(v)
          }
        }
      }
    },
    plugins: [noDataPlugin]
  });
})();
</script>
@endsection
