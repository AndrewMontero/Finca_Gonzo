@extends('layouts.app')
@section('title','Entregas')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0">Entregas</h1>
  <a href="{{ route('entregas.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Crear Entrega
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="text-nowrap">#</th>
          <th class="text-nowrap">Cliente</th>
          <th class="text-nowrap">Repartidor</th>
          <th class="text-nowrap">Fecha / hora</th>
          <th class="text-nowrap">Estado</th>
          <th class="text-end text-nowrap">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($entregas as $entrega)
          <tr>
            <td>{{ $entrega->id }}</td>
            <td>{{ optional($entrega->cliente)->nombre ?? '—' }}</td>
            <td>{{ optional($entrega->repartidor)->name ?? 'Sin asignar' }}</td>
            <td>
              @if($entrega->fecha_hora)
                {{-- Si tienes cast en el modelo, ya es Carbon --}}
                {{ \Illuminate\Support\Carbon::parse($entrega->fecha_hora)->format('Y-m-d H:i') }}
              @else
                —
              @endif
            </td>
            <td>
              @php
                $estado = $entrega->estado;
                $badge  = match($estado){
                  'realizada' => 'success',
                  'cancelada' => 'danger',
                  default     => 'warning'
                };
              @endphp
              <span class="badge text-bg-{{ $badge }} text-uppercase">{{ $estado }}</span>
            </td>
            <td class="text-end">
              <a href="{{ route('entregas.show', $entrega) }}" class="btn btn-outline-secondary btn-sm">Ver</a>
              <a href="{{ route('entregas.edit', $entrega) }}" class="btn btn-warning btn-sm">Editar</a>
              <form action="{{ route('entregas.destroy', $entrega) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('¿Eliminar la entrega #{{ $entrega->id }}?');">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No hay entregas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer bg-white">
    {{ $entregas->links() }}
  </div>
</div>
@endsection
