<p>¡Hola {{ optional(optional($factura->entrega)->cliente)->nombre ?? 'cliente' }}!</p>
<p>Adjuntamos su factura #{{ $factura->id }}.</p>
<p>Gracias por su compra.</p>
