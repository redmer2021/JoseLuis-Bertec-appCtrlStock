<?php

use App\Http\Controllers\AutentController;
use App\Http\Controllers\DescargasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::post('LoginNuevo', [AutentController::class, 'login'])->name('LoginNuevo');
Route::get('UsrAutoriz', [AutentController::class, 'UsrAutoriz'])->middleware('auth')->name('UsrAutoriz');
Route::post('CerrarSesion', [AutentController::class, 'logout'])->name('CerrarSesion');

Route::get('/exportar-revcompras', [DescargasController::class, 'exportarRevCompras'])
    ->name('exportar.revcompras');

Route::get('/exportar-revventas', [DescargasController::class, 'exportarRevVentas'])
    ->name('exportar.revventas');

Route::get('/exportar-revdepositos', [DescargasController::class, 'exportarRevDepositos'])
    ->name('exportar.revdepositos');

