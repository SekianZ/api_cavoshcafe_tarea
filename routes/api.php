<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;


// POST /api/login
Route::post('/login', [ClienteController::class, 'getCliente']);

// POST /api/registrar
Route::post('/registrar', [ClienteController::class, 'setCliente']);

// POST /api/codigo
Route::post('/codigo', [ClienteController::class, 'getClienteCodigo']);

// POST /api/validar-codigo (opcional, pero útil)
Route::post('/validar-codigo', [ClienteController::class, 'validarCodigo']);

// Ruta protegida de ejemplo (requiere autenticación Sanctum)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
