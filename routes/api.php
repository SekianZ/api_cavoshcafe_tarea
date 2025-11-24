<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;

Route::post('/login',     [ClienteController::class, 'getCliente']);
Route::post('/registrar', [ClienteController::class, 'setCliente']);
Route::post('/codigo',    [ClienteController::class, 'getClienteCodigo']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
