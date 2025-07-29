@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')
<h2 class="text-2xl font-bold mb-4">Agregar Cliente</h2>

<form action="{{ route('clientes.store') }}" method="POST" class="bg-white p-6 rounded shadow-md">
    @csrf
    <div class="mb-4">
        <label class="block font-medium">Nombre</label>
        <input type="text" name="nombre" class="border rounded w-full px-3 py-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium">Email</label>
        <input type="email" name="email" class="border rounded w-full px-3 py-2">
    </div>

    <div class="mb-4">
        <label class="block font-medium">Teléfono</label>
        <input type="text" name="telefono" class="border rounded w-full px-3 py-2">
    </div>

    <button class="bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
</form>
@endsection
