<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Middleware\Authenticationmiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(Authenticationmiddleware::class);

Route::post('/search', [CashierController::class, 'search']);

Route::post('/ticket', [CashierController::class, 'generate_ticket'])->middleware(Authenticationmiddleware::class);
