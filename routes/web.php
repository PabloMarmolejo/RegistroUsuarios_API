<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;

Route::get('/api/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
Route::post('/api/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
Route::get('/api/usuarios/{id}', [UsuariosController::class, 'show'])->name('usuarios.show');
Route::put('/api/usuarios/{id}', [UsuariosController::class, 'update'])->name('usuarios.update');
Route::delete('/api/usuarios/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
