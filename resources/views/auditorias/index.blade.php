@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">📜 Bitácora de Auditoría</h2>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded-md mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif

    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Usuario</th>
                <th class="px-4 py-2">Acción</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditorias as $auditoria)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $auditoria->id }}</td>
                <td class="px-4 py-2">{{ $auditoria->usuario->name ?? 'N/A' }}</td>
                <td class="px-4 py-2">{{ $auditoria->accion }}</td>
                <td class="px-4 py-2">{{ $auditoria->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-2">
                    <form action="{{ route('auditorias.destroy', $auditoria->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $auditorias->links() }}
    </div>
</div>
@endsection
