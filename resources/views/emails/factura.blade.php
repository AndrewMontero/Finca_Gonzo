@php($cli = optional(optional($factura->entrega)->cliente))
<p>Hola {{ $cli->nombre ?? 'cliente' }},</p>
<p>Adjunto encontrarás tu factura <strong>#{{ $factura->id }}</strong>.</p>

<ul>
  <li><strong>Total:</strong> ₡{{ number_format($factura->total, 2) }}</li>
  <li><strong>Fecha:</strong> {{ $factura->created_at }}</li>
</ul>

<p>¡Gracias por su compra!</p>
<p>Finca Gonzo</p>
