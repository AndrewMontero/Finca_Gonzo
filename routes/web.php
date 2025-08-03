<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\DashboardController;

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

    // Generar facturas PDF
    Route::get('/facturas/{entrega}/pdf', [FacturaController::class, 'generarFactura'])
        ->name('facturas.pdf');
});

// ✅ Solo ADMIN puede gestionar usuarios y productos
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('usuarios', UserController::class);
    Route::resource('productos', ProductoController::class);
});

// ✅ Solo REPARTIDOR puede acceder a entregas
Route::middleware(['auth', 'role:repartidor'])->group(function () {
    Route::get('entregas', [EntregaController::class, 'index'])->name('entregas.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('auditorias', AuditoriaController::class)->only(['index', 'destroy']);
});


require __DIR__.'/auth.php';
