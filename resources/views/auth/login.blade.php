@extends('layouts.app')
@section('title','Iniciar sesión')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 mb-4 text-center">Iniciar sesión</h1>

          @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
          @endif

          <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Correo</label>
              <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <div class="d-grid">
              <button class="btn btn-success">Entrar</button>
            </div>
          </form>

          <div class="text-center mt-3">
            ¿No tienes una cuenta?
            <a href="{{ route('register') }}" class="fw-semibold">Créala aquí</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
