<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DetalleEntregaController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard principal con middleware
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 🔐 Rutas protegidas (requieren login)
Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Generar facturas PDF (disponible solo si está logueado)
    Route::get('/facturas/{entrega}/pdf', [FacturaController::class, 'generarFactura'])
        ->name('facturas.pdf');

    // Recursos accesibles para usuarios autenticados
    Route::resource('clientes', ClienteController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('entregas', EntregaController::class);
    Route::resource('detalle-entregas', DetalleEntregaController::class);
});

// ✅ Solo ADMIN puede gestionar usuarios y auditorías
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('usuarios', UserController::class);
    Route::resource('auditorias', AuditoriaController::class)->only(['index', 'destroy']);
});

// ✅ Solo REPARTIDOR puede acceder a entregas
Route::middleware(['auth', 'role:repartidor'])->group(function () {
    Route::get('entregas', [EntregaController::class, 'index'])->name('entregas.index');
});

Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');

require __DIR__.'/auth.php';
