@extends('layouts.app')

@section('title', 'Entregas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Encabezado --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1 class="h4 mb-0">
                    <i class="bi bi-truck me-2"></i>Gestión de Entregas
                </h1>
                <a href="{{ route('entregas.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Entrega
                </a>
            </div>

            {{-- Mensajes de éxito --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Mensajes de error --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Tabla de entregas --}}
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>
                                        <i class="bi bi-person me-1"></i>Cliente
                                    </th>
                                    <th>
                                        <i class="bi bi-truck me-1"></i>Repartidor
                                    </th>
                                    <th>
                                        <i class="bi bi-calendar me-1"></i>Fecha/Hora
                                    </th>
                                    <th class="text-center">
                                        <i class="bi bi-flag me-1"></i>Estado
                                    </th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entregas as $entrega)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $entrega->id }}</td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle text-muted me-2"></i>
                                                <span>{{ optional($entrega->cliente)->nombre ?? '—' }}</span>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-badge text-muted me-2"></i>
                                                <span>{{ optional($entrega->repartidor)->name ?? 'Sin asignar' }}</span>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($entrega->fecha_hora)
                                                <div class="small">
                                                    <i class="bi bi-calendar-date me-1"></i>
                                                    {{ \Carbon\Carbon::parse($entrega->fecha_hora)->format('d/m/Y') }}
                                                    <br>
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($entrega->fecha_hora)->format('H:i') }}
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        
                                        <td class="text-center">
                                            @php
                                                $estado = $entrega->estado;
                                                $badgeClass = match($estado) {
                                                    'realizada' => 'success',
                                                    'cancelada' => 'danger',
                                                    default => 'warning'
                                                };
                                                $icon = match($estado) {
                                                    'realizada' => 'check-circle',
                                                    'cancelada' => 'x-circle',
                                                    default => 'clock'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }} text-uppercase">
                                                <i class="bi bi-{{ $icon }} me-1"></i>{{ $estado }}
                                            </span>
                                        </td>
                                        
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                {{-- Ver entrega --}}
                                                <a href="{{ route('entregas.show', $entrega) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                {{-- Editar entrega --}}
                                                <a href="{{ route('entregas.edit', $entrega) }}" 
                                                   class="btn btn-outline-warning btn-sm"
                                                   title="Editar entrega">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                {{-- Eliminar entrega --}}
                                                <form action="{{ route('entregas.destroy', $entrega) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirmarEliminacion('{{ $entrega->id }}', '{{ optional($entrega->cliente)->nombre }}')">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm" 
                                                            title="Eliminar entrega">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                            <h5>No hay entregas registradas</h5>
                                            <p class="mb-0">
                                                <a href="{{ route('entregas.create') }}" class="btn btn-primary">
                                                    <i class="bi bi-plus-lg me-1"></i>Crear primera entrega
                                                </a>
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Paginación --}}
                @if($entregas->hasPages())
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                Mostrando {{ $entregas->firstItem() }} a {{ $entregas->lastItem() }} 
                                de {{ $entregas->total() }} entregas
                            </div>
                            {{ $entregas->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- JavaScript para confirmaciones --}}
<script>
function confirmarEliminacion(entregaId, clienteNombre) {
    return confirm(`¿Estás seguro de eliminar la entrega #${entregaId}?\n\nCliente: ${clienteNombre || 'N/A'}\n\nEsta acción no se puede deshacer.`);
}

// Auto-ocultar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection