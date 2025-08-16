@extends('layouts.app')
@section('title','Editar Cliente')

@section('content')
  <h1 class="h3 mb-3">Editar Cliente</h1>

  <div class="card">
    <div class="card-body">
      <form action="{{ route('clientes.update', $cliente) }}" method="post" novalidate>
        @method('PUT')
        @include('clientes._form', ['cliente' => $cliente])
      </form>
    </div>
  </div>
@endsection
