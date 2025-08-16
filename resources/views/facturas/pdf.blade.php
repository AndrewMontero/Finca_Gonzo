@php
    $entrega = $factura->entrega;
    $cliente = optional($entrega)->cliente;
    $productos = optional($entrega)->productos ?? collect();
    $fmt = fn($n) => '₡' . number_format($n, 2);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Factura #{{ $factura->id }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; color:#222; }
    .wrap { width: 92%; margin: 0 auto; }
    h1 { font-size: 22px; margin: 0 0 8px; }
    .muted { color:#666; font-size:12px; }
    .card { border:1px solid #e5e7eb; border-radius:10px; padding:14px; margin:12px 0; }
    table { width:100%; border-collapse: collapse; }
    th, td { border: 1px solid #e5e7eb; padding: 8px 10px; font-size: 13px; }
    th { background:#f8fafc; text-align:left; }
    .totals td { border:0; padding:6px 0; }
</style>
</head>
<body>
<div class="wrap">
    <h1>Factura #{{ $factura->id }}</h1>
    <div class="muted">Fecha: {{ $factura->created_at }}</div>

    <div class="card">
        <strong>Cliente</strong><br>
        {{ $cliente->nombre ?? '—' }}<br>
        {{ $cliente->correo ?? '—' }}<br>
        Tel: {{ $cliente->telefono ?? '—' }}<br>
        {{ $cliente->ubicacion ?? '' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:60%">Descripción</th>
                <th style="width:20%">Cantidad</th>
                <th style="width:20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @if($productos->count())
                @foreach($productos as $p)
                    @php
                        $cant = (int) ($p->pivot->cantidad ?? 1);
                        $sub  = $p->precio_unitario * $cant;
                    @endphp
                    <tr>
                        <td>{{ $p->nombre }}</td>
                        <td>{{ $cant }}</td>
                        <td>{{ $fmt($sub) }}</td>
                    </tr>
                @endforeach
            @else
                {{-- fallback si no hay productos: muestra la entrega --}}
                <tr>
                    <td>Entrega #{{ $entrega->id }}</td>
                    <td>1</td>
                    <td>{{ $fmt($factura->subtotal) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="totals" style="margin-top:10px">
        <tr>
            <td style="text-align:right; width:80%"><strong>Subtotal:&nbsp;</strong></td>
            <td style="width:20%">{{ $fmt($factura->subtotal) }}</td>
        </tr>
        <tr>
            <td style="text-align:right;"><strong>Total:&nbsp;</strong></td>
            <td>{{ $fmt($factura->total) }}</td>
        </tr>
    </table>

    <p class="muted" style="margin-top:18px">Gracias por su compra.</p>
</div>
</body>
</html>
