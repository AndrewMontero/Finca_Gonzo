@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear Entrega</h2>
    <form action="{{ route('entregas.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="cliente_id">Cliente</label>
            <select name="cliente_id" class="form-control" required>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="repartidor_id">Repartidor</label>
            <select name="repartidor_id" class="form-control" required>
                @foreach($repartidores as $rep)
                    <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="fecha_hora">Fecha y Hora</label>
            <input type="datetime-local" name="fecha_hora" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select name="estado" class="form-control" required>
                <option value="pendiente">Pendiente</option>
                <option value="realizada">Realizada</option>
                <option value="cancelada">Cancelada</option>
            </select>
        </div>

        <h4>Productos</h4>
        @foreach($productos as $producto)
            <div class="form-check">
                <input type="checkbox" name="productos[]" value="{{ $producto->id }}">
                <label>{{ $producto->nombre }} (Stock: {{ $producto->stock_actual }})</label>
                <input type="number" name="cantidades[]" class="form-control" placeholder="Cantidad" min="1">
            </div>
        @endforeach

        <button type="submit" class="btn btn-success mt-3">Guardar</button>
    </form>
</div>
@endsection
