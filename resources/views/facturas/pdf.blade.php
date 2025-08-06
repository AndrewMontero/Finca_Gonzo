<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $factura->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Factura #{{ $factura->id }}</h2>
    <p><strong>Cliente:</strong> {{ $factura->entrega->cliente->nombre }}</p>
    <p><strong>Repartidor:</strong> {{ $factura->entrega->repartidor->name }}</p>
    <p><strong>Fecha:</strong> {{ $factura->created_at->format('d/m/Y H:i') }}</p>

    <h3>Productos</h3>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->entrega->productos as $producto)
            <tr>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->pivot->cantidad }}</td>
                <td>₡{{ number_format($producto->precio_unitario, 2) }}</td>
                <td>₡{{ number_format($producto->precio_unitario * $producto->pivot->cantidad, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total: ₡{{ number_format($factura->total, 2) }}</h3>
</body>
</html>
