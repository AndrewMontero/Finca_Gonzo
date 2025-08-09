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

    // Recursos principales
    Route::resource('clientes', ClienteController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('entregas', EntregaController::class);
    Route::resource('detalle-entregas', DetalleEntregaController::class);

    // Facturas (mínimo necesario ahora)
    Route::resource('facturas', FacturaController::class)->only(['index', 'create', 'store', 'show']);

    // Generar PDF de factura (si tu controlador lo implementa)
    Route::get('/facturas/{entrega}/pdf', [FacturaController::class, 'generarFactura'])
        ->name('facturas.pdf');
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

/*
|--------------------------------------------------------------------------
| (Opcional) Rutas para REPARTIDOR
| Nota: ya tienes resource('entregas') en las rutas autenticadas;
| si necesitas vistas específicas del rol repartidor, usa prefijo.
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth', 'role:repartidor'])->prefix('repartidor')->group(function () {
//     Route::get('/entregas', [EntregaController::class, 'index'])->name('repartidor.entregas.index');
// });

require __DIR__ . '/auth.php';
