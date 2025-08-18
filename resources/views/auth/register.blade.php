@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
<div class="container d-flex justify-content-center py-5">
  <div class="card shadow-sm" style="max-width: 480px; width: 100%;">
    <div class="card-body">
      <h1 class="h4 mb-3 text-center">Crear cuenta</h1>

      <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name') }}" required>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Correo</label>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email') }}" required>
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
          @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Confirmar contraseña</label>
          <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button class="btn btn-success w-100">Registrarme</button>
      </form>

      <div class="text-center mt-3">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
      </div>
    </div>
  </div>
</div>
@endsection
