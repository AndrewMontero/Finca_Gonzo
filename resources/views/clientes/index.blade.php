@extends('layouts.app')
@section('title','Clientes')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-3">
    <div>
        <h1 class="h3 mb-1">Clientes</h1>
        <small class="text-muted">Total: {{ $clientes->total() }}</small>
    </div>
    <a href="{{ route('clientes.create') }}" class="btn btn-success">
        <i class="bi bi-person-plus me-1"></i> Nuevo Cliente
    </a>
</div>

<form action="{{ route('clientes.index') }}" method="get" class="mb-3">
    <div class="input-group input-group-lg">
        <input type="text" name="q" value="{{ $q }}" class="form-control"
            placeholder="Buscar por nombre, email, teléfono o ubicación…">
        <button class="btn btn-outline-secondary" type="submit" title="Buscar">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>

@if($clientes->count())
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Ubicación</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $c)
                <tr>
                    <td class="fw-semibold">{{ $c->nombre }}</td>
                    <td>{{ $c->correo ?? '—' }}</td>
                    <td>{{ $c->telefono ?? '—' }}</td>
                    <td>{{ $c->ubicacion ?? '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('clientes.edit', $c) }}" class="btn btn-warning btn-sm">Editar</a>

                        <form action="{{ route('clientes.destroy', $c) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar cliente {{ $c->nombre }}?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $clientes->links() }}
    </div>
</div>
@else
<div class="text-center text-muted py-5">
    No hay clientes. Crea el primero.
</div>
@endif
@endsection

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush