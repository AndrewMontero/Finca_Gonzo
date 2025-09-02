<?php

use Illuminate\Support\Facades\Route;

// Controladores
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
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\TiendaController;
use App\Http\Middleware\RoleMiddleware; // ✅ Importa la clase del middleware de rol

// Auth (único)
use App\Http\Controllers\Auth\CustomAuthController;

// -----------------------------------------------------
// Público
// -----------------------------------------------------
Route::get('/', fn() => redirect()->route('login')); // ✅ redirección en vez de vista

// -----------------------------------------------------
// Invitados (login/registro)
// -----------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login',     [CustomAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [CustomAuthController::class, 'login'])->name('login.attempt');

    Route::get('/register',  [CustomAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomAuthController::class, 'register'])->name('register.store');
});

// -----------------------------------------------------
// Autenticados (cualquier rol)
// -----------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clientes', ClienteController::class);

    Route::resource('entregas', EntregaController::class);
    Route::resource('detalle-entregas', DetalleEntregaController::class);

    Route::resource('facturas', FacturaController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('facturas/{factura}/print', [FacturaController::class, 'print'])->name('facturas.print');
    Route::post('facturas/{factura}/email', [FacturaController::class, 'email'])->name('facturas.email');
    Route::delete('facturas/{factura}',   [FacturaController::class, 'destroy'])->name('facturas.destroy');

    Route::get('/facturas/{entrega}/pdf', [FacturaController::class, 'generarFactura'])->name('facturas.pdf');

    Route::get('/reportes',            [ReportesController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/ventas',     [ReportesController::class, 'reporteVentas'])->name('reportes.ventas');
    Route::get('/reportes/ventas/csv', [ReportesController::class, 'exportVentasCsv'])->name('reportes.ventas.csv');
    Route::get('/reportes/entregas',   [ReportesController::class, 'reporteEntregas'])->name('reportes.entregas');
    Route::get('/reportes/productos',  [ReportesController::class, 'reporteProductos'])->name('reportes.productos');

    Route::resource('productos', ProductoController::class);
    Route::get('productos-stock-bajo', [ProductoController::class, 'stockBajo'])->name('productos.stock-bajo');
    Route::get('api/productos',        [ProductoController::class, 'api'])->name('productos.api');
    Route::post('productos/{id}/actualizar-stock',      [ProductoController::class, 'actualizarStock'])->name('productos.actualizar-stock');
    Route::post('productos/{id}/actualizar-stock-ajax', [ProductoController::class, 'actualizarStockAjax'])->name('productos.actualizar-stock.ajax');
    Route::post('productos/{id}/update-stock',          [ProductoController::class, 'updateStock'])->name('productos.update-stock');
    Route::post('productos/{id}/duplicate',             [ProductoController::class, 'duplicate'])->name('productos.duplicate');
});

// -----------------------------------------------------
// Admin (rol: admin)
// -----------------------------------------------------
Route::middleware(['auth', RoleMiddleware::class . ':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users',               [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');

        Route::resource('auditorias', AuditoriaController::class)->only(['index', 'destroy']);

        Route::post('/reseed/facturas', [MaintenanceController::class, 'reseedFacturas'])->name('reseed.facturas');
        Route::post('/reseed/entregas', [MaintenanceController::class, 'reseedEntregas'])->name('reseed.entregas');
    });

// -----------------------------------------------------
// Cambio de estado de entregas
// -----------------------------------------------------
Route::middleware(['auth', 'verified'])
    ->patch('entregas/{entrega}/estado', [EntregaController::class, 'updateEstado'])
    ->name('entregas.estado');

// -----------------------------------------------------
// TIENDA (rol: cliente)
// -----------------------------------------------------
Route::middleware(['auth', 'verified', RoleMiddleware::class . ':cliente'])->group(function () {
    Route::get('/tienda',                     [TiendaController::class, 'catalogo'])->name('tienda.index');
    Route::post('/tienda/agregar/{producto}', [TiendaController::class, 'agregar'])->name('tienda.agregar');

    Route::get('/carrito',                    [TiendaController::class, 'carrito'])->name('tienda.carrito');
    Route::post('/carrito/actualizar',        [TiendaController::class, 'actualizar'])->name('tienda.carrito.actualizar');
    Route::post('/carrito/vaciar',            [TiendaController::class, 'vaciarCarrito'])->name('tienda.carrito.vaciar');
    Route::post('/carrito/finalizar',         [TiendaController::class, 'finalizar'])->name('tienda.finalizar');

    Route::get('/mis-pedidos',                [TiendaController::class, 'pedidos'])->name('tienda.pedidos');
});
