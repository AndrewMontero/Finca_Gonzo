@extends('layouts.app')
@section('title', 'Usuarios')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0"><i class="bi bi-people me-2"></i>Usuarios</h1>

        <form method="GET" class="d-flex" role="search">
            <input class="form-control me-2" type="search" name="q" value="{{ $q }}" placeholder="Buscar nombre o correo">
            <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th class="text-center">Rol</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $i => $u)
                <tr>
                    <td>{{ ($users->firstItem() ?? 1) + $i }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td class="text-center">
                        {{-- ✅ Cambia rol y envía PATCH a admin.users.role --}}
                        <form method="POST" action="{{ route('admin.users.role', $u) }}" class="d-inline-block">
                            @csrf
                            @method('PATCH')
                            <select name="rol" class="form-select form-select-sm w-auto d-inline-block">
                                <option value="cliente"     @selected($u->rol==='cliente')>Cliente</option>
                                <option value="repartidor"  @selected($u->rol==='repartidor')>Repartidor</option>
                                <option value="admin"       @selected($u->rol==='admin')>Admin</option>
                            </select>
                            <button class="btn btn-sm btn-primary ms-2">Guardar</button>
                        </form>
                    </td>
                    <td class="text-end">
                        <span class="badge bg-secondary">{{ $u->created_at?->format('Y-m-d') }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No hay usuarios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
@endsection
