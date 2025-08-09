@extends('layouts.app')
@section('title','Reporte de Entregas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Reporte de Entregas</h1>

    <form class="bg-white p-4 rounded shadow mb-4" method="GET" action="{{ route('reportes.entregas') }}">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm text-gray-600">Estado</label>
                <select name="estado" class="border rounded px-3 py-2 w-full">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado')=='pendiente'?'selected':'' }}>Pendiente</option>
                    <option value="realizada" {{ request('estado')=='realizada'?'selected':'' }}>Realizada</option>
                    <option value="cancelada" {{ request('estado')=='cancelada'?'selected':'' }}>Cancelada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm text-gray-600">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="flex items-end gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Filtrar</button>
                <a class="bg-gray-200 px-4 py-2 rounded" href="{{ route('reportes.entregas') }}">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="bg-white p-4 rounded shadow">
            <div class="text-gray-500 text-sm">Pendientes</div>
            <div class="text-2xl font-semibold">{{ $kpis['pendientes'] }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-gray-500 text-sm">Realizadas</div>
            <div class="text-2xl font-semibold">{{ $kpis['realizadas'] }}</div>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Entrega</th>
                        <th class="py-2">Estado</th>
                        <th class="py-2">Cliente</th>
                        <th class="py-2">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entregas as $e)
                    <tr class="border-t">
                        <td class="py-2">{{ $e->id }}</td>
                        <td class="py-2">{{ $e->estado }}</td>
                        <td class="py-2">{{ $e->cliente_id ?? '-' }}</td>
                        <td class="py-2">{{ $e->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $entregas->links() }}</div>
    </div>
</div>
@endsection
