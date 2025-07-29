@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">Clientes</h2>
    <a href="{{ route('clientes.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">+ Nuevo Cliente</a>
</div>

<form method="GET" class="mb-4 flex">
    <input type="text" name="search" placeholder="Buscar cliente..." 
           value="{{ request('search') }}"
           class="border rounded px-3 py-2 w-1/3 mr-2">
    <button class="bg-blue-500 text-white px-4 py-2 rounded">Buscar</button>
</form>

<table class="w-full bg-white shadow-md rounded">
    <thead>
        <tr class="bg-gray-200">
            <th class="px-4 py-2">Nombre</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Teléfono</th>
            <th class="px-4 py-2">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientes as $cliente)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $cliente->nombre }}</td>
            <td class="px-4 py-2">{{ $cliente->email }}</td>
            <td class="px-4 py-2">{{ $cliente->telefono }}</td>
            <td class="px-4 py-2 flex">
                <a href="{{ route('clientes.edit', $cliente) }}" class="bg-yellow-500 text-white px-3 py-1 rounded mr-2">Editar</a>
                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="bg-red-500 text-white px-3 py-1 rounded">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
