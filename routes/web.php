<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PaginaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaginaController::class, 'inicio'])->name('inicio');
Route::get('/servicios', [PaginaController::class, 'servicios'])->name('servicios');
Route::get('/citas', [CitaController::class, 'create'])->name('citas.create');
Route::post('/citas', [CitaController::class, 'store'])->middleware('throttle:10,1')->name('citas.store');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::prefix('admin')->group(function () {
        Route::get('/', [PaginaController::class, 'dashboard'])->name('admin.dashboard');
        Route::resource('clientes', ClienteController::class)->except('show')->parameters(['clientes' => 'cliente']);
    });
});
// Continuidad con los enlaces públicos de las fases anteriores.
Route::redirect('/index.html', '/');
Route::redirect('/servicios.html', '/servicios');
Route::redirect('/citas.html', '/citas');
