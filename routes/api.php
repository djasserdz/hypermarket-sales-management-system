<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\Authenticationmiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/zbi', function () {
    return 'ya no9sh';
})->middleware(Authenticationmiddleware::class);
