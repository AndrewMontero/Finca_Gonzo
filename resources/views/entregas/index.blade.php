@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Listado de Entregas</h2>
    <a href="{{ route('entregas.create') }}" class="btn btn-primary mb-3">Crear Entrega</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Repartidor</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entregas as $entrega)
                <tr>
                    <td>{{ $entrega->id }}</td>
                    <td>{{ $entrega->cliente->nombre }}</td>
                    <td>{{ $entrega->repartidor->name }}</td>
                    <td>{{ $entrega->fecha_hora }}</td>
                    <td>{{ $entrega->estado }}</td>
                    <td>
                        <a href="{{ route('entregas.show', $entrega->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('entregas.edit', $entrega->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('entregas.destroy', $entrega->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $entregas->links() }}
</div>
@endsection
