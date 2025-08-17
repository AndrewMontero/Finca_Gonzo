<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Factura #{{ $factura->id }}</title>
</head>
<body style="font-family: Arial, sans-serif; color:#222;">
  <h2 style="margin:0 0 12px;">Hola {{ $cliente->nombre ?? 'cliente' }},</h2>
  <p>Adjuntamos tu <strong>Factura #{{ $factura->id }}</strong> de Finca Gonzo.</p>

  <p style="margin-top:16px;">
    <strong>Total:</strong> ₡{{ number_format($factura->total, 2) }}<br>
    <strong>Fecha:</strong> {{ $factura->created_at }}
  </p>

  <p>¡Gracias por tu compra!</p>
  <p>Finca Gonzo</p>

</body>
</html>

