<?php

use App\Http\Controllers\AutentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::post('LoginNuevo', [AutentController::class, 'login'])->name('LoginNuevo');
Route::get('UsrAutoriz', [AutentController::class, 'UsrAutoriz'])->middleware('auth')->name('UsrAutoriz');
Route::post('CerrarSesion', [AutentController::class, 'logout'])->name('CerrarSesion');


