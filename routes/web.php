<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DetalleEntregaController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rutas autenticadas (usuario logueado y verificado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reportes
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/ventas', [ReportesController::class, 'reporteVentas'])->name('reportes.ventas');
    Route::get('/reportes/ventas/csv', [ReportesController::class, 'exportVentasCsv'])->name('reportes.ventas.csv');
    Route::get('/reportes/entregas', [ReportesController::class, 'reporteEntregas'])->name('reportes.entregas');
    Route::get('/reportes/productos', [ReportesController::class, 'reporteProductos'])->name('reportes.productos');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clientes
    Route::resource('clientes', ClienteController::class);

    // Entregas y Detalle de entregas
    Route::resource('entregas', EntregaController::class);
    Route::resource('detalle-entregas', DetalleEntregaController::class);

    // Facturas
    Route::resource('facturas', FacturaController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/facturas/{entrega}/pdf', [FacturaController::class, 'generarFactura'])->name('facturas.pdf');

    // Rutas para productos
    Route::resource('productos', ProductoController::class);
    Route::get('productos-stock-bajo', [ProductoController::class, 'stockBajo'])->name('productos.stock-bajo');
    Route::get('api/productos', [ProductoController::class, 'api'])->name('productos.api');

    // Actualizar stock (versión normal y Ajax)
    Route::post('productos/{id}/actualizar-stock', [ProductoController::class, 'actualizarStock'])->name('productos.actualizar-stock');
    Route::post('productos/{id}/actualizar-stock-ajax', [ProductoController::class, 'actualizarStockAjax'])->name('productos.actualizar-stock.ajax');

    // NUEVAS RUTAS para los botones mejorados
    Route::post('/productos/{id}/update-stock', [ProductoController::class, 'updateStock'])->name('productos.update-stock');
    Route::post('/productos/{id}/duplicate', [ProductoController::class, 'duplicate'])->name('productos.duplicate');
});

/*
|--------------------------------------------------------------------------
| Rutas para ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('usuarios', UserController::class);
    Route::resource('auditorias', AuditoriaController::class)->only(['index', 'destroy']);
});

require __DIR__ . '/auth.php';
