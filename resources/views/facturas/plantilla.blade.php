<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Factura</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .titulo { font-size: 18px; font-weight: bold; margin-bottom: 20px; }
        .info { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2 class="titulo">Factura #{{ $entrega->id }}</h2>

    <div class="info">
        <p><strong>Cliente:</strong> {{ $entrega->cliente->nombre }}</p>
        <p><strong>Repartidor:</strong> {{ $entrega->repartidor->name }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($entrega->fecha_hora)->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entrega->productos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->pivot->cantidad }}</td>
                    <td>{{ number_format($producto->precio_unitario, 2) }}</td>
                    <td>{{ number_format($producto->pivot->cantidad * $producto->precio_unitario, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="text-align:right; margin-top:20px;">
        Total: ₡{{ number_format($entrega->productos->sum(fn($p) => $p->pivot->cantidad * $p->precio_unitario), 2) }}
    </h3>
</body>
</html>
