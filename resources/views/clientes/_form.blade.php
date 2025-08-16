@csrf
<div class="mb-3">
  <label class="form-label">Nombre *</label>
  <input type="text" name="nombre" class="form-control" required
         value="{{ old('nombre', $cliente->nombre) }}">
  @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input type="email" name="correo" class="form-control"
           value="{{ old('correo', $cliente->correo) }}">
    @error('correo') <small class="text-danger">{{ $message }}</small> @enderror
  </div>
  <div class="col-md-6">
    <label class="form-label">Teléfono</label>
    <input type="text" name="telefono" class="form-control"
           value="{{ old('telefono', $cliente->telefono) }}">
    @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
  </div>
</div>

<div class="mt-3">
  <label class="form-label">Ubicación</label>
  <input type="text" name="ubicacion" class="form-control"
         value="{{ old('ubicacion', $cliente->ubicacion) }}">
  @error('ubicacion') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mt-4 d-flex gap-2">
  <button class="btn btn-success">Guardar</button>
  <a href="{{ route('clientes.index', request()->only('q')) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
