@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Entrega</h2>
    <form action="{{ route('entregas.update', $entrega->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="cliente_id">Cliente</label>
            <select name="cliente_id" class="form-control" required>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}"
                        {{ $entrega->cliente_id == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="repartidor_id">Repartidor</label>
            <select name="repartidor_id" class="form-control" required>
                @foreach($repartidores as $rep)
                    <option value="{{ $rep->id }}"
                        {{ $entrega->repartidor_id == $rep->id ? 'selected' : '' }}>
                        {{ $rep->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="fecha_hora">Fecha y Hora</label>
            <input type="datetime-local" name="fecha_hora" class="form-control"
                value="{{ \Carbon\Carbon::parse($entrega->fecha_hora)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select name="estado" class="form-control" required>
                <option value="pendiente" {{ $entrega->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="realizada" {{ $entrega->estado == 'realizada' ? 'selected' : '' }}>Realizada</option>
                <option value="cancelada" {{ $entrega->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>
        </div>

        <h4>Productos</h4>
        @foreach($productos as $producto)
            @php
                $cantidadExistente = $entrega->productos->find($producto->id)->pivot->cantidad ?? '';
            @endphp
            <div class="form-check">
                <input type="checkbox" name="productos[]" value="{{ $producto->id }}"
                    {{ $entrega->productos->contains($producto->id) ? 'checked' : '' }}>
                <label>{{ $producto->nombre }} (Stock: {{ $producto->stock_actual }})</label>
                <input type="number" name="cantidades[]" class="form-control"
                    placeholder="Cantidad" min="1" value="{{ $cantidadExistente }}">
            </div>
        @endforeach

        <button type="submit" class="btn btn-success mt-3">Actualizar</button>
    </form>
</div>
@endsection
