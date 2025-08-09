@extends('layouts.app')
@section('title','Reporte de Ventas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Reporte de Ventas</h1>

    <form class="bg-white p-4 rounded shadow mb-4" method="GET" action="{{ route('reportes.ventas') }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                <a class="bg-gray-200 px-4 py-2 rounded" href="{{ route('reportes.ventas') }}">Limpiar</a>
            </div>
            <div class="flex items-end">
                <a class="bg-green-600 text-white px-4 py-2 rounded"
                   href="{{ route('reportes.ventas.csv', request()->only('desde','hasta')) }}">
                    Exportar CSV
                </a>
            </div>
        </div>
    </form>

    <div class="bg-white p-4 rounded shadow mb-4">
        <div class="text-gray-500 text-sm">Total del período</div>
        <div class="text-2xl font-semibold">₡{{ number_format($total, 2) }}</div>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Factura</th>
                        <th class="py-2">Cliente</th>
                        <th class="py-2">Total</th>
                        <th class="py-2">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $v)
                    <tr class="border-t">
                        <td class="py-2">{{ $v->id }}</td>
                        <td class="py-2">{{ $v->cliente_id }}</td>
                        <td class="py-2">₡{{ number_format($v->total,2) }}</td>
                        <td class="py-2">{{ $v->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $ventas->links() }}</div>
    </div>
</div>
@endsection
