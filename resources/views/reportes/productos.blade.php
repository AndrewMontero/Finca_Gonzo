@extends('layouts.app')
@section('title','Reporte de Productos')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Reporte de Productos</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold mb-4">Bajo Stock</h2>
            @if($bajo->isEmpty())
                <p class="text-gray-500">Sin alertas de stock.</p>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Producto</th>
                        <th class="py-2">Stock</th>
                        <th class="py-2">Mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bajo as $p)
                    <tr class="border-t">
                        <td class="py-2">{{ $p->nombre }}</td>
                        <td class="py-2">{{ $p->stock_actual }}</td>
                        <td class="py-2">{{ $p->stock_minimo }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold mb-4">Top Vendidos</h2>
            @if($top->isEmpty())
                <p class="text-gray-500">Sin datos.</p>
            @else
            <ol class="list-decimal pl-5">
                @foreach($top as $p)
                    <li class="mb-1">
                        <span class="font-medium">{{ $p->nombre }}</span>
                        <span class="text-gray-500">— {{ $p->entregas_count }} entregas</span>
                    </li>
                @endforeach
            </ol>
            @endif
        </div>
    </div>
</div>
@endsection
