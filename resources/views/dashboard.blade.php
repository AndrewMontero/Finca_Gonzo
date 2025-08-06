@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Panel de Indicadores</h2>

    <div class="row">
        <div class="col-md-3">
            <div class="card p-3">
                <h4>Total Ventas</h4>
                <p>₡{{ number_format($totalVentas, 2) }}</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h4>Entregas Completadas</h4>
                <p>{{ $entregasCompletadas }}</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h4>Entregas Pendientes</h4>
                <p>{{ $entregasPendientes }}</p>
            </div>
        </div>
    </div>

    <hr>

    <h4>Productos con Bajo Stock</h4>
    <ul>
        @forelse($productosBajoStock as $producto)
            <li>{{ $producto->nombre }} - Stock actual: {{ $producto->stock_actual }}</li>
        @empty
            <li>No hay productos con bajo stock.</li>
        @endforelse
    </ul>

    <hr>

    <h4>Top 5 Productos Más Vendidos</h4>
    <ul>
        @foreach($productosMasVendidos as $producto)
            <li>{{ $producto->nombre }} - {{ $producto->entregas_count }} entregas</li>
        @endforeach
    </ul>

    <hr>

    <h4>Ventas por Mes</h4>
    <canvas id="ventasChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ventasChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($ventasPorMes)) !!},
            datasets: [{
                label: 'Ventas Mensuales',
                data: {!! json_encode(array_values($ventasPorMes)) !!},
                borderWidth: 1,
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
