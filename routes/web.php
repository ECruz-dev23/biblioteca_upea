<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Ayudante;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\OpinionController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Route;

/* ================= CATÁLOGO PÚBLICO ================= */
Route::get('/', [CatalogoController::class, 'index'])->name('catalogo');
Route::get('catalogo/detalle/{id}', [CatalogoController::class, 'detalle'])->name('catalogo.detalle');

Route::resource('reservas', ReservaController::class)->only(['index', 'store']);
Route::resource('opiniones', OpinionController::class)->only(['create', 'store']);

/* ================= AUTENTICACIÓN ================= */
Route::get('auth/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('auth/login', [LoginController::class, 'login'])->name('login.post');
Route::post('auth/login-ajax', [LoginController::class, 'ajax'])->name('auth.login.ajax');
Route::post('auth/registro', [RegistroController::class, 'store'])->name('auth.registro');
Route::post('auth/logout', [LoginController::class, 'logout'])->name('logout');

/* ================= PANEL ADMINISTRADOR ================= */
Route::prefix('admin')->name('admin.')->middleware('rol:administrador')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('libros', Admin\LibroController::class)->except(['show']);
    Route::resource('usuarios', Admin\UsuarioController::class)->except(['show']);

    Route::resource('prestamos', PrestamoController::class)->only(['index', 'create', 'store']);
    Route::patch('prestamos/{prestamo}/devolver', [PrestamoController::class, 'devolver'])->name('prestamos.devolver');

    Route::get('reportes', [Admin\ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/pdf/{tipo}', [Admin\ReporteController::class, 'pdf'])->name('reportes.pdf');

    Route::get('opiniones/pdf', [Admin\OpinionController::class, 'pdf'])->name('opiniones.pdf');
    Route::resource('opiniones', Admin\OpinionController::class)->only(['index', 'store']);
});

/* ================= PANEL AYUDANTES (pasantes / becari@s) ================= */
    Route::prefix('ayudantes')->name('ayudantes.')->middleware('rol:pasante,becario')->group(function () {
    Route::get('/', [Ayudante\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('libros', Ayudante\LibroController::class)->only(['index']);

    Route::resource('usuarios', Ayudante\UsuarioController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('prestamos', PrestamoController::class)->only(['index', 'create', 'store']);
    Route::patch('prestamos/{prestamo}/devolver', [PrestamoController::class, 'devolver'])->name('prestamos.devolver');

    Route::resource('opiniones', Ayudante\OpinionController::class)->only(['create', 'store']);
});