@extends('layouts.app')
@section('title','Reportes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Reportes</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('reportes.ventas') }}" class="block bg-white p-4 rounded shadow hover:shadow-md">
            <h2 class="font-semibold">Ventas</h2>
            <p class="text-gray-500 text-sm">Rango de fechas, total y exportación CSV.</p>
        </a>
        <a href="{{ route('reportes.entregas') }}" class="block bg-white p-4 rounded shadow hover:shadow-md">
            <h2 class="font-semibold">Entregas</h2>
            <p class="text-gray-500 text-sm">Por estado y fechas.</p>
        </a>
        <a href="{{ route('reportes.productos') }}" class="block bg-white p-4 rounded shadow hover:shadow-md">
            <h2 class="font-semibold">Productos</h2>
            <p class="text-gray-500 text-sm">Bajo stock y top vendidos.</p>
        </a>
    </div>
</div>
@endsection
