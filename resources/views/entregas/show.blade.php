@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de Entrega</h2>
    <ul>
        <li><strong>Cliente:</strong> {{ $entrega->cliente->nombre }}</li>
        <li><strong>Repartidor:</strong> {{ $entrega->repartidor->name }}</li>
        <li><strong>Fecha:</strong> {{ $entrega->fecha_hora }}</li>
        <li><strong>Estado:</strong> {{ $entrega->estado }}</li>
    </ul>

    <h4>Productos entregados:</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entrega->productos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->pivot->cantidad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('entregas.index') }}" class="btn btn-primary">Volver</a>
</div>
@endsection
